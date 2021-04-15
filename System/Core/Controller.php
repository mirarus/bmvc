<?php

/**
 * Controller
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.6
 */

namespace System;

class Controller
{	

	private static $instance;
	private static $params = [];

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	static function view($view, $data=[], $layout=false)
	{
		return View::load($view, $data, $layout);
	}

	static function import($action, &$return=null)
	{
		$module = null;
		$controller = null;

		if (is_array($action)) {
			list($module, $controller) = $action;
		} elseif (strstr($action, '@')) {
			list($module, $controller) = explode('@', $action);
		} elseif (strstr($action, '/')) {
			list($module, $controller) = explode('/', $action);
		} else {
			$module = config('default/module');
			$controller = $action;
		}

		if ($module != null && $controller != null) {

			$controller = ucfirst($controller);

			if (_dir(APPDIR . '/Modules/' . $module)) {
				if (_dir(APPDIR . '/Modules/' . $module . '/Controller')) {

					if (file_exists($cxfile = APPDIR . '/Modules/' . $module . '/Controller/_' . $module . '_.php')) {

						require_once $cxfile;

						$cxcontroller = '_' . $module . '_';

						if (strpos($cxcontroller, "/") || strpos($cxcontroller, "\\")) {
							$cxcontroller = explode('/', $cxcontroller);
							$cxcontroller = end($cxcontroller);
						}

						$_cxcontroller = ('App\Controller\\' . $cxcontroller);

						if (class_exists($_cxcontroller)) {
							new $_cxcontroller();
						} else {
							MError::title('Controller Error!')::print('Class Not Defined in Controller File!', 'Controller Name: ' . $module . '/' . $cxcontroller);
						}
					}

					if (file_exists($file = APPDIR . '/Modules/' . $module . '/Controller/' . $controller . '.php')) {
						
						require_once $file;

						if (strpos($controller, "/") || strpos($controller, "\\")) {
							$controller = explode('/', $controller);
							$controller = end($controller);
						}

						$_controller = ('App\Controller\\' . $controller);

						if (class_exists($_controller)) {
							if (is_array(self::$params) && !empty(self::$params)) {
								return $return = new $_controller(self::$params);
							} else {
								return $return = new $_controller();
							}
						} else {
							MError::title('Controller Error!')::print('Class Not Defined in Controller File!', 'Controller Name: ' . $module . '/' . $controller);
						}
					} else {
						MError::title('Controller Error!')::print('Controller Not Found!', 'Controller Name: ' . $module . '/' . $controller);
					}
				} else {
					MError::title('Controller Error!')::print('Controller Dir Not Found!');
				}
			} else {
				MError::title('Controller Error!')::print('Module Not Found!', 'Module Name: ' . $module);
			}
		}
	}

	static function par($params=[])
	{
		self::$params = $params;
		return new self;
	}

	static function call($action, $params=null, &$return=null)
	{
		$module = null;
		$controller = null;
		$method = null;

		if (is_array($action)) {
			list($module, $controller, $method) = $action;
		} elseif (strstr($action, '@')) {
			if (count(explode('@', $action)) == 3) {
				list($module, $controller, $method) = explode('@', $action);
			}
		} elseif (strstr($action, '/')) {
			if (count(explode('/', $action)) == 3) {
				list($module, $controller, $method) = explode('/', $action);
			}
		}

		if ($module != null && $controller != null && $method != null) {

			@$class = self::import([$module, $controller]);
			
			if (isset($class)) {

				if (method_exists(@$class, $method)) {
					if ($params == null) {
						return $return = call_user_func([$class, $method]);
					} else {
						return $return = call_user_func_array([$class, $method], array_values($params));
					}
				} else {
					MError::title('Controller Error!')::print('Controller Method Not Found!', 'Controller Name: ' . $module . '/' . $controller . '<br>Method Name: ' . $method);
				}
			}
		}
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
# new Controller;