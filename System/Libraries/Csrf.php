<?php

/**
 * Csrf
 *
 * Mirarus BMVC
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

class Csrf
{

	function __construct($deleteExpired=true)
	{
		if (self::session_get('csrf') == null) {
			self::session_set('csrf', []);
		}

		if ($deleteExpired) {
			self::deleteExpired();
		}
	}

	static function generate($time=3600, $length=10)
	{
		if (self::session_get('csrf') != null) {

			$token_id = self::random($length);
			$token = self::token();

			self::session_set(['csrf' => [$token_id => [
				"token" => $token, 
				"time" => (time() + $time)
			]]]);
			
			return [
				"key" => $token_id, 
				"token" => $token
			];
		}
	}

	static function check($data=[])
	{
		foreach (@$data as $key => $value) {
			if (self::session_get('csrf') != null) {
				if (self::session_get('csrf', $key)) {
					if (time() <= self::session_get('csrf', $key)['time'] && self::session_get('csrf', $key)['token'] == $value) {
						return true;
					}
					self::delete($key);
					return false;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	static function input($time=3600)
	{
		$csrf = self::generate($time);
		if ($csrf != null) {
			return '<input type="hidden" name="' . $csrf["key"] . '" value="' . $csrf["token"] . '">' . "\n\r";
		}
	}

	static function deleteExpired()
	{
		foreach (self::session_get('csrf') as $key => $value) {
			if (self::session_get('csrf')) {
				if (self::session_get('csrf', $key)) {
					if (time() >= self::session_get('csrf', $key)['time'] && self::session_get('csrf', $key)['token']) {
						self::delete($key);
					}
				}
			}
		}
	}

	private static function delete($key)
	{
		if (self::session_get('csrf') != null) {
			if (self::session_get('csrf', $key)) {
				self::session_del('csrf', $key);
			}
		}
	}

	private static function token()
	{
		if (self::session_get('csrf')) {
			return base64_encode(hash('sha256', self::random(500)));
		}
	}

	private static function random($len)
	{
		if (function_exists('openssl_random_pseudo_bytes')) {
			$byteLen = intval(($len / 2) + 1);
			$return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
		} elseif (@is_readable('/dev/urandom')) {
			$f=fopen('/dev/urandom', 'r');
			$urandom = fread($f, $len);
			fclose($f);
			$return = '';
		}

		if (empty($return)) {
			for ($i=0; $i < $len; ++$i) {
				if (!isset($urandom)) {
					if ($i%2 == 0) {
						mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
					}
					$rand = 48 + mt_rand()%64;
				} else {
					$rand=48 + ord($urandom[$i])%64;
				}

				if ($rand > 57) $rand += 7;
				if ($rand > 90) $rand += 6;
				if ($rand == 123) $rand = 52;
				if ($rand == 124) $rand = 53;
				$return .= chr($rand);
			}
		}
		return $return;
	}

	private static function session_get($storage, $child=false)
	{
		if ($child == false) {
			if (isset($_SESSION[md5($storage)])) {
				return $_SESSION[md5($storage)];
			}
		} else {
			if (isset($_SESSION[md5($storage)][$child])) {
				return $_SESSION[md5($storage)][$child];
			}
		}
	}

	private static function session_set($storage, $content=null)
	{
		if (is_array($storage)) {
			foreach ($storage as $key => $value) {
				$_SESSION[md5($key)] = $value;
			}
		} else {
			$_SESSION[md5($storage)] = $content;
		}
	}

	private static function session_del($storage, $child)
	{
		unset($_SESSION[md5($storage)][$child]);
	}
}

# Initialize
new Csrf;