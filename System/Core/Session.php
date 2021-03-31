<?php

/**
 * Session
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

namespace System;

class Session
{

	private static $instance;
	
	function __construct()
	{
		//@ini_set('session.cookie_httponly', 1);
		//@ini_set('session.use_only_cookies', 1);
		//@ini_set('session.gc_maxlifetime', 3600);
		//@session_set_cookie_params(3600);
		
		/*if (session_status() !== PHP_SESSION_ACTIVE || session_id() === "") {
			session_name("BMVC-MMVC");
			session_start();
			
			self::set(md5('session_hash'), self::generateHash());
		} else {
			if (self::get(md5('session_hash')) != self::generateHash()) {
				self::destroy();
			}
		}*/
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	static function set($storage, $content=null)
	{
		if (is_array($storage)) {
			foreach ($storage as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else {
			$_SESSION[$storage] = $content;
		}
	}

	static function get($storage=null, $child=false)
	{
		if (is_null($storage)) {
			return $_SESSION;
		}
		
		return self::has($storage, $child);
	}

	static function has($storage, $child=false)
	{
		if ($child == false) {
			if (isset($_SESSION[$storage])) {
				return $_SESSION[$storage];
			}
		} else {
			if (isset($_SESSION[$storage][$child])) {
				return $_SESSION[$storage][$child];
			}
		}
	}

	static function delete($storage=null, $child=false)
	{
		if (is_null($storage)) {
			session_unset();
		} else {
			if ($child == false) {
				if (isset($_SESSION[$storage])) {
					unset($_SESSION[$storage]);
				}
			} else {
				if (isset($_SESSION[$storage][$child])) {
					unset($_SESSION[$storage][$child]);
				}
			}
		}
	}

	static function destroy()
	{
		session_destroy();
	}

	private static function generateHash()
	{
		if (array_key_exists('REMOTE_ADDR', $_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			return md5(sha1(md5($_SERVER['REMOTE_ADDR'] . 'u2LMq1h4oUV0ohL9svqedoB5LebiIE4z' . $_SERVER['HTTP_USER_AGENT'])));
		}
		return md5(sha1(md5('u2LMq1h4oUV0ohL9svqedoB5LebiIE4z')));
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
# new Session;