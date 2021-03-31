<?php

/**
 * Model
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.9
 */

namespace System;

class Model
{

	private static $instance;
	private static $params = [];

	function __construct()
	{
		self::DB();
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	static function DB()
	{
		$active = config('db/active');
		$host = config('db/host');
		$name = config('db/name');
		$user = config('db/user');
		$pass = config('db/pass');

		if ($active == true) {
			return new \BasicDB($host, $name, $user, $pass);
		}
	}

	static function import($action, &$return=null)
	{
		$module = null;
		$model = null;

		if (is_array($action)) {
			list($module, $model) = $action;
		} elseif (strstr($action, '@')) {
			list($module, $model) = explode('@', $action);
		} elseif (strstr($action, '/')) {
			list($module, $model) = explode('/', $action);
		} else {
			$module = config('default/module');
			$model = $action;
		}

		if ($module != null && $model != null) {

			$model = ucfirst($model);

			if (_dir(APPDIR . '/Modules/' . $module)) {
				if (_dir(APPDIR . '/Modules/' . $module . '/Model')) {
					if (file_exists($file = APPDIR . '/Modules/' . $module . '/Model/' . $model . '.php')) {

						require_once $file;

						if (strpos($model, "/") || strpos($model, "\\")) {
							$model = explode('/', $model);
							$model = end($model);
						}

						$_model = ('App\Model\\' . $model);

						if (class_exists($_model)) {
							if (is_array(self::$params) && !empty(self::$params)) {
								return $return = new $_model(self::$params);
							} else {
								return $return = new $_model();
							}
						} else {
							MError::title('Model Error!')::print('Class Not Defined in Model File!', 'Model Name: ' . $model);
						}
					} else {
						MError::title('Model Error!')::print('Model Not Found!', 'Model Name: ' . $model);
					}
				} else {
					MError::title('Model Error!')::print('Model Dir Not Found!');
				}
			} else {
				MError::title('Model Error!')::print('Module Not Found!', 'Module Name: ' . $module);
			}
		}
	}

	static function par($params=[])
	{
		self::$params = $params;
		return new self;
	}

	function __call($method, $args)
	{
		return isset($this->{$method}) && is_callable($this->{$method}) ? call_user_func_array($this->{$method}, $args) : null;
	}

	static function __callStatic($method, $args)
	{
		return isset(self::$method) && is_callable(self::$method) ? call_user_func_array(self::$method, $args) : null;
	}

	function __set($key, $value)
	{
		$this->{$key} = $value instanceof \Closure ? $value->bindTo($this) : $value;
	}
}

# Initialize - AutoInitialize
# new Model;