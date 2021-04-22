<?php

/**
 * Model
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.4
 */

namespace BMVC\Core;

use Exception;
use BMVC\Libs\BasicDB;

final class Model
{

	/**
	 * @var string
	 */
	private static $dir = APPDIR . '/Http/Model/';

	/**
	 * @var array
	 */
	private static $params = [];

	/**
	 * @return Model
	 */
	public function __construct()
	{
		self::DB();
	}

	/**
	 * @return BasicDB
	 */
	public static function DB(): BasicDB
	{
		$host = config('db/host');
		$name = config('db/name');
		$user = config('db/user');
		$pass = config('db/pass');

		return new BasicDB($host, $name, $user, $pass);
	}

	/**
	 * @param mixed       $action
	 * @param object|null &$return
	 */
	public static function import($action, object &$return=null)
	{
		$model     = null;
		$namespace = null;

		if (@strstr($action, '@')) {
			$action = explode('@', $action);
		} elseif (@strstr($action, '/')) {
			$action = explode('/', $action);
		} elseif (@strstr($action, '.')) {
			$action = explode('.', $action);
		}

		if ($action > 1) {
			$model = @array_pop($action);
		} else {
			$model = $action;
		}
		$namespace = @implode($action, '\\');

		if (($namespace === null || $namespace !== null) && $model != null) {

			$_nsm_ = ($namespace != null) ? implode([$namespace, '_model_'], '/') : '_model_';
			
			if (file_exists(self::$dir . $_nsm_ . '.php')) {
				$_model_ = (App::$namespaces['model'] . str_replace(['/', '//'], '\\', $_nsm_));
				new $_model_();
			}

			$model  = ucfirst($model);
			$_nsm   = ($namespace != null) ? implode([$namespace, $model], '/') : $model;
			$_model = (App::$namespaces['model'] . str_replace(['/', '//'], '\\', $_nsm));

			if (is_array(self::$params) && !empty(self::$params)) {
				return $return = new $_model(self::$params);
			} else {
				return $return = new $_model();
			}
		}
	}

	/**
	 * @param  array $params
	 * @return Model
	 */
	public static function par(array $params=[]): Model
	{
		self::$params = $params;
		return new self;
	}

	/**
	 * @param mixed       $action
	 * @param array       $params
	 * @param object|null &$return
	 */
	public static function call($action, array $params=[], object &$return=null)
	{
		$method    = null;
		$model     = null;
		$namespace = null;

		if (@strstr($action, '@')) {
			$action = explode('@', $action);
		} elseif (@strstr($action, '/')) {
			$action = explode('/', $action);
		} elseif (@strstr($action, '.')) {
			$action = explode('.', $action);
		}

		$method    = @array_pop($action);
		$model     = @array_pop($action);
		$namespace = @implode($action, '\\');

		if (isset($namespace) && $model != null && $method != null) {

			$class = self::import([$namespace, $model]);
			
			if (method_exists($class, $method)) {
				if ($params == null) {
					return $return = call_user_func([$class, $method]);
				} else {
					return $return = call_user_func_array([$class, $method], array_values($params));
				}
			} else {
				$model = ucfirst($model);
				$_nsm  = ($namespace != null) ? implode([$namespace, $model], '/') : $model;
				throw new Exception('Model Method Not Found! | Model: ' . $_nsm . ' - Method: ' . $method);
			}
		}
	}
}