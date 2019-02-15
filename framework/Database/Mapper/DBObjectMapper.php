<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * DBObjectMapperp
 */

namespace SmoothPHP\Framework\Database\Mapper;

use SmoothPHP\Framework\Database\Database;
use SmoothPHP\Framework\Database\DatabaseException;

define('MYSQL_NO_LIMIT', -1);

class DBObjectMapper {
	private $mysql;

	private $className, $classDef;
	private $fields;

	private $fetch, $insert, $delete;

	public function __construct(Database $mysql, $clazz) {
		$this->mysql = $mysql;

		$this->className = $clazz;
		$this->classDef = new \ReflectionClass($clazz);

		if (!$this->classDef->isSubclassOf(MappedDBObject::class))
			throw new DatabaseException("Attempting to map an object that is not a subclass of MappedDBObject");

		$this->fields = array_filter(array_map(function (\ReflectionProperty $field) {
			if ($field->isStatic())
				return null;
			$field->setAccessible(true);
			return $field;
		}, $this->classDef->getProperties()), function ($value) {
			return $value != null;
		});

		// Prepare queries
		{
			$object = $this->classDef->newInstanceWithoutConstructor();

			// Set up fetch query
			{
				$query = 'SELECT ';

				$this->fetch = new PreparedMapStatement();

				$query .= implode(', ', array_map(function (\ReflectionProperty $field) {
					$this->fetch->references[] = null;
					$this->fetch->params[$field->getName()] = &$this->fetch->references[count($this->fetch->references) - 1];
					return '`' . $field->getName() . '`';
				}, $this->fields));

				$query .= ' FROM `' . $object->getTableName() . '` WHERE `id` = %d';

				$this->fetch->statement = $this->mysql->prepareCustom($query);
			}

			// Set up insert/update query
			{
				$query = 'INSERT INTO `' . $object->getTableName() . '` (';

				$insertParams = [];
				$this->insert = new PreparedMapStatement();

				$first = true;
				array_map(function (\ReflectionProperty $field) use (&$object, &$query, &$insertParams, &$first) {
					if (!$first)
						$query .= ', ';
					else
						$first = false;
					$query .= '`' . $field->getName() . '`';

					$value = $field->getValue($object);
					if (is_int($value))
						$insertKey = '%d';
					else if (is_float($value))
						$insertKey = '%f';
					else
						$insertKey = '%s';

					$insertParams[] = $insertKey;
					if ($field->getName() != 'id')
						$this->insert->params[$field->getName()] = '`' . $field->getName() . '` = VALUES(`' . $field->getName() . '`)';
				}, $this->fields);

				$query .= ') VALUES (' . implode(', ', $insertParams) . ') ON DUPLICATE KEY UPDATE ' . implode(', ', array_values($this->insert->params));
				$this->insert->statement = $this->mysql->prepare($query, false);
			}

			// Set up delete query
			{
				$query = 'DELETE FROM `' . $object->getTableName() . '` WHERE `id` = %d';
				$this->delete = $this->mysql->prepare($query, false);
			}
		}
	}

	public function __wakeup() {
		$this->classDef = new \ReflectionClass($this->className);
		$this->fields = array_filter(array_map(function (\ReflectionProperty $field) {
			if ($field->isStatic())
				return null;
			$field->setAccessible(true);
			return $field;
		}, $this->classDef->getProperties()), function ($value) {
			return $value != null;
		});
	}

	/**
	 * @param int|array|string $where Either an object ID, or an array of properties, or a string
	 * @param int $limit
	 * @return array|MappedDBObject
	 */
	public function fetch($where, $limit = 1) {
		$prepared = null;
		if (is_int($where) || ctype_digit($where)) {
			$prepared = $this->fetch;
			$prepared->statement->execute((int)$where);
			call_user_func_array([$prepared->statement->getMySQLi_stmt(), 'bind_result'], $prepared->references);
		} else {
			$query = 'SELECT ';

			$prepared = new PreparedMapStatement();

			$query .= implode(', ', array_map(function (\ReflectionProperty $field) use (&$prepared) {
				$prepared->references[] = null;
				$prepared->params[$field->getName()] = &$prepared->references[count($prepared->references) - 1];
				return '`' . $field->getName() . '`';
			}, $this->fields));

			$query .= ' FROM `' . $this->classDef->newInstanceWithoutConstructor()->getTableName() . '` WHERE ';

			if (is_array($where)) {
				$whereSeparator = 'AND';
				if (isset($where['_separator'])) {
					$whereSeparator = $where['_separator'];
					unset($where['_separator']);
				}

				$conditions = [];
				array_walk($where, function (&$value, $key) use (&$conditions) {
					if ($key[0] == '_')
						return;

					$comparator = '=';
					if (strtolower(substr($key, 0, 5)) == 'like ') {
						$comparator = 'LIKE';
						$key = substr($key, 5);
					}
					$valueType = is_int($value) || ctype_digit($value) ? '%d' : '%s';
					$conditions[] = sprintf('`%s` %s %s', $key, $comparator, $valueType);
				});
				$query .= implode(' ' . $whereSeparator . ' ', $conditions);

				if (isset($where['_order'])) {
					$query .= ' ORDER BY ' . $where['_order'];
				}
			} else
				$query .= $where;

			if ($limit != MYSQL_NO_LIMIT)
				$query .= ' LIMIT ' . $limit;

			$prepared->statement = $this->mysql->prepareCustom($query);

			if (is_array($where)) {
				call_user_func_array([$prepared->statement, 'execute'], array_values($where));
			} else
				$prepared->statement->execute();

			call_user_func_array([$prepared->statement->getMySQLi_stmt(), 'bind_result'], $prepared->references);
			Database::checkError($prepared->statement->getMySQLi_stmt());
		}

		$stmt = $prepared->statement->getMySQLi_stmt();
		$stmt->store_result();

		$results = [];
		while ($stmt->fetch()) {
			$target = $this->classDef->newInstanceWithoutConstructor();
			/* @var $field \ReflectionProperty */
			foreach ($this->fields as $field) {
				$field->setValue($target, $prepared->params[$field->getName()]);
			}
			$results[] = $target;
		}

		$stmt->free_result();
		$stmt->reset();
		Database::checkError($stmt);

		if ($limit == 1) {
			if (count($results) == 0)
				return null;
			else
				return current($results);
		}

		return $results;
	}

	public function insert(MappedDBObject $object) {
		$params = [];
		/* @var $field \ReflectionProperty */
		$idField = null;
		foreach ($this->fields as $field) {
			if ($field->getName() == 'id')
				$idField = $field;
			$params[] = $field->getValue($object);
		}

		call_user_func_array([$this->insert->statement, 'execute'], $params);
		Database::checkError($this->insert->statement->getMySQLi_stmt());
		$id = $this->insert->statement->getMySQLi_stmt()->insert_id;
		if ($id != 0)
			$idField->setValue($object, $id);
	}

	/**
	 * @param MappedDBObject|int $object
	 */
	public function delete($object) {
		if ($object instanceof MappedDBObject)
			$id = $object->getId();
		else
			$id = $object;
		$this->delete->execute($id);
	}

}