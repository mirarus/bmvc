<?php

/**
 * Route
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.7
 */

namespace BMVC\Core;

final class Route
{

	private static $instance;
	private static $notFound = '';
	private static $routes = [];
	private static $groups = [];
	private static $prefix = '/';
	private static $ip;
	private static $groupped = 0;
	private static $mainRoute = '/';
	private static $patterns  = [
		':all'        => '(.*)',
		':num'        => '([0-9]+)',
		':alpha'	  	=> '([a-zA-Z]+)',
		':alpnum'     => '([a-zA-Z0-9_-]+)',
		':lowercase'  => '([a-z]+)',
		':uppercase'  => '([A-Z]+)',

		'{all}'       => '(.*)',
		'{num}'       => '([0-9]+)',
		'{alpha}'	    => '([a-zA-Z]+)',
		'{alpnum}'    => '([a-zA-Z0-9_-]+)',
		'{lowercase}' => '([a-z]+)',
		'{uppercase}' => '([A-Z]+)',
	];

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	static function Run(&$return = null)
	{
		$routes = Route::getRoutes();

		if (isset($routes) && !empty($routes) && is_array($routes)) {
			$match = 0;

			foreach ($routes as $route) {

				$method = $route['method'];
				$action = $route['callback'];
				$url 	= $route['pattern'];
				$ip 	= (isset($route['ip']) ? $route['ip'] : null);
				$_url = isset($_GET['url']) ? $_GET['url'] : null;

				if (preg_match("#^{$url}$#", '/' . rtrim(@$_url, '/'), $params)) {

					if ($method === @Request::getRequestMethod() && @Request::checkIp($ip)) {

						if (strstr(@$_SERVER['REQUEST_URI'], '/Public/')) {
							self::get_404();
						}

						$match++;
						array_shift($params);

						return $return = [
							'method' => $method,
							'action' => $action,
							'params' => $params,
							'url' => $url,
							'_url' => $_url
						];
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

	private static function Route($method, $pattern, $callback)
	{
		$closure = null;
		if ($pattern == '/') {
			$pattern = self::$prefix . trim($pattern, '/');
		} else {
			if (self::$prefix == '/') {
				$pattern = self::$prefix . trim($pattern, '/');
			} else {
				$pattern = self::$prefix . $pattern;
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

	static function group($callback)
	{
		self::$groupped++;
		self::$groups[] = [
			'baseRoute' => self::$prefix,
			'ip'        => self::$ip
		];
		call_user_func($callback);
		if (self::$groupped > 0) {
			self::$prefix = self::$groups[self::$groupped-1]['baseRoute'];
			self::$ip     = self::$groups[self::$groupped-1]['ip'];
		}
		self::$groupped--;
		if (self::$groupped <= 0) {
			self::$prefix = '/';
			self::$ip     = '';
		}
		self::$prefix = @self::$groups[self::$groupped-1]['baseRoute'];
	}

	static function prefix($prefix)
	{
		self::$prefix = self::$mainRoute . $prefix;
		return new self;
	}

	static function ip($ip)
	{
		self::$ip = $ip;
		return new self;
	}

	static function get($pattern, $callback)
	{
		self::Route('GET', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function post($pattern, $callback)
	{
		self::Route('POST', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function patch($pattern, $callback)
	{
		self::Route('PATCH', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function delete($pattern, $callback)
	{
		self::Route('DELETE', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function put($pattern, $callback)
	{
		self::Route('PUT', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function options($pattern, $callback)
	{
		self::Route('OPTIONS', self::$mainRoute . $pattern, $callback);
		return new self;
	}

	static function match($methods, $pattern, $callback)
	{
		foreach ($methods as $method) {
			self::Route(strtoupper($method), self::$mainRoute . $pattern, $callback);
		}
	}

	static function any($pattern, $callback)
	{
		$methods = ['GET', 'POST', 'PATCH', 'DELETE', 'PUT', 'OPTIONS'];
		foreach ($methods as $method) {
			self::Route($method, self::$mainRoute . $pattern, $callback);
		}
	}

	function where($expressions)
	{
		$routeKey = array_search(end(self::$routes), self::$routes);
		$pattern = self::_parseUri(self::$routes[$routeKey]['pattern'], $expressions);
		$pattern = '/' . implode('/', $pattern);
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		self::$routes[$routeKey]['pattern'] = $pattern;
		return new self;
	}

	static function name($name, $params=[])
	{
		$routeKey = array_search(end(self::$routes), self::$routes);
		self::$routes[$routeKey]['name'] = $name;
		return new self;
	}

	static function getUrl($name, $params=[])
	{
		foreach (self::$routes as $route) {
			if (array_key_exists('name', $route) && $route['name'] == $name) {
				$pattern = $route['pattern'];
				$pattern = self::_parseUri($pattern, $params);
				$pattern = implode('/', $pattern);
				break;
			}
		}
		return $pattern;
	}

	static function getRoutes()
	{
		return self::$routes;
	}

	private static function _parseUri($uri, $expressions=[])
	{
		$pattern = explode('/', ltrim($uri, '/'));
		foreach ($pattern as $key => $val) {
			if (preg_match('/[\[{\(].*[\]}\)]/U', $val, $matches)) {
				foreach ($matches as $match) {
					$matchKey = substr($match, 1, -1);
					if (array_key_exists($matchKey, $expressions))
						$pattern[$key] = $expressions[$matchKey];
				}
			}
		}
		return $pattern;
	}

	static function set_404($callback)
	{
		self::$notFound = $callback;
		return new self;
	}

	static function get_404()
	{
		http_response_code(404);
		if (self::$notFound) {
			if (is_callable(self::$notFound)) {
				call_user_func(self::$notFound);
			} else {
				@Controller::call(self::$notFound, null);
			}
		} else {
			MError::title('Page Error!')::print('404 Page Not Found!', (@Request::get('url') ? 'Page: ' . Request::get('url') : null) , false);
		}
		exit();
	}

	static function url_check(array $urls=[], string $url)
	{
		if (!in_array($url, $urls)) {
			self::get_404();
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
# new Route;