<?php

/**
 * Session
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.3
 */

namespace BMVC\Core;

class Session
{

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
}

# Initialize - AutoInitialize
# new Session;