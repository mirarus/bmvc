<?php

/**
 * INIT
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.2
 */

require_once ROOTDIR . '/vendor/autoload.php';

require_once 'helper.php';
require_once 'variables.php';
require_once 'Kernel.php';
require_once 'AutoLoader.php';

require_once APPDIR . '/routes.php';
require_once APPDIR . '/config.php';

System\App::Run();