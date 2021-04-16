<?php

/**
 * INIT
 *
 * Mirarus BMVC
 * @package BMVC
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.3
 */

require_once ROOTDIR . '/vendor/autoload.php';

BMVC\Core\App::Run([
	'routes' => APPDIR . '/routes.php',
	'config' => APPDIR . '/config.php'
]);

BMVC\Core\App::$log->info('Run');