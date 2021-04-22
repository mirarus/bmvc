<?php

/**
 * Mirarus BMVC
 *
 * PHP version 7
 *
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.0
*/

define("ROOTDIR", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("PUBLICDIR", ROOTDIR . "Public");
define("APPDIR", ROOTDIR . "App");

if (!is_file(ROOTDIR . '.htaccess')) {
	die('The .htaccess file does not exist.');
} elseif (!is_dir(APPDIR)) {
	die('The App directory not found.');
} elseif (!is_file(ROOTDIR . 'init.php')) {
	die('init.php file not found!');
} elseif (!file_exists(ROOTDIR . 'vendor/autoload.php')) {
	die('This software requires composer');
} else {
	require_once ROOTDIR . 'init.php';
}