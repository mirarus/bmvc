<?php

/**
 * Kernel
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

use Whoops\Run as WhoopsRun;
use Whoops\Handler\PrettyPageHandler as WhoopsPrettyPageHandler;

final class Kernel
{

	/**
	 * @var boolean
	 */
	private static $init = false;

	public function __construct()
	{
		self::Run();
	}

	public static function Run(): void
	{
		if (self::$init == true) {
			return;
		}

		self::initSession();
		self::initWhoops();

		self::$init = true;
	}

	private static function initSession(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE || session_id() === null) {
			@ini_set('session.cookie_httponly', 1);
			@ini_set('session.use_only_cookies', 1);
			@ini_set('session.gc_maxlifetime', 3600 * 24);
			@session_set_cookie_params(3600 * 24);
			
			session_name("BMVC-MMVC");
			session_start();
		}
	}

	private static function initWhoops(): void
	{
		$whoops = new WhoopsRun;
		$whoops->pushHandler(new WhoopsPrettyPageHandler);
		$whoops->register();
	}
}

# Initialize
new Kernel;