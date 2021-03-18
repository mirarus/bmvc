<?php

/**
 *
 * Mirarus BMVC
 *
 * PHP version 7
 *
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/basic-mvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 *
*/

define("BMVC_START", microtime(true));
define("ROOTDIR", str_replace("System", "", realpath(getcwd())));
define("PUBLICDIR", ROOTDIR . "/Public");
define("APPDIR", ROOTDIR . "/App");
define("SYSTEMDIR", ROOTDIR . "/System");

if (!is_file(ROOTDIR . '/.htaccess')) {
	exit('The .htaccess file does not exist.');
} elseif (!is_dir(PUBLICDIR)) {
	exit('The Public directory not found.');
} elseif (!is_dir(APPDIR)) {
	exit('The App directory not found.');
} elseif (!is_dir(SYSTEMDIR)) {
	exit('The System directory not found.');
} elseif (!is_file(SYSTEMDIR . '/init.php')) {
	die('init.php file not found!');
} elseif (!version_compare(PHP_VERSION, '7.3.0', '>=')) {
	die('This software requires a minimum php 7.3.0');
} else {

	require_once SYSTEMDIR . '/init.php';

	if (!defined("TIMEZONE")) {
		define("TIMEZONE", "Europe/Istanbul");
	}

	if (!defined("ENVIRONMENT")) {
		define("ENVIRONMENT", "production");
	}

	if (!defined("URL")) {
		$https = @$_SERVER['HTTPS'];
		$fport = @$_SERVER['HTTP_X_FORWARDED_PORT'] ? $_SERVER['HTTP_X_FORWARDED_PORT'] : null;
		$url = (((isset($https) && $https == 'on' || $_SERVER['SERVER_PORT'] == 443 || @$fport == 443) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
		$url = str_replace('\\', '/', $url . dirname($_SERVER['PHP_SELF']));
		$url = str_replace(['/Public', '/public'], null, $url) . '/';
		define("URL", $url);
	}

	\System\App::Run();
}