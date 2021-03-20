<?php

/**
 * AutoLoader
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

class AutoLoader
{

	function __construct()
	{
		spl_autoload_register(array($this, 'loadApp'));
		spl_autoload_register(array($this, 'loadSystemCore'));
		spl_autoload_register(array($this, 'loadSystemLibrary'));
		spl_autoload_register(array($this, 'loadAppLibrary'));
		$this->loadHelpers();
	}

	private function loadApp($class)
	{
		$file = APPDIR . '/' . str_replace(['\\', '//'], '/', $class) . '.php';
		$file = str_replace(['\\', '//'], "/", $file);

		if (file_exists($file)) {
			require_once($file);
		}
	}

	private function loadSystemCore($class)
	{
		$prefix = 'System\\';
		$dir = SYSTEMDIR . '/Core/';
		$len = strlen($prefix);

		if (strncmp($prefix, $class, $len) !== 0) { return; }

		$relative_class = substr($class, $len);
		$file = $dir . str_replace(['\\', '//'], '/', $relative_class) . '.php';
		$file = str_replace(['\\', '//'], "/", $file);

		if (file_exists($file)) {

			require_once $file;

			if (class_exists($class)) {
				new $class;
			}
			/* else {
				ep('Class Not Defined in System\Core File!', 'Class Name: ' . $class);
			}*/
		} else {
			ep('Class Not Found!', 'Class Name: ' . $class);
		}
	}

	private function loadSystemLibrary($class)
	{
		$file = SYSTEMDIR . '/Libraries/' . str_replace(['\\', '//'], '/', $class) . '.php';
		$file = str_replace(['\\', '//'], "/", $file);

		if (file_exists($file)) {

			require_once $file;

			/*if (!class_exists($class)) {
				ep('Class Not Defined in System\Libraries File!', 'Class Name: ' . $class);
			}*/
		}
	}

	private function loadAppLibrary($class)
	{
		$file = APPDIR . '/Libraries/' . str_replace(['\\', '//'], '/', $class) . '.php';
		$file = str_replace(['\\', '//'], "/", $file);

		if (file_exists($file)) {

			require_once $file;

			/*if (!class_exists($class)) {
				ep('Class Not Defined in System\Libraries File!', 'Class Name: ' . $class);
			}*/
		}
	}

	private function loadHelpers()
	{
		array_map(function ($file) {
			require_once $file;
		}, glob(APPDIR . "/Helpers/*.php"));

		array_map(function ($file) {
			require_once $file;
		}, glob(SYSTEMDIR . "/Helpers/*.php"));
	}
}

# Initialize
new AutoLoader;