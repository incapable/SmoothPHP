<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateCompiler.php
 * Template compiler, builds the first level of cache.
 */

namespace SmoothPHP\Framework\Templates;

use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Commands\BlockElement;
use SmoothPHP\Framework\Templates\Elements\Operators\ArithmeticOperatorElement;
use SmoothPHP\Framework\Templates\Elements\Operators\DereferenceOperatorElement;

class TemplateCompiler {
    const DELIMITER_START = '{';
    const DELIMITER_END = '}';

    private $commands, $operators;

    public function __construct() {
        // All these commands and operators will have the following method called:
        // static handle(TemplateCompiler, TemplateLexer $command, TemplateLexer $lexer, Chain, $stackEnd);
        $this->commands = array(
            'include' => Elements\Commands\IncludeElement::class,
            'assign' => Elements\Commands\AssignElement::class,
            'block' => BlockElement::class,
            'if' => Elements\Commands\IfElement::class,
            'while' => Elements\Commands\WhileElement::class
        );
        $this->operators = array(
            '+' => ArithmeticOperatorElement::class,
            '-' => ArithmeticOperatorElement::class,
            '*' => ArithmeticOperatorElement::class,
            '/' => ArithmeticOperatorElement::class,
            '\'' => Elements\PrimitiveElement::class,
            '"' => Elements\PrimitiveElement::class,
            '=' => Elements\Operators\EqualsOperatorElement::class,
            '!' => Elements\Operators\InEqualsOperatorElement::class,
            '|' => Elements\Operators\FunctionOperatorElement::class
        );
    }

    public function compile($file) {
        $lexer = new TemplateLexer(file_get_contents($file));

        $chain = new Chain();
        $this->read($lexer, $chain);

        $tpl = new Compiler\CompilerState();
        $chain = $chain->optimize($tpl);
        $tpl->finishing = true;
        $chain = $chain->optimize($tpl);

        return $chain;
    }

    public function read(TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $rawString = '';

        $finishString = function () use (&$chain, &$rawString) {
            if (strlen(trim($rawString)) > 0)
                $chain->addElement(new Elements\PrimitiveElement($rawString));
            $rawString = '';
        };

        while (true) {
            if ($stackEnd !== null && $lexer->peek($stackEnd)) {
                $finishString();
                return;
            } else if ($lexer->peek(self::DELIMITER_START)) {
                if ($lexer->isWhitespace()) {
                    $rawString .= self::DELIMITER_START;
                    continue;
                }

                $command = '';
                while (!$lexer->peek(self::DELIMITER_END)) {
                    $char = $lexer->next();
                    if ($char !== false)
                        $command .= $char;
                    else
                        throw new TemplateCompileException("Template syntax error, unfinished command: " . self::DELIMITER_START . $command);
                }

                $finishString();

                $this->handleCommand(new TemplateLexer($command), $lexer, $chain);
            } else {
                $char = $lexer->next();
                if ($char !== false)
                    $rawString .= $char;
                else {
                    $finishString();
                    return;
                }
            }
        }
    }

    public function handleCommand(TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $command->skipWhitespace();

        if ($stackEnd != null && $command->peek($stackEnd)) {
            return;
        } else if ($command->peek('(')) {
            $elements = new Chain();
            $this->handleCommand($command, $lexer, $elements, ')');
            $chain->addElement(new Elements\ParenthesisElement(self::flatten($elements)));
        } else if ($command->peek('$')) {
            $chain->addElement(new Elements\Commands\VariableElement($command->readAlphaNumeric()));
        } else {
            $next = $command->readAlphaNumeric();

            if (isset($this->commands[$next]))
                call_user_func(array($this->commands[$next], 'handle'), $this, $command, $lexer, $chain, $stackEnd);
            else if (strlen(trim($next)) > 0) {
                $chain->addElement(new Elements\PrimitiveElement($next, true));
                if ($command->peek('(')) {
                    // Function call
                    $name = $chain->pop();
                    if (!($name instanceof Elements\PrimitiveElement))
                        throw new TemplateCompileException("Attempting to call a function without a name.");

                    $deref = false;
                    if ($chain->previous() instanceof DereferenceOperatorElement)
                        $deref = $chain->previous();

                    $args = new Chain();
                    do {
                        $this->handleCommand($command, $lexer, $args, ')');
                    } while ($command->skipWhitespace() || $command->peek(','));

                    $function = new Elements\Operators\FunctionOperatorElement($name->getValue(), $args);
                    if ($deref !== false)
                        $deref->setRight($function);
                    else
                        $chain->addElement($function);
                } else if ($chain->previous(2) instanceof DereferenceOperatorElement) {
                    $chain->previous(2)->setRight($chain->pop());
                }
            } else {
                $command->skipWhitespace();
                if (isset($this->operators[$command->peekSingle()]))
                    call_user_func(array($this->operators[$command->peekSingle()], 'handle'), $this, $command, $lexer, $chain, $stackEnd);
                else if (strlen($command->peekSingle()) > 0)
                    throw new TemplateCompileException("Unknown operator '" . $command->peekSingle() . "'");
            }
        }

        $command->skipWhitespace();
        if ($command->peekSingle() && $command->peekSingle() != ',') // Do we have more to read?
            $this->handleCommand($command, $lexer, $chain, $stackEnd);
        return;
    }

    public static function flatten($chain) {
        if ($chain instanceof Chain) {
            $all = $chain->getAll();
            if (count($all) == 1)
                return self::flatten(current($all));
        }

        return $chain;
    }

}
