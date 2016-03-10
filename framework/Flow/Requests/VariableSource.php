<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * VariableSource.php
 * A "source" for external parameters with filtering options.
 */

namespace SmoothPHP\Framework\Flow\Requests;

class VariableSource {
    private $source;
    private $filter;
    
    public function __construct(array $source) {
        $this->source = $source;
        $this->filter = FILTER_DEFAULT;
    }
    
    public function get($varName, $filter = FILTER_DEFAULT) {
        if ($filter == FILTER_DEFAULT)
            $filter = $this->filter;

        if (!isset($this->source[$varName]))
            return $filter == FILTER_VALIDATE_BOOLEAN ? null : false;
        
        $value = trim($this->source[$varName]);
        $options = array(
            'flags' => 0
        );
        switch($filter) {
            case FILTER_VALIDATE_INT:
                $options['flags'] |= FILTER_FLAG_ALLOW_HEX | FILTER_FLAG_ALLOW_OCTAL;
                break;
            case FILTER_VALIDATE_FLOAT:
                $options['flags'] |= FILTER_FLAG_ALLOW_SCIENTIFIC | FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
                break;
            case FILTER_VALIDATE_BOOLEAN:
                $options['flags'] |= FILTER_NULL_ON_FAILURE;
                break;
            default:
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                $options['flags'] |= FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;
        }

        return filter_var($value, $filter, $options);
    }
    
    public function __get($varName) {
        $filter = FILTER_DEFAULT;
        switch($varName) {
            case "int":
                $filter = FILTER_VALIDATE_INT;
                break;
            case "float":
                $filter = FILTER_VALIDATE_FLOAT;
                break;
            case "boolean":
                $filter = FILTER_VALIDATE_BOOLEAN;
                break;
            case "email":
                $filter = FILTER_VALIDATE_EMAIL;
                break;
            case "url":
                $filter = FILTER_VALIDATE_URL;
                break;
        }
        
        if ($filter != FILTER_DEFAULT) {
            $filteredSource = new VariableSource($this->source);
            $filteredSource->filter = $filter;
            return $filteredSource;
        }
        
        return $this->get($varName, $filter);
    }
    
}