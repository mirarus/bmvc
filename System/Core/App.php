<?php

/**
 * App
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.7
 */

namespace System;

class App
{

	protected static $routes = [];
	protected static $groups = [];
	protected static $baseRoute = '/';
	protected static $ip;

	public static $notFound = '';

	private static $instance;
	private static $patterns  = [
		':all'        => '(.*)',
		':num'        => '([0-9]+)',
		':alpha'	  => '([a-zA-Z]+)',
		':alpnum'     => '([a-zA-Z0-9_-]+)',
		':lowercase'  => '([a-z]+)',
		':uppercase'  => '([A-Z]+)',

		'{all}'       => '(.*)',
		'{num}'       => '([0-9]+)',
		'{alpha}'	  => '([a-zA-Z]+)',
		'{alpnum}'    => '([a-zA-Z0-9_-]+)',
		'{lowercase}' => '([a-z]+)',
		'{uppercase}' => '([A-Z]+)',
	];

	public function __construct()
	{
		if (is_cli()) {
			die("Cli Not Available, Browser Only.");
		}
		
		header("X-Frame-Options: sameorigin");
		header("Strict-Transport-Security: max-age=15552000; preload");
		header("X-Powered-By: PHP/BMVC-MMVC");

		date_default_timezone_set(TIMEZONE);

		switch (ENVIRONMENT) {
			case 'development':
			error_reporting(-1);
			ini_set('display_errors', 0);
			break;
			case 'testing':
			case 'production':
			ini_set('display_errors', 0);
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
			break;
			default:
			header('HTTP/1.1 503 Service Unavailable.', true, 503);
			echo 'The application environment is not set correctly.';
			exit(1);
		}
	}

	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public static function Route($method, $pattern, $callback)
	{
		$closure = null;
		if ($pattern == '/') {
			$pattern = self::$baseRoute . trim($pattern, '/');
		} else {
			if (self::$baseRoute == '/') {
				$pattern = self::$baseRoute . trim($pattern, '/');
			} else {
				$pattern = self::$baseRoute . $pattern;
			}
		}
		foreach (self::$patterns as $key => $value) {
			$pattern = @strtr($pattern, [$key => $value]);
		}
		if (is_callable($callback)) {
			$closure = $callback;
		} elseif (stripos($callback, '@') !== false) {
			$closure = $callback;
		}
		$route_ = [
			'method'   => $method,
			'pattern'  => $pattern,
			'callback' => @$closure
		];
		if (self::$ip) {
			$route_['ip'] = self::$ip;
		}
		self::$routes[] = $route_;
	}

	public static function Run()
	{
		if (isset(self::$routes) && !empty(self::$routes) && is_array(self::$routes)) {
			$match = 0;

			foreach (self::$routes as $route) {

				$method = $route['method'];
				$action = $route['callback'];
				$url 	= $route['pattern'];
				$ip 	= (isset($route['ip']) ? $route['ip'] : null);
				$_url   = isset($_GET['url']) ? $_GET['url'] : null;


				if (preg_match("#^{$url}$#", '/' . rtrim(@$_url, '/'), $params)) {
					if ($method === @Request::getRequestMethod() && @Request::checkIp($ip)) {

						if (strstr(@$_SERVER['REQUEST_URI'], '/Public/')) {
							self::get_404();
						}

						$match++;
						array_shift($params);
						
						if (is_callable($action)) {
							return call_user_func_array($action, array_values($params));
						} else {
							if (!isset($_url) && empty($_url)) {
								$action = [
									config('default/module'), 
									config('default/controller'), 
									config('default/method')
								];
							}
							if (is_dir(APPDIR . '/Modules/') && opendir(APPDIR . '/Modules/')) {
								@Controller::call(@$action, @$params);
							} else {
								MError::title('Module Error!')::print('Modules Dir Not Found!', null, true);
							}
						}
					}
				}
			}
			if ($match === 0) {
				self::get_404();
			}
		} else {
			MError::title('Route Error!')::print('Route Not Found!', null, true);
			http_response_code(404);
			exit();
		}
	}

	protected static function get_404()
	{
		http_response_code(404);
		if (self::$notFound) {
			if (is_callable(self::$notFound)) {
				call_user_func(self::$notFound);
			} else {
				@Controller::call(self::$notFound, null);
			}
		} else {
			MError::title('Page Error!')::print('404 Page Not Found!', 'Page: ' . reg('url'), false);
		}
		exit();
	}

	public function __call($method, $args)
	{
		return isset($this->{$method}) && is_callable($this->{$method}) ? call_user_func_array($this->{$method}, $args) : null;
	}

	public static function __callStatic($method, $args)
	{
		return isset(self::$method) && is_callable(self::$method) ? call_user_func_array(self::$method, $args) : null;
	}

	public function __set($key, $value)
	{
		$this->{$key} = $value instanceof \Closure ? $value->bindTo($this) : $value;
	}
}

define("BMVC_END", microtime(true));
define("BMVC_LOAD", number_format((BMVC_END - BMVC_START), 5));