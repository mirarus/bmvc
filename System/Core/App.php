<?php

/**
 * App
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.2
 */

namespace System;

class App
{

	protected static 
	$routes = [],
	$groups = [],
	$baseRoute = '/',
	$ip = '';

	static $notFound = '';

	private static $patterns  = [
		'{all}'       => '(.*)',
		'{al}'        => '([^/]+)',
		'{num}'       => '([0-9]+)',
		'{alpha}'	  => '([a-zA-Z]+)',
		'{alpnum}'    => '([a-zA-Z0-9_-]+)',
		'{lowercase}' => '([a-z]+)',
		'{uppercase}' => '([A-Z]+)',
	];

	function __construct()
	{
		if (is_cli()) {
			die("Cli Not Available, Browser Only.");
		}

		if (session_status() !== PHP_SESSION_ACTIVE || session_id() === "") {
			@ini_set('session.cookie_httponly', 1);
			@ini_set('session.use_only_cookies', 1);
			@ini_set('session.gc_maxlifetime', 3600);
			@session_set_cookie_params(3600);
			
			session_name("BMVC-MMVC");
			session_start();
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

	static function Route($method, $pattern, $callback)
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
			//$pattern = str_replace($key, $value, $pattern);
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

	static function Run()
	{
		if (isset(self::$routes) && !empty(self::$routes) && is_array(self::$routes)) {
			$match = 0;
			foreach (self::$routes as $route) {
				$method = $route['method'];
				$action = $route['callback'];
				$url 	= $route['pattern'];
				$route['ip'] = $route['ip'] ?? null;
				$_GET['url'] = $_GET['url'] ?? null;

				if (preg_match("#^{$url}$#", '/' . rtrim(@$_GET['url'], '/'), $params)) {
					if ($method === @self::get_request_method() && @self::check_ip(@$route['ip'])) {

						if (strstr(@$_SERVER['REQUEST_URI'], '/Public/')) {
							self::get_404();
						}

						$match++;
						array_shift($params);
						
						if (is_callable($action)) {
							return call_user_func_array($action, array_values($params));
						} else {
							if (!isset($_GET['url']) && empty($_GET['url'])) {
								$action = [
									config('default/module'), 
									config('default/controller'), 
									config('default/method')
								];
							}
							if (is_dir(APPDIR . '/Modules/') && opendir(APPDIR . '/Modules/')) {
								@Controller::call(@$action, @$params);
							} else {
								MError::title('Module Error!')::print('Modules Dir Not Found!');
							}
						}
					}
				}
			}
			if ($match === 0) {
				self::get_404();
			}
		} else {
			MError::title('Route Error!')::print('Route Not Found!');
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
				@Controller::call(@self::$notFound, null);
			}
		} else {
			MError::print('404 Not Found!', null, true);
		}
		exit();
	}

	private static function get_request_method()
	{
		$method = @$_SERVER['REQUEST_METHOD'];
		if ($method == "HEAD") {
			ob_start();
			$method = "GET";
		} elseif ($method == "POST") {
			if (function_exists('getallheaders'))
				getallheaders();
			$headers = [];
			foreach ($_SERVER as $name => $value) {
				if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
					$headers[@strtr(ucwords(strtolower(@strtr(substr($name, 5), ['_' => ' ']))), [' ' => '-', 'Http' => 'HTTP'])] = $value;
				}
			}
			if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ["PUT", "DELETE", "PATCH"])) {
				$method = $headers['X-HTTP-Method-Override'];
			}
		}
		return $method;
	}

	private static function check_ip($ip=null)
	{
		if (isset($ip) && !empty($ip)) {
			if (is_array($ip)) {
				if (!in_array($_SERVER['REMOTE_ADDR'], $ip)) {
					return false;
				}
				return true;
			} else {
				if ($_SERVER['REMOTE_ADDR'] != $ip) {
					return false;
				}
				return true;
			}
			return true;
		}
		return true;
	}
}

define("BMVC_END", microtime(true));
define("BMVC_LOAD", number_format((BMVC_END - BMVC_START), 5));