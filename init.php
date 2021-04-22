<?php

/**
 * INIT
 *
 * Mirarus BMVC
 * @package BMVC
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

require_once 'vendor/autoload.php';
require_once 'App/config.php';

BMVC\Core\App::namespace([
	'controller' => 'App\Controller\\',
	'model' => 'App\Model\\'
])->Run($_config);