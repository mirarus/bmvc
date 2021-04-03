<?php

/**
 * Csrf
 *
 * Mirarus BMVC
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

class Csrf
{

	static function check($rToken=null, string $page='default', $removeToken=false) : bool
	{
		$rToken = ($rToken ?? $_POST['csrf-token'] ?? null);
		$stoken = self::getSessionToken($page);

		if (empty($stoken) || time() > (int) $stoken->expiry) {
			self::removeToken($page);
			return false;
		}

		$sessionConfirm = hash_equals($stoken->sesstoken, $rToken);
		$cookieConfirm  = hash_equals($stoken->cooktoken, self::getCookieToken($page));

		if ($removeToken) {
			self::removeToken($page);
		}
		if ($sessionConfirm && $cookieConfirm) {
			return true;
		}
		return false;
	}

	static function input(int $expiry=1800, string $page='default')
	{
		$token = (self::getSessionToken($page) ?? self::setNewToken($page, $expiry));
		return '<input type="hidden" id="csrf-token" name="csrf-token" value="'. $token->sesstoken .'">' . "\n\r";
	}

	private static function removeToken(string $page) : bool
	{
		unset($_COOKIE[self::makeCookieName($page)], $_SESSION['csrf-tokens'][$page]);
		return true;
	}

	private static function setNewToken(string $page, int $expiry)
	{
		$token 			  = new \stdClass();
		$token->page      = $page;
		$token->expiry    = time() + $expiry;
		$token->sesstoken = base64_encode(random_bytes(32));
		$token->cooktoken = md5(base64_encode(random_bytes(32)));
		
		setcookie(self::makeCookieName($page), $token->cooktoken, $token->expiry);
		
		return $_SESSION['csrf-tokens'][$page] = $token;
	}

	private static function getSessionToken(string $page)
	{
		return !empty($_SESSION['csrf-tokens'][$page]) ? $_SESSION['csrf-tokens'][$page] : null;
	}

	private static function getCookieToken(string $page) : string
	{
		$value = self::makeCookieName($page);
		return !empty($_COOKIE[$value]) ? $_COOKIE[$value] : '';
	}

	private static function makeCookieName(string $page) : string
	{
		return 'csrf-token-' . substr(md5($page), 0, 10);
	}
}

# Initialize
new Csrf;