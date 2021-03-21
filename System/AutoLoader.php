<?php

/**
 * AutoLoader
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
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
		if ($class == 'index') return false;

		$file = APPDIR . '/' . $class . '.php';
		$file = @strtr($file, ['\\' => '/', '//' => '/']);

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

		if ($relative_class == 'index') return false;

		$file = $dir . $relative_class . '.php';
		$file = @strtr($file, ['\\' => '/', '//' => '/']);

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
		if ($class == 'index') return false;

		$file = SYSTEMDIR . '/Libraries/' . $class . '.php';
		$file = @strtr($file, ['\\' => '/', '//' => '/']);

		if (file_exists($file)) {
			require_once $file;
			/*if (!class_exists($class)) {
				ep('Class Not Defined in System\Libraries File!', 'Class Name: ' . $class);
			}*/
		}
	}

	private function loadAppLibrary($class)
	{
		if ($class == 'index') return false;

		$file = APPDIR . '/Libraries/' . $class . '.php';
		$file = @strtr($file, ['\\' => '/', '//' => '/']);

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

			if ($file == APPDIR . '/Helpers/index.php') return false;
			require_once $file;

		}, glob(APPDIR . "/Helpers/*.php"));

		array_map(function ($file) {
			
			if ($file == SYSTEMDIR . '/Helpers/index.php') return false;
			require_once $file;

		}, glob(SYSTEMDIR . "/Helpers/*.php"));
	}
}

# Initialize
new AutoLoader;