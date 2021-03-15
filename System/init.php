<?php

/**
 *
 * Mirarus Basic MVC
 *
 * PHP version 7
 *
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/basic-mvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.5
 *
*/

require_once APPDIR . '/config.php';

require_once 'helper.php';

if (config('general/timezone')) {
	define("TIMEZONE", config('general/timezone'));
}
if (config('general/environment')) {
	define("ENVIRONMENT", config('general/environment'));
}

require_once 'Core/MError.php';
require_once 'Core/Lang.php';
require_once 'Core/Helper.php';
require_once 'Core/Library.php';
require_once 'Core/Log.php';
require_once 'Core/Exception.php';

require_once 'Core/App.php';
require_once 'Core/Model.php';
require_once 'Core/Controller.php';
require_once 'Core/View.php';
require_once 'Core/Route.php';
require_once 'Core/System.php';

require_once APPDIR . '/routes.php';

if (file_exists(ROOTDIR . 'vendor/autoload.php')) {
	require_once ROOTDIR . 'vendor/autoload.php';
}