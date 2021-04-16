<?php

/**
 * Csrf
 *
 * Mirarus BMVC
 * @package BMVC\Libs
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.4
 */

namespace BMVC\Libs;

class Csrf
{

	private static $page;

	function __construct()
	{
		self::$page = "b4e27faacd7a7d7ed04aecb30bd29451";
	}

	static function token(int $expiry=3600)
	{
		return self::getToken(null, $expiry);
	}

	static function input(int $expiry=3600)
	{
		$token = self::getToken(null, $expiry);
		if ($token) {
			return '<input type="hidden" name="csrf_token" value="'. $token .'">' . "\r\n";
		}
	}

	static function verify($token=null)
	{
		return self::verifyToken(null, false, $token);
	}

	private static function getToken(string $page=null, int $expiry=3600*24*7)
	{
		$page = $page ? $page : self::$page;

		self::confirmSessionStarted();

		if (empty($page)) {
			return false;
		}

		$token = (self::getSessionToken($page) ?? self::setNewToken($page, $expiry));

		return $token->sessiontoken;
	}

	private static function verifyToken(string $page=null, $removeToken=false, $requestToken=null) : bool
	{
		$page = $page ? $page : self::$page;

		self::confirmSessionStarted();

		$requestToken = ($requestToken ?? $_POST['csrf_token'] ?? null);

		if (empty($page)) {
			return false;
		} else if (empty($requestToken)) {
			return false;
		}

		$token = self::getSessionToken($page);

		if (empty($token) || time() > (int) $token->expiry) {
			self::removeToken($page);
			return false;
		}

		$sessionConfirm = hash_equals($token->sessiontoken, $requestToken);
		$cookieConfirm  = hash_equals($token->cookietoken, self::getCookieToken($page));

		if ($removeToken) {
			self::removeToken($page);
		}

		if ($sessionConfirm || $cookieConfirm) {
			return true;
		}
		return false;
	}

	private static function removeToken(string $page) : bool
	{
		self::confirmSessionStarted();

		if (empty($page)) {
			return false;
		}

		unset($_COOKIE[self::makeCookieName($page)], $_SESSION['csrf_tokens'][$page]);

		return true;
	}

	private static function setNewToken(string $page, int $expiry)
	{
		$token = new \stdClass();
		$token->page   		 = $page;
		$token->expiry 		 = time() + $expiry;
		$token->sessiontoken = base64_encode(random_bytes(32));
		$token->cookietoken  = md5(base64_encode(random_bytes(32)));

		setcookie(self::makeCookieName($page), $token->cookietoken, $token->expiry);

		return $_SESSION['csrf_tokens'][$page] = $token;
	}

	private static function getSessionToken(string $page=null)
	{
		return !empty($_SESSION['csrf_tokens'][$page]) ? $_SESSION['csrf_tokens'][$page] : null;
	}

	private static function getCookieToken(string $page) : string
	{
		$value = self::makeCookieName($page);
		return !empty($_COOKIE[$value]) ? $_COOKIE[$value] : '';
	}

	private static function makeCookieName(string $page) : string
	{
		if (empty($page)) {
			return '';
		}
		return 'csrf_token-' . substr(md5($page), 0, 10);
	}

	private static function confirmSessionStarted() : bool
	{
		if (!isset($_SESSION)) {
			return false;
		}
		return true;
	}
}

# Initialize
new Csrf;

app()->csrf = new Csrf();