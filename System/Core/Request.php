<?php

/**
 * Request
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

namespace System;

use System\{Header, Filter};

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
	private $server;
	public $header;

	public function __construct()
	{
		$this->server = $_SERVER;
		$this->header = Header::extract($this->server);
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function getMethod()
	{
		return $this->server['REQUEST_METHOD'];
	}

	public function isGet()
	{
		return $this->getMethod() === self::METHOD_GET;
	}

	public function isPost()
	{
		return $this->getMethod() === self::METHOD_POST;
	}

	public function isPut()
	{
		return $this->getMethod() === self::METHOD_PUT;
	}

	public function isPatch()
	{
		return $this->getMethod() === self::METHOD_PATCH;
	}

	public function isDelete()
	{
		return $this->getMethod() === self::METHOD_DELETE;
	}

	public function isHead()
	{
		return $this->getMethod() === self::METHOD_HEAD;
	}

	public function isOptions()
	{
		return $this->getMethod() === self::METHOD_OPTIONS;
	}

	public function isAjax()
	{
		if ($this->params('isajax')) {
			return true;
		} elseif (isset($this->header['X_REQUESTED_WITH']) && $this->header['X_REQUESTED_WITH'] === 'XMLHttpRequest') {
			return true;
		}

		return false;
	}

	public function isXhr()
	{
		return $this->isAjax();
	}

	public function isFormData()
	{
		return ($this->getMethod() === self::METHOD_POST && is_null($this->getContentType())) || in_array($this->getMediaType(), self::$formDataMediaTypes);
	}

	public function headers($key = null, $default = null)
	{
		if ($key) {
			return $this->header->get($key, $default);
		}
		return $this->header;
	}

	public function getContentType()
	{
		return $this->header->get('CONTENT_TYPE');
	}

	public function getMediaType()
	{
		$contentType = $this->getContentType();
		if ($contentType) {
			$contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
			return strtolower($contentTypeParts[0]);
		}
		return null;
	}

	public function getMediaTypeParams()
	{
		$contentType = $this->getContentType();
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

	public function getContentCharset()
	{
		$mediaTypeParams = $this->getMediaTypeParams();
		if (isset($mediaTypeParams['charset'])) {
			return $mediaTypeParams['charset'];
		}
		return null;
	}

	public function getContentLength()
	{
		return $this->header->get('CONTENT_LENGTH', 0);
	}

	public function getHost()
	{
		if (isset($this->server['HTTP_HOST'])) {
			if (strpos($this->server['HTTP_HOST'], ':') !== false) {
				$hostParts = explode(':', $this->server['HTTP_HOST']);
				return $hostParts[0];
			}
			return $this->server['HTTP_HOST'];
		}
		return $this->server['SERVER_NAME'];
	}

	public function getHostWithPort()
	{
		return sprintf('%s:%s', $this->getHost(), $this->getPort());
	}

	public function getPort()
	{
		return (int)$this->server['SERVER_PORT'];
	}

	public function getScheme()
	{
		return stripos($this->server['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http';
	}

	public function getScriptName()
	{
		return $this->server['SCRIPT_NAME'];
	}

	public function getRootUri()
	{
		return $this->getScriptName();
	}

	public function getPath()
	{
		return $this->getScriptName() . $this->getPathInfo();
	}

	public function getPathInfo()
	{
		return $this->server['PATH_INFO'];
	}

	public function getResourceUri()
	{
		return $this->getPathInfo();
	}

	public function getUrl()
	{
		$url = $this->getScheme() . '://' . $this->getHost();
		if (($this->getScheme() === 'https' && $this->getPort() !== 443) || ($this->getScheme() === 'http' && $this->getPort() !== 80)) {
			$url .= sprintf(':%s', $this->getPort());
		}
		return $url;
	}

	public function getIp()
	{
		$keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
		foreach ($keys as $key) {
			if (isset($this->server[$key])) {
				return $this->server[$key];
			}
		}
		return $this->server['REMOTE_ADDR'];
	}

	public function getReferrer()
	{
		return $this->header->get('HTTP_REFERER');
	}

	public function getReferer()
	{
		return $this->getReferrer();
	}

	public function getUserAgent()
	{
		return $this->header->get('HTTP_USER_AGENT');
	}

	static public function getRequestMethod()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == self::METHOD_HEAD) {
			ob_start();
			$method = self::METHOD_GET;
		} elseif ($method == self::METHOD_POST) {
			if (function_exists('getallheaders'))
				getallheaders();
			$headers = [];
			foreach ($_SERVER as $name => $value) {
				if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
					$headers[@strtr(ucwords(strtolower(@strtr(substr($name, 5), ['_' => ' ']))), [' ' => '-', 'Http' => 'HTTP'])] = $value;
				}
			}
			if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], [self::METHOD_PUT, self::METHOD_DELETE, self::METHOD_PATCH])) {
				$method = $headers['X-HTTP-Method-Override'];
			}
		}
		return $method;
	}

	static public function checkDomain($domain)
	{
		if (isset($domain) && !empty($domain)) {
			if ($domain !== trim(str_replace('www.', '', $_SERVER['SERVER_NAME']), '/'))
				return false;
			return true;
		}
		return true;
	}

	static public function checkIp($ip)
	{
		if (isset($ip) && !empty($ip)) {
			if (is_array($ip)) {
				if (!in_array($_SERVER['REMOTE_ADDR'], $ip))
					return false;
				return true;
			} else {
				if ($_SERVER['REMOTE_ADDR'] != $ip)
					return false;
				return true;
			}
			return true;
		}
		return true;
	}

	static private function rea($method, $data=null, $db_filter=true)
	{
		$_method = @Filter::filterXSS($method);

		if (isset($data) && !empty($data)) {
			if ($db_filter == true) {
				return @Filter::filterDB($_method[$data]);
			} else {
				return $_method[$data];
			}
		} else {
			return $_method;
		}
	}

	static public function server($data=null, $db_filter=true)
	{
		return self::rea($_SERVER, $data, $db_filter);
	}

	static public function request($data=null, $db_filter=true)
	{
		return self::rea($_REQUEST, $data, $db_filter);
	}

	static public function post($data=null, $db_filter=true)
	{
		return self::rea($_POST, $data, $db_filter);
	}

	static public function get($data=null, $db_filter=true)
	{
		return self::rea($_GET, $data, $db_filter);
	}

	static public function files($data=null)
	{
		return self::rea($_FILES, $data, false);
	}

	static public function filter($data=null, $type='post', $db_filter=true)
	{
		if ($type == 'server') {
			return self::server($data, $db_filter);
		} elseif ($type == 'request') {
			return self::request($data, $db_filter);
		} elseif ($type == 'post') {
			return self::post($data, $db_filter);
		} elseif ($type == 'get') {
			return self::get($data, $db_filter);
		} elseif ($type == 'files') {
			return self::files($data, false);
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