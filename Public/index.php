<?php

/**
 * Mirarus BMVC
 *
 * PHP version 7
 *
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.6
*/

define("BMVC_START", microtime(true));

$_ROOTDIR = realpath(getcwd());
$_ROOTDIR = @strtr($_ROOTDIR, ["Public" => null, "public" => null]);
$_ROOTDIR = @substr($_ROOTDIR, -1) == '/' ? @substr($_ROOTDIR, 0, -1) : $_ROOTDIR;
$_ROOTDIR = strtr($_ROOTDIR, ['\\' => '/', '//' => '/']);
define("ROOTDIR", $_ROOTDIR);

define("PUBLICDIR", ROOTDIR . "/Public");
define("APPDIR", ROOTDIR . "/App");
define("SYSTEMDIR", ROOTDIR . "/System");

if (!is_file(ROOTDIR . '/.htaccess')) {
	die('The .htaccess file does not exist.');
} elseif (!is_dir(PUBLICDIR)) {
	die('The Public directory not found.');
} elseif (!is_dir(APPDIR)) {
	die('The App directory not found.');
} elseif (!is_dir(SYSTEMDIR)) {
	die('The System directory not found.');
} elseif (!is_file(SYSTEMDIR . '/init.php')) {
	die('init.php file not found!');
} elseif (!version_compare(PHP_VERSION, '7.3.0', '>=')) {
	die('This software requires a minimum php 7.3.0');
} elseif (!file_exists(ROOTDIR . '/vendor/autoload.php')) {
	die('This software requires composer');
} else {
	require_once SYSTEMDIR . '/init.php';
}