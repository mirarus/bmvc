<?php

/**
 * INIT
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.7
 */

if (file_exists(ROOTDIR . '/vendor/autoload.php')) {
	require_once ROOTDIR . '/vendor/autoload.php';
}

require_once APPDIR . '/config.php';

require_once 'helper.php';
require_once 'MException.php';
require_once 'AutoLoader.php';

if (config('general/timezone')) {
	define("TIMEZONE", config('general/timezone'));
}

if (config('general/environment')) {
	define("ENVIRONMENT", config('general/environment'));
}

require_once APPDIR . '/routes.php';