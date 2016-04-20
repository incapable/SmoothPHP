<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MappedMySQLObject.php
 * Description
 */

namespace SmoothPHP\Framework\Database\Mapper;

use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Database\MySQLException;

class MySQLObjectMapper {
    private $mysql;

    private $classDef;
    private $fields;

    private $fetch, $insert;

    public function __construct(MySQL $mysql, $clazz) {
        $this->mysql = $mysql;

        $this->classDef = new \ReflectionClass($clazz);

        if (!$this->classDef->isSubclassOf(MappedMySQLObject::class))
            throw new MySQLException("Attempting to map an object that is not a subclass of MappedMySQLObject");

        $this->fields = array_map(function (\ReflectionProperty $field) {
            $field->setAccessible(true);
            return $field;
        }, $this->classDef->getProperties());
    }

    public function createStructure() {

    }

    /**
     * @param int $id
     * @return MappedMySQLObject
     */
    public function fetch($id) {
        $target = $this->classDef->newInstanceWithoutConstructor();

        if (!isset($this->fetch)) {
            $query = 'SELECT ';

            $this->fetch = new PreparedMapStatement();

            $query .= implode(', ', array_map(function (\ReflectionProperty $field) {
                $this->fetch->params[$field->getName()] = null;
                $this->fetch->references[] = &$this->fetch->params[$field->getName()];
                return '`' . $field->getName() . '`';
            }, $this->fields));

            $query .= ' FROM `' . $target->getTableName() . '` WHERE `id` = %d';

            $this->fetch->statement = $this->mysql->prepare($query, false);
            call_user_func_array(array($this->fetch->statement->getStatement(), 'bind_result'), $this->fetch->references);
            MySQL::checkError($this->fetch->statement->getStatement());
        }

        $this->fetch->statement->execute($id);
        $this->fetch->statement->getStatement()->fetch();
        $this->fetch->statement->getStatement()->reset();

        /* @var $field \ReflectionProperty */
        foreach ($this->fields as $field) {
            $field->setValue($target, $this->fetch->params[$field->getName()]);
        }

        return $target;
    }

    public function insert(MappedMySQLObject $object) {
        if (!isset($this->insert)) {
            $query = 'INSERT INTO `' . $object->getTableName() . '` (';

            $insertParams = array();
            $this->insert = new PreparedMapStatement();

            $first = true;
            array_map(function (\ReflectionProperty $field) use (&$object, &$query, &$insertParams, &$first) {
                if (!$first)
                    $query .= ', ';
                else
                    $first = false;
                $query .= '' . $field->getName() . '';

                $value = $field->getValue($object);
                if (is_int($value))
                    $insertKey = '%d';
                else if (is_float($value))
                    $insertKey = '%f';
                else
                    $insertKey = '%s';

                $insertParams[] = $insertKey;
                if ($field->getName() != 'id')
                    $this->insert->params[$field->getName()] = '`' . $field->getName() . '` = ' . $insertKey;
            }, $this->fields);

            $query .= ') VALUES (' . implode(', ', $insertParams) . ') ON DUPLICATE KEY UPDATE ' . implode(', ', array_values($this->insert->params));
            $this->insert->statement = $this->mysql->prepare($query, false);
        }

        $params = array();
        for ($i = 0; $i < 2; $i++)
            foreach ($this->fields as $field)
                if ($i == 0 || $field->getName() != 'id')
                    $params[] = $field->getValue($object);

        call_user_func_array(array($this->insert->statement, 'execute'), $params);
    }

}