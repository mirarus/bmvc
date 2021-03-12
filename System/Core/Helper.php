<?php

/**
 * Helper
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

namespace System;

class Helper
{
	
	function __construct()
	{		
		if (config('helpers') != null) {
			self::import(config('helpers'));
		}

		spl_autoload_register(function($class) {
			$prefix = '\\';
			$base_dir = APPDIR . '/Helpers/';
			$len = strlen($prefix);
			if (strncmp($prefix, $class, $len) !== 0) {
				return;
			}
			$relative_class = substr($class, $len);
			$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
			if (file_exists($file)) {
				require $file;
			} else {
				MError::title('File Error!')::print('File Not Found!', 'File Name: ' . $file);
			}
		});

		array_map(function ($file) {
			require_once $file;
		}, glob(SYSTEMDIR . "/Helpers/*.php"));
	}

	private static function include($helper, $type=null)
	{
		if ($helper != null) {
			$uri = ($type == 'SYSTEM') ? (SYSTEMDIR . '/Helpers/') : (APPDIR . '/Helpers/');
			if (_dir($uri)) {
				if (file_exists($file = $uri . $helper . '.php')) {
					require_once $file;
				} else {
					MError::title('Helpers Error!')::print('Helpers Not Found!', 'Helpers Name: ' . $helper);
				}
			} else {
				MError::title('Helpers Error!')::print('Helpers Dir Not Found!');
			}
		}
	}

	static function import($helper, $type='APP')
	{
		if ($helper != null) {
			if (is_array($helper)) {
				foreach($helper as $data) {
					self::include($data, $type);
				}
			} else {
				self::include($helper, $type);
			}
		}
	}
}

# Initialize
new Helper;