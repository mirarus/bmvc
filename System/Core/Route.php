<?php

/**
 * Route
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

class Route extends System\App
{

	private static $groupped = 0;
	private static $mainRoute = '/';

	static function group($callback)
	{
		self::$groupped++;
		self::$groups[] = [
			'baseRoute' => self::$baseRoute,
			'ip'        => self::$ip
		];
		call_user_func($callback);
		if (self::$groupped > 0) {
			self::$baseRoute = self::$groups[self::$groupped-1]['baseRoute'];
			self::$ip        = self::$groups[self::$groupped-1]['ip'];
		}
		self::$groupped--;
		if (self::$groupped <= 0) {
			self::$baseRoute = '/';
			self::$ip        = '';
		}
	}

	static function prefix($prefix)
	{
		self::$baseRoute = self::$mainRoute . $prefix;
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
		$pattern = self::_parseUri(self::$routes[$routeKey]['uri'], $expressions);
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
				$uri = $route['uri'];
				$pattern = self::_parseUri($uri, $params);
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
		App::get_404();
	}

	static function __callStatic($method, $args)
	{
		//if ($method == 'module')
		return new self;
	}

	function __call($method, $args)
	{
		//if ($method == 'module')
		return new self;
	}
}

# Initialize
new Route;