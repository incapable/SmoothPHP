<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FunctionOperatorElement.php
 * Element that calls a function.
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class FunctionOperatorElement extends Element {
    private static $cacheableFunctions;

    private $functionName;
    private $args;

    public function __construct($functionName, Chain $args) {
        if (!isset(self::$cacheableFunctions))
            self::fillCacheableFunctions();

        $this->functionName = $functionName;
        $this->args = $args;
    }

    public function optimize(CompilerState $tpl) {
        $simpleArgs = true;
        $args = $this->args->getAll();
        $optimizedChain = new Chain();
        $resolvedArgs = array();

        for ($i = 0; $i < count($args); $i++) {
            $args[$i] = $args[$i]->optimize($tpl);
            $optimizedChain->addElement($args[$i]);

            if (!($args[$i] instanceof PrimitiveElement))
                $simpleArgs = false;
            else
                $primitiveArgs[] = $args[$i]->getValue();
        }

        $this->args = $optimizedChain;

        if (in_array($this->functionName, self::$cacheableFunctions) && $simpleArgs) {
            return new PrimitiveElement(call_user_func_array($this->functionName, $primitiveArgs));
        } else
            return $this;
    }

    public function writePHPInChain(PHPBuilder $php, $isChainPiece = false) {
        $php->openPHP();
        $php->append($this->functionName);
        $php->append('(');

        $args = $this->args->getAll();
        $last = end($args);
        array_map(function (Element $arg) use ($php, $last) {
            $arg->writePHP($php);
            if ($arg != $last)
                $php->append(',');
        }, $args);

        $php->append(')');
        if ($isChainPiece)
            $php->append(';');
    }

    private static function fillCacheableFunctions() {
        self::$cacheableFunctions = array(
            /* Math functions */ 'abs', 'acos', 'acosh', 'asin', 'asinh', 'atan2', 'atan', 'atanh', 'base_convert', 'ceil', 'cos', 'cosh', 'dechex', 'decoct', 'deg2rad', 'exp', 'expm1', 'floor', 'fmod', 'getrandmax', 'hexdec', 'hypot', 'intdiv', 'is_finite', 'is_infinite', 'is_nan', 'lcg_value', 'log10', 'log1p', 'log', 'max', 'min', 'mt_getrandmax', 'octdec', 'pi', 'pow', 'rad2deg', 'round', 'sin', 'sinh', 'sqrt', 'tan', 'tanh',
            /* String functions */ 'addcslashes', 'addslashes', 'chop', 'chr', 'chunk_split', 'convert_cyr_string', 'convert_uudecode', 'convert_uuencode', 'count_chars', 'crc32', 'explode', 'hebrev', 'hebrevc', 'html_entity_decode', 'htmlentities', 'htmlspecialchars_decode', 'htmlspecialchars', 'implode', 'join', 'lcfirst', 'levenshtein', 'localeconv', 'ltrim', 'md5', 'metaphone', 'money_format', 'nl2br', 'number_format', 'ord', 'parse_str', 'print', 'printf', 'quoted_printable_decode', 'quoted_printable_encode', 'quotemeta', 'rtrim', 'sha1', 'similar_text', 'soundex', 'sprintf', 'sscanf', 'str_getcsv', 'str_ireplacce', 'str_pad', 'str_repeat', 'str_replace', 'str_rot13', 'str_shuffle', 'str_word_count', 'strcasecmp', 'strchr', 'strcmp', 'strcoll', 'strcspn', 'strip_tags', 'stripcslashes', 'stripos', 'stripslashes', 'stristr', 'strlen', 'strnatcasecmp', 'strnatcmp', 'strncasecmp', 'strncmp', 'strpbrk', 'strpos', 'strrchr', 'strrev', 'strripos', 'strrpos', 'strspn', 'strstr', 'strtok', 'strtolower', 'strtoupper', 'strtr', 'substr_compare', 'substr_count', 'subtr_replace', 'substr', 'trim', 'ucfirst', 'ucwords', 'vsprintf', 'wordwrap',
            /* Variable functions */ 'boolval', 'doubleval', 'empty', 'floatval', 'gettype', 'intval', 'is_array', 'is_bool', 'is_callable', 'is_double', 'is_float', 'is_int', 'is_integer', 'is_long', 'is_null', 'is_numeric', 'is_object', 'is_real', 'is_resource', 'is_scalar', 'is_string', 'isset', 'print_r', 'strval', 'var_dump'
        );
    }
}
