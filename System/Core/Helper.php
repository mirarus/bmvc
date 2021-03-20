<?php

/**
 * Helper
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

namespace System;

class Helper
{
	
	function __construct()
	{		
		if (config('helpers') != null) {
			self::import(config('helpers'));
		}
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

# Initialize - AutoInitialize
# new Helper;