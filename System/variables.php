<?php

/**
 * VERIABLES
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

if (config('general/timezone')) {
	define("TIMEZONE", config('general/timezone'));
} else {
	define("TIMEZONE", "Europe/Istanbul");
}
if (config('general/environment')) {
	define("ENVIRONMENT", config('general/environment'));
} else {
	define("ENVIRONMENT", "production");
}
if (!defined("URL")) {
	define("URL", base_url());
}