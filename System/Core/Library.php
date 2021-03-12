<?php

/**
 * Library
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.4
 */

namespace System;

class Library
{
	
	function __construct()
	{		
		if (config('libraries') != null) {
			self::import(config('libraries'));
		}

		spl_autoload_register(function($class) {
			$prefix = '\\';
			$base_dir = APPDIR . '/Libraries/';
			$len = strlen($prefix);
			if (strncmp($prefix, $class, $len) !== 0) {
				return;
			}
			$relative_class = substr($class, $len);
			$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
			if (file_exists($file)) {
				require $file;
			} else {
				MError::title('Class Error!')::print('Class Not Found!', 'Class Name: ' . $file);
			}
		});

		array_map(function ($file) {
			require_once $file;

			$eFile  = explode('/', $file);
			$eclass = array_pop($eFile);
			$eclass = explode('.', $eclass);
			$class  = array_shift($eclass);

			if (!class_exists($class)) {
				MError::title('Class Error!')::print('Class Not Found!', 'Class Name: ' . $class);
			}
		}, glob(SYSTEMDIR . "/Libraries/*.php"));
	}

	private static function include($library, $type=null)
	{
		if ($library != null) {
			$uri = ($type == 'SYSTEM') ? (SYSTEMDIR . '/Libraries/') : (APPDIR . '/Libraries/');
			if (_dir($uri)) {
				if (file_exists($file = $uri . $library . '.php')) {
					require_once $file;
				} else {
					MError::title('Library Error!')::print('Library Not Found!', 'Library Name: ' . $library);
				}
			} else {
				MError::title('Library Error!')::print('Library Dir Not Found!');
			}
		}
	}

	static function import($library, $type='APP')
	{
		if ($library != null) {
			if (is_array($library)) {
				foreach($library as $data) {
					self::include($data, $type);
				}
			} else {
				self::include($library, $type);
			}
		}
	}
}

# Initialize
new Library;