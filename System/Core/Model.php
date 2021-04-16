<?php

/**
 * Model
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.3
 */

namespace BMVC\Core;
use BMVC\Libs\{BasicDB, MError};

final class Model
{

	private static $params = [];

	function __construct()
	{
		self::DB();
	}

	static function DB()
	{
		$host = config('db/host');
		$name = config('db/name');
		$user = config('db/user');
		$pass = config('db/pass');

		return new BasicDB($host, $name, $user, $pass);
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

					if (file_exists($mxfile = APPDIR . '/Modules/' . $module . '/Model/_' . $model . '_.php')) {

						require_once $mxfile;

						$mxmodel = '_' . $module . '_';

						if (strpos($mxmodel, "/") || strpos($mxmodel, "\\")) {
							$mxmodel = explode('/', $mxmodel);
							$mxmodel = end($mxmodel);
						}

						$_mxmodel = (App::$namespaces['model'] . $mxmodel);

						if (class_exists($_mxmodel)) {
							new $_mxmodel();
						} else {
							MError::title('Model Error!')::print('Class Not Defined in Model File!', 'Model Name: ' . $module . '/' . $mxmodel);
						}
					}

					if (file_exists($file = APPDIR . '/Modules/' . $module . '/Model/' . $model . '.php')) {

						require_once $file;

						if (strpos($model, "/") || strpos($model, "\\")) {
							$model = explode('/', $model);
							$model = end($model);
						}

						$_model = (App::$namespaces['model'] . $model);

						if (class_exists($_model)) {
							if (is_array(self::$params) && !empty(self::$params)) {
								return $return = new $_model(self::$params);
							} else {
								return $return = new $_model();
							}
						} else {
							MError::title('Model Error!')::print('Class Not Defined in Model File!', 'Model Name: ' . $module . '/' . $model);
						}
					} else {
						MError::title('Model Error!')::print('Model Not Found!', 'Model Name: ' . $module . '/' . $model);
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
}

# Initialize - AutoInitialize
# new Model;