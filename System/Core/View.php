<?php

/**
 * View
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.5
 */

namespace BMVC\Core;

class View
{

	private static $instance;

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private static function import($module, $view, $data=[], &$return=null)
	{
		@$_REQUEST['vd'] = $data;
		$dir = (APPDIR . '/Modules/' . $module);
		$viewDir = ($dir . '/View');
		$cacheDir = ($dir . '/Cache');

		if (config('general/view/blade') == false) {

			$data ? extract($data) : null;

			if (file_exists($file = $viewDir . '/' . $view . '.php')) {

				if (config('general/view/cache') == true) {
					
					$cacheFile = self::cache($view, $file, $cacheDir);

					ob_start();
					require_once $cacheFile;
					$ob_content = ob_get_contents();
					ob_end_clean();
					return $return = $ob_content;
				} else {

					ob_start();
					require_once $file;
					$ob_content = ob_get_contents();
					ob_end_clean();
					return $return = $ob_content;
				}
			} else {
				MError::title('View Error!')::stop(true)::print('View File Found!', 'View Name: ' . $module . '/' . $view);
			}
		} else {

			if (file_exists($file = $dir . '/View/' . $view . '.blade.php')) {

				$blade = new \Jenssegers\Blade\Blade($viewDir, $cacheDir);
				return $blade->make($view, $data)->render();
			} else {
				MError::title('Blade View Error!')::stop(true)::print('Blade View File Found!', 'Blade View Name: ' . $module . '/' . $view);
			}
		}
	}

	static function load($action, $data=[], $layout=false, &$return=null)
	{
		$module = null;
		$view = null;

		if (is_array($action)) {
			$module = array_shift($action);
			$view = implode('/', $action);
		} elseif (strstr($action, '@')) {
			$action = explode('@', $action);
			$module = array_shift($action);
			$view = implode('/', $action);
		} elseif (strstr($action, '/')) {
			$action = explode('/', $action);
			$module = array_shift($action);
			$view = implode('/', $action);
		} else {
			$module = config('default/module');
			$view = $action;
		}

		if ($module != null && $view != null) {

			if (_dir(APPDIR . '/Modules/' . $module)) {
				if (_dir(APPDIR . '/Modules/' . $module . '/View')) {

					if ($layout == true) {

						if (_dir(APPDIR . '/Modules/' . $module . '/Layout')) {
							if (file_exists($file = APPDIR . '/Modules/' . $module . '/Layout/Main.php')) {

								$content = $view != null ? self::import($module, $view, $data, $return) : null;
								require_once $file;
							} else {
								MError::title('View Error!')::stop(true)::print('Layout File Found!', 'Layout Name: Main');
							}
						} else {
							MError::title('View Error!')::stop(true)::print('Layout Dir Not Found!');
						}
					} else {
						echo self::import($module, $view, $data, $return);
					}
				} else {
					MError::title('View Error!')::stop(true)::print('View Dir Not Found!');
				}
			} else {
				MError::title('View Error!')::stop(true)::print('Module Not Found!', 'Module Name: ' . $module);
			}
		}
	}

	static private function cache($fileName, $fileContent, $cacheDir)
	{
		$file = ($cacheDir . '/' . md5($fileName) . '.php');

		if (config('general/view/cacheExpire') != null) {
			$cacheExpire = config('general/view/cacheExpire');
		} else {
			$cacheExpire = 120;
		}
		
		if (!file_exists($file) || (filemtime($file) < (time() - $cacheExpire))) {

			$content = file_get_contents($fileContent);
			$signature = "\n<?php /** FILE: " . $fileContent . " - DATE: " . date(DATE_RFC822) ." - EXPIRE: " . date(DATE_RFC822, time() + $cacheExpire) . " */ ?>";
			$content = $content . $signature;
			file_put_contents($file, $content, LOCK_EX);
		}
		return $file;
	}

	function __call($method, $args)
	{
		return isset($this->{$method}) && is_callable($this->{$method}) ? call_user_func_array($this->{$method}, $args) : null;
	}

	static function __callStatic($method, $args)
	{
		return isset(self::$method) && is_callable(self::$method) ? call_user_func_array(self::$method, $args) : null;
	}

	function __set($key, $value)
	{
		$this->{$key} = $value instanceof \Closure ? $value->bindTo($this) : $value;
	}
}

# Initialize - AutoInitialize
# new View;