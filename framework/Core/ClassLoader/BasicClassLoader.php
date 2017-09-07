<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * BasicClassLoader.php
 * "Basic" class loader relying on PHP's require_once to load PHP classes without any special features.
 */

namespace SmoothPHP\Framework\Core\ClassLoader;

require_once __DIR__ . '/ClassLoader.php';

class BasicClassLoader implements ClassLoader {
	private $prefixes;

	public function __construct() {
		$this->prefixes = [];
		$this->prefixes['SmoothPHP\Framework'] = [__ROOT__ . 'framework/'];
		$this->prefixes[''] = [__ROOT__ . 'src/'];
	}

	public function addPrefix($namespace, $path) {
		if (!is_dir($path))
			throw new \RuntimeException(sprintf('\'%s\' is not a valid class path', $path));

		if (substr($namespace, 0, 1) == '\\')
			$namespace = substr($namespace, 1);
		if (!isset($this->prefixes[$namespace]))
			$this->prefixes[$namespace] = [];
		$this->prefixes[$namespace][] = $path;
	}

	public function loadFromComposer() {
		$composerNamespaces = __ROOT__ . 'src/vendor/composer/autoload_psr4.php';
		if (file_exists($composerNamespaces)) {
			$composerLibs = require $composerNamespaces;

			foreach ($composerLibs as $namespace => $dirs) {
				$namespace = substr($namespace, 0, strlen($namespace) - 1);
				foreach ($dirs as $dir)
					$this->addPrefix($namespace, $dir . '/');
			}
		}

		$composerFiles = __ROOT__ . 'src/vendor/composer/autoload_files.php';
		if (file_exists($composerFiles)) {
			$files = require $composerFiles;

			foreach ($files as $file) {
				require_once $file;
			}
		}

		$composerUnspacedFiles = __ROOT__ . 'src/vendor/composer/autoload_namespaces.php';
		if (file_exists($composerUnspacedFiles)) {
			$namespaces = require $composerUnspacedFiles;

			foreach ($namespaces as $dirs)
				foreach ($dirs as $dir)
					$this->addPrefix('', $dir . '/');
		}
	}

	public function register() {
		spl_autoload_register([$this, 'loadClass'], true, false);
	}

	public function loadClass($class) {
		if ($file = $this->findClassFile($class))
			require_once $file;

		return $file;
	}

	protected function findClassFile($class) {
		if ($class[0] == '\\')
			$class = substr($class, 1);

		if (($pos = strrpos($class, '\\')) !== false) {
			$classPath = str_replace('\\', '/', substr($class, 0, $pos)) . '/';
			$className = substr($class, $pos + 1);
		} else {
			$classPath = null;
			$className = $class;
		}

		$classPath .= str_replace('_', '/', $className) . '.php';

		foreach ($this->prefixes as $prefix => $dirs) {
			if (empty($prefix) || strpos($class, $prefix) === 0) {
				if (!empty($prefix))
					$prefix = $prefix . '\\';
				$prefix = str_replace('\\', '/', $prefix);

				foreach ($dirs as $dir) {
					$ldPath = preg_replace('#' . $prefix . '#', $dir, $classPath, 1);
					if (($realpath = realpath($ldPath)) === false) {
						clearstatcache(true, $ldPath);
						$realpath = realpath($ldPath);
					}

					if (file_exists($realpath))
						return $realpath;
				}
			}
		}

		if ($file = stream_resolve_include_path($classPath))
			return $file;

		// Regular means don't work
		$next = strstr($class, '\\');
		if ($next)
			return $this->findClassFile($next);
	}

}