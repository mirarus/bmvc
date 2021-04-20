<?php

/**
 * App
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 4.2
 */

namespace BMVC\Core;

use BMVC\Libs\MError;
use Whoops\Run as WRun;
use Whoops\Handler\PrettyPageHandler as WPrettyPageHandler;
use Monolog\Logger as MlLogger;
use Monolog\Handler\StreamHandler as MlStreamHandler;
use Monolog\Formatter\LineFormatter as MlLineFormatter;

final class App
{

	private static $init = false;
	public static $whoops;
	public static $log;
	public static $namespaces = [
		'controller' => 'App\Controller\\',
		'model'      => 'App\Model\\'
	];

	public function __construct(array $array = [])
	{
		self::Run($array);
	}

	public static function Run(array $array = []): void
	{
		if (self::$init == true) {
			return;
		}
		
		self::initWhoops();
		self::initMonolog();
		self::initError();
		self::initSession();
		self::initHeader();
		self::init($array);
		self::initAutoLoader();
		self::initialize();
		self::initRoute();

		self::$init = true;
	}

	private static function initWhoops(): void
	{
		$whoops = new WRun;
		$whoops->pushHandler(new WPrettyPageHandler);
		$whoops->register();
		self::$whoops = $whoops;
	}
	
	private static function initMonolog(): void
	{
		$log = new MlLogger('BMVC');
		$stream = new MlStreamHandler(SYSTEMDIR . '/Logs/app.log');
		$formatter = new MlLineFormatter(MlLineFormatter::SIMPLE_FORMAT, MlLineFormatter::SIMPLE_DATE);
		$formatter->includeStacktraces(true);
		$stream->setFormatter($formatter);
		$log->pushHandler($stream);
		self::$log = $log;
	}

	private static function initError(): void
	{
		self::$whoops->pushHandler(function ($exception, $inspector, $run) {
			self::$log->error($exception);
		});
	}
	
	private static function initSession(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE || session_id() === null) {
			@ini_set('session.cookie_httponly', 1);
			@ini_set('session.use_only_cookies', 1);
			@ini_set('session.gc_maxlifetime', 3600 * 24);
			@session_set_cookie_params(3600 * 24);
			
			@session_name("BMVC");
			@session_start();
		}
	}

	private static function initHeader(): void
	{
		@header("X-Frame-Options: sameorigin");
		@header("Strict-Transport-Security: max-age=15552000; preload");
		@header("X-Powered-By: PHP/BMVC");
	}

	private static function init(array $array = []): void
	{
		if (isset($array['routes'])) {
			require_once $array['routes'];
		}

		if (isset($array['config'])) {
			require_once $array['config'];
		}

		if (function_exists('mb_internal_encoding')) {
			@mb_internal_encoding("UTF-8");
		}

		if (is_cli()) {
			die("Cli Not Available, Browser Only.");
		}

		@date_default_timezone_set(TIMEZONE);

		switch (ENVIRONMENT) {
			case 'development':
			@error_reporting(-1);
			@ini_set('display_errors', 0);
			break;
			case 'testing':
			case 'production':
			@ini_set('display_errors', 0);
			@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
			break;
			default:
			@header('HTTP/1.1 503 Service Unavailable.', true, 503);
			echo 'The application environment is not set correctly.';
			exit(1);
		}
	}

	private static function initAutoLoader(): void
	{
		spl_autoload_register(function ($class) {
			if ($class == 'index') return false;

			$file = APPDIR . '/Libraries/' . $class . '.php';
			$file = @strtr($file, ['\\' => '/', '//' => '/']);

			if (file_exists($file)) {
				require_once $file;
			}
		});

		array_map(function ($file) {
			if ($file == APPDIR . '/Helpers/index.php') return false;
			require_once $file;
		}, glob(APPDIR . "/Helpers/*.php"));

		array_map(function ($file) {
			if ($file == SYSTEMDIR . '/Helpers/index.php') return false;
			require_once $file;
		}, glob(SYSTEMDIR . "/Helpers/*.php"));
	}

	private static function initialize(): void
	{
		if (config('initialize') !== null) {
			foreach (config('initialize') as $init) {
				new $init;
			}
		}
	}

	private static function initRoute()
	{
		$route = Route::Run();

		$action = $route['action'];
		$params = $route['params'];
		$_url   = $route['_url'];

		if (is_callable($action)) {
			return call_user_func_array($action, array_values($params));
		} else {
			if (!isset($_url) && empty($_url)) {
				$action = [
					config('default/module'), 
					config('default/controller'), 
					config('default/method')
				];
			}
			if (is_dir(APPDIR . '/Modules/') && opendir(APPDIR . '/Modules/')) {
				@Controller::call(@$action, @$params);
			} else {
				MError::title('Module Error!')::print('Modules Dir Not Found!', null, true);
			}
		}
	}
}

define("BMVC_END", microtime(true));
define("BMVC_LOAD", number_format((BMVC_END - BMVC_START), 5));