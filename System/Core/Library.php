<?php

/**
 * Library
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.6
 */

namespace System;

class Library
{
	
	function __construct()
	{		
		if (config('libraries') != null) {
			self::import(config('libraries'));
		}
	}

	private static function include($library, $type=null)
	{
		if ($library != null) {

			if ($library == 'index') return false;

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

# Initialize - AutoInitialize
# new Library;