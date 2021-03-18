<?php

/**
 * View
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.8
 */

namespace System;

class View
{

	private static function import($module, $view, $data=[], $return=false)
	{
		$data ? extract($data) : null;
		@$_REQUEST['vd'] = $data;
		$dir = (APPDIR . '/Modules/' . $module);

		if (file_exists($file = $dir . '/View/' . $view . '.blade.php')) { // $view . '.php' -> $view . '.blade.php'

			$blade = new \Jenssegers\Blade\Blade($dir . '/View', $dir . '/Cache');
			return $blade->make($view, $data)->render();

			/*if ($return == false) {
				require_once $file;
			} else {
				ob_start();
				require_once $file;
				$text = ob_get_contents();
				ob_end_clean();
				return $text;
			}*/
		} else {
			MError::title('View Error!')::print('View File Found!', 'View Name: ' . $module . '/' . $view);
		}
	}

	static function load($action, $data=[], $layout=false)
	{
		$module = null;
		$view = null;

		if (is_array($action)) {
			list($module, $view) = $action;
		} elseif (strstr($action, '@')) {
			list($module, $view) = explode('@', $action);
		} elseif (strstr($action, '/')) {
			list($module, $view) = explode('/', $action);
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

								$content = $view != null ? self::import($module, $view, $data, true) : null;
								require_once $file;
							} else {
								MError::title('View Error!')::print('Layout File Found!', 'Layout Name: Main');
							}
						} else {
							MError::title('View Error!')::print('Layout Dir Not Found!');
						}
					} else {
						echo self::import($module, $view, $data, true);
					}
				} else {
					MError::title('View Error!')::print('View Dir Not Found!');
				}
			} else {
				MError::title('View Error!')::print('Module Not Found!', 'Module Name: ' . $module);
			}
		}
	}
}

# Initialize - AutoInitialize
# new View;