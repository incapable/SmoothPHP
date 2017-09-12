<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * JSElement.php
 * A template block that is later replaced by all used JavaScript files.
 */

namespace SmoothPHP\Framework\Cache\Assets\Template;

use JShrink\Minifier;
use SmoothPHP\Framework\Core\Lock;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class JSElement extends Element {
	const FORMAT = '<script type="text/javascript" src="%s"%s></script>';
	const COMPILED_PATH = __ROOT__ . 'cache/js/compiled.%s.js';

	private $mode;

	public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
		$args = new Chain();
		$compiler->handleCommand($command, $lexer, $args);
		$args = $args->getAll();

		$mode = '';
		if (count($args) >= 1 && $args[0] instanceof PrimitiveElement)
			$mode = ' ' . $args[0]->getValue();

		if (__ENV__ == 'dev')
			$chain->addElement(new DebugJSElement());
		else
			$chain->addElement(new self($mode));
	}

	public function __construct($mode) {
		$this->mode = $mode;
	}

	public function optimize(CompilerState $tpl) {
		return $this;
	}

	public function output(CompilerState $tpl) {
		/* @var $assetsRegister \SmoothPHP\Framework\Cache\Assets\AssetsRegister */
		$assetsRegister = $tpl->vars->assets->getValue();

		$files = [];

		foreach (array_unique($assetsRegister->getJSFiles()) as $js) {
			if (strtolower(substr($js, 0, 4)) == 'http') {
				echo sprintf(self::FORMAT, $js, '');
				continue;
			}

			$files[] = $assetsRegister->getJSPath($js);
		}

		if (count($files) == 0)
			return;

		$hash = md5(array_reduce($files, function ($carry, $file) {
			return $carry . ',' . $file . filemtime($file);
		}));

		if (!file_exists(sprintf(self::COMPILED_PATH, $hash))) {
			$lock = new Lock('compiled.js.' . $hash);

			if ($lock->lock()) {
				$contents = '';
				array_walk($files, function ($file) use ($assetsRegister, &$contents) {
					$contents .= '; ' . file_get_contents($file);
				});

				$optimized = Minifier::minify($contents);

				$path = sprintf(self::COMPILED_PATH, $hash);
				file_put_contents($path, $optimized);
				file_put_contents($path . '.gz', gzencode($optimized, 9));
			}
		}

		global $kernel;
		$path = $kernel->getRouteDatabase()->buildPath('assets_js_compiled', $hash);

		header('Link: <' . $path . '>; rel=preload; as=script', false);
		echo sprintf(self::FORMAT, $path, $this->mode);
	}
}
