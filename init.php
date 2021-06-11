<?php

/**
 * INIT
 *
 * Mirarus BMVC
 * @package BMVC
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.5
 */

require_once 'vendor/autoload.php';
require_once 'App/config.php';

BMVC\Core\App::namespace([
  'controller' => 'Controller',
  'model' => 'Model',
  'view' => 'View'
], 'App\Http');
BMVC\Core\App::init($_config);