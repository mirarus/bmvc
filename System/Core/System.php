<?php

/**
 * System
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

namespace System;

class System
{

	function __call($class, $args=null)
	{
		$_class = ('System\\' . $class);

		if (class_exists($_class)) {
			if (is_array($args) && !empty($args)) {
				return $return = new $_class($args);
			} else {
				return $return = new $_class();
			}
		} else {
			MError::title('Class Error!')::print('Class not found in System namespace!', 'Class Name: ' . $class);
		}
	}

	static function __callStatic($class, $args=null)
	{
		$_class = ('System\\' . $class);

		if (class_exists($_class)) {
			if (is_array($args) && !empty($args)) {
				return $return = new $_class($args);
			} else {
				return $return = new $_class();
			}
		} else {
			MError::title('Class Error!')::print('Class not found in System namespace!', 'Class Name: ' . $class);
		}
	}
}

# Initialize - AutoInitialize
# new System;