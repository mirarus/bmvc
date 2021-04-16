<?php

/**
 * Request
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.3
 */

namespace BMVC\Core;

class Request
{

	const METHOD_HEAD = 'HEAD';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_OVERRIDE = '_METHOD';

	private static $instance;
	private static $formDataMediaTypes = ['application/x-www-form-urlencoded'];

	public $body;
	public $server;
	public $request;
	public $env;
	public $session;
	public $cookie;
	public $files;
	public $post;
	public $get;

	public function __construct()
	{
		$this->getBody('object');
	}

	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function getBody(string $type='object')
	{
		$_body = [
			'server'  => self::server(),
			'request' => self::request(),
			'env'     => self::env(),
			'session' => self::session(),
			'cookie'  => self::cookie(),
			'files'   => self::files(),
			'post'    => self::post(),
			'get'     => self::get()
		];

		if ($type == 'object') {
			$this->body    = self::arrayToObject($_body);

			$this->server  = $this->body->server;
			$this->request = $this->body->request;
			$this->env     = $this->body->env;
			$this->session = $this->body->session;
			$this->cookie  = $this->body->cookie;
			$this->files   = $this->body->files;
			$this->post    = $this->body->post;
			$this->get     = $this->body->get;
		} elseif ($type == 'array') {
			$this->body    = $_body;

			$this->server  = $this->body['server'];
			$this->request = $this->body['request'];
			$this->env     = $this->body['env'];
			$this->session = $this->body['session'];
			$this->cookie  = $this->body['cookie'];
			$this->files   = $this->body['files'];
			$this->post    = $this->body['post'];
			$this->get     = $this->body['get'];
		} else {
			$this->body    = $_body;

			$this->server  = $this->body['server'];
			$this->request = $this->body['request'];
			$this->env     = $this->body['env'];
			$this->session = $this->body['session'];
			$this->cookie  = $this->body['cookie'];
			$this->files   = $this->body['files'];
			$this->post    = $this->body['post'];
			$this->get     = $this->body['get'];
		}
	}

	public static function _server($key=null)
	{
		if ($key) {
			return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
		}
		return $_SERVER;
	}

	public static function header($key=null, $default=null)
	{
		$_header = Header::extract(self::_server());
		
		if ($key) {
			if ($default) {
				return $_header[$key] == $default;
			}
			return isset($_header[$key]) ? $_header[$key] : null;
		}
		return $_header;
	}

	public static function getMethod()
	{
		return self::_server('REQUEST_METHOD');
	}

	public static function isGet()
	{
		return self::getMethod() === self::METHOD_GET;
	}

	public static function isPost()
	{
		return self::getMethod() === self::METHOD_POST;
	}

	public static function isPut()
	{
		return self::getMethod() === self::METHOD_PUT;
	}

	public static function isPatch()
	{
		return self::getMethod() === self::METHOD_PATCH;
	}

	public static function isDelete()
	{
		return self::getMethod() === self::METHOD_DELETE;
	}

	public static function isHead()
	{
		return self::getMethod() === self::METHOD_HEAD;
	}

	public static function isOptions()
	{
		return self::getMethod() === self::METHOD_OPTIONS;
	}

	public static function isAjax()
	{
		if (self::header('X_REQUESTED_WITH') !== null && self::header('X_REQUESTED_WITH') === 'XMLHttpRequest') {
			return true;
		}
		return false;
	}

	public static function isFormData()
	{
		return (self::getMethod() === self::METHOD_POST && is_null(self::getContentType())) || in_array(self::getMediaType(), self::$formDataMediaTypes);
	}
	public static function getContentType()
	{
		return self::header('CONTENT_TYPE');
	}

	public static function getMediaType()
	{
		$contentType = self::getContentType();
		if ($contentType) {
			$contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
			return strtolower($contentTypeParts[0]);
		}
		return null;
	}

	public static function getMediaTypeParams()
	{
		$contentType = self::getContentType();
		$contentTypeParams = array();
		if ($contentType) {
			$contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
			$contentTypePartsLength = count($contentTypeParts);
			for ($i = 1; $i < $contentTypePartsLength; $i++) {
				$paramParts = explode('=', $contentTypeParts[$i]);
				$contentTypeParams[strtolower($paramParts[0])] = $paramParts[1];
			}
		}
		return $contentTypeParams;
	}

	public static function getContentCharset()
	{
		$mediaTypeParams = self::getMediaTypeParams();
		if (isset($mediaTypeParams['charset'])) {
			return $mediaTypeParams['charset'];
		}
		return null;
	}

	public static function getContentLength()
	{
		return self::header('CONTENT_LENGTH', 0);
	}

	public static function getHost()
	{
		if (self::_server('HTTP_HOST') !== null) {
			if (strpos(self::_server('HTTP_HOST'), ':') !== false) {
				$hostParts = explode(':', self::_server('HTTP_HOST'));
				return $hostParts[0];
			}
			return self::_server('HTTP_HOST');
		}
		return self::_server('SERVER_NAME');
	}

	public static function getPort()
	{
		return (int) self::_server('SERVER_PORT');
	}

	public static function getHostWithPort()
	{
		return sprintf('%s:%s', self::getHost(), self::getPort());
	}

	public static function getScheme()
	{
		return stripos(self::_server('SERVER_PROTOCOL'), 'https') === true ? 'https' : 'http';
	}

	public static function getScriptName()
	{
		return self::_server('SCRIPT_NAME');
	}

	public static function getPathInfo()
	{
		return self::_server('PATH_INFO');
	}

	public static function getPath()
	{
		return self::getScriptName() . self::getPathInfo();
	}

	public static function getResourceUri()
	{
		return self::getPathInfo();
	}

	public static function getUrl()
	{
		$url = self::getScheme() . '://' . self::getHost();
		if ((self::getScheme() === 'https' && self::getPort() !== 443) || (self::getScheme() === 'http' && self::getPort() !== 80)) {
			$url .= sprintf(':%s', self::getPort());
		}
		return $url;
	}

	public static function getIp()
	{
		return IP::get();
	}
	
	public static function getReferrer()
	{
		return self::header('HTTP_REFERER');
	}

	public static function getReferer()
	{
		return self::getReferrer();
	}

	public static function getUserAgent()
	{
		return self::header('HTTP_USER_AGENT');
	}

	public static function getRequestMethod()
	{
		$method = self::getMethod();
		if ($method === self::METHOD_HEAD) {
			ob_start();
			$method = self::METHOD_GET;
		} elseif ($method === self::METHOD_POST) {
			if (function_exists('getallheaders'))
				getallheaders();
			$headers = [];
			foreach (self::_server() as $name => $value) {
				if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
					$headers[@strtr(ucwords(strtolower(@strtr(substr($name, 5), ['_' => ' ']))), [' ' => '-', 'Http' => 'HTTP'])] = $value;
				}
			}
			if (self::header('X-HTTP-Method-Override') !== null && in_array(self::header('X-HTTP-Method-Override'), [self::METHOD_PUT, self::METHOD_DELETE, self::METHOD_PATCH])) {
				$method = self::header('X-HTTP-Method-Override');
			}
		}
		return $method;
	}

	public static function checkDomain($domain)
	{
		if (isset($domain) && !empty($domain)) {
			if ($domain !== trim(str_replace('www.', '', self::_server('SERVER_NAME')), '/'))
				return false;
			return true;
		}
		return true;
	}

	public static function checkIp($ip)
	{
		if (isset($ip) && !empty($ip)) {
			if (is_array($ip)) {
				if (!in_array(self::getIp(), $ip))
					return false;
				return true;
			} else {
				if (self::getIp() != $ip)
					return false;
				return true;
			}
			return true;
		}
		return true;
	}

	public static function server($data=null, $db_filter=true, $xss_filter=true)
	{
		return self::rea(self::_server(), $data, $db_filter, $xss_filter);
	}

	public static function request($data=null, $db_filter=true, $xss_filter=true)
	{
		return self::rea($_REQUEST, $data, $db_filter, $xss_filter);
	}

	public static function env($data=null)
	{
		return self::rea($_ENV, $data, false, false);
	}

	public static function session($data=null)
	{
		return self::rea($_SESSION, $data, false, false);
	}

	public static function cookie($data=null)
	{
		return self::rea($_COOKIE, $data, false, false);
	}

	public static function files($data=null, $xss_filter=true)
	{
		return self::rea($_FILES, $data, false, $xss_filter);
	}

	public static function post($data=null, $db_filter=true, $xss_filter=true)
	{
		return self::rea($_POST, $data, $db_filter, $xss_filter);
	}

	public static function get($data=null, $db_filter=true, $xss_filter=true)
	{
		return self::rea($_GET, $data, $db_filter, $xss_filter);
	}

	public static function filter($data=null, $type='post', $db_filter=true, $xss_filter=true)
	{
		if ($type == 'server') {
			return self::server($data, $db_filter, $xss_filter);
		} elseif ($type == 'request') {
			return self::request($data, $db_filter, $xss_filter);
		} elseif ($type == 'env') {
			return self::env($data);
		} elseif ($type == 'session') {
			return self::session($data);
		} elseif ($type == 'cookie') {
			return self::cookie($data);
		} elseif ($type == 'files') {
			return self::files($data, $xss_filter);
		} elseif ($type == 'post') {
			return self::post($data, $db_filter, $xss_filter);
		} elseif ($type == 'get') {
			return self::get($data, $db_filter, $xss_filter);
		}
	}

	public static function body(string $method=null, string $body_type='object')
	{
		self::instance()->getBody($body_type);

		if ($method) {
			if ($body_type == 'object') {
				return self::instance()->body->$method;
			} elseif ($body_type == 'array') {
				return self::instance()->body[$method];
			} else {
				return self::instance()->body[$method];
			}
		} else {
			return self::instance()->body;
		}
	}

	private static function rea($method, $data=null, $db_filter=true, $xss_filter=true)
	{
		if ($xss_filter == true) {
			$method = @Filter::filterXSS($method);
		}

		if (isset($data) && !empty($data)) {
			if ($db_filter == true) {
				if (isset($method[$data])) {
					return @Filter::filterDB($method[$data]);
				}
			} else {
				if (isset($method[$data])) {
					return $method[$data];
				}
			}
		} else {
			return $method;
		}
	}

	private static function arrayToObject(array $array) : object
	{
		$object = new \stdClass();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$value = self::arrayToObject($value);
			}
			$object->$key = $value;
		}
		return $object;
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