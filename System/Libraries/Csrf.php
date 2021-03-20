<?php

/**
 * Csrf
 *
 * Mirarus BMVC
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

use System\Session;

class Csrf
{

	function __construct($deleteExpired=false)
	{
		if (Session::has('csrf') == null) {
			Session::set('csrf', []);
		}

		if ($deleteExpired) {
			foreach (Session::get('csrf') as $key => $value) {
				if (Session::has('csrf')) {
					if (Session::has('csrf', $key)) {
						if (time() >= Session::get('csrf', $key)['time'] && Session::get('csrf', $key)['token']) {
							$this->delete($key);
						}
					}
				}
			}
		}
	}

	static function generate($time=3600, $length=10)
	{
		if (Session::has('csrf') != null) {

			$token_id = self::random($length);
			$token = self::token();

			Session::set(['csrf' => [$token_id => [
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
			if (Session::has('csrf') != null) {
				if (Session::has('csrf', $key)) {
					if (time() <= Session::get('csrf', $key)['time'] && Session::get('csrf', $key)['token'] == $value) {
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

	private static function delete($key)
	{
		if (Session::has('csrf') != null) {
			if (Session::has('csrf', $key)) {
				Session::delete('csrf', $key);
			}
		}
	}

	private static function token()
	{
		if (Session::has('csrf')) {
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
}

# Initialize
new Csrf(true);