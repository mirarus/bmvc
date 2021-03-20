<?php

/**
 * Mirarus BMVC
 *
 * PHP version 7
 *
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
*/

$_ROOTDIR = str_replace(['\\', '//'], '/', str_replace(['Public', 'public'], null, realpath(getcwd())));

require_once $_ROOTDIR . 'index.php';