<?php

if (!function_exists('pr')) {
	function pr($data, $stop=false) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		if ($stop === true) {
			die();
		}
	}
}

if (!function_exists('config')) {
	function config($par) {
		if ($par != null) {
			include APPDIR . '/config.php'; 

			if (is_array($par)) {
				$vars = $par;
			} elseif (strstr($par, '@')) {
				$vars = explode('@', $par);
			} elseif (strstr($par, '/')) {
				$vars = explode('/', $par);
			} else {
				$vars = [$par];
			}

			if ($vars != null) {
				foreach ($vars as $key) {
					if (isset($_config[$key])) {	
						$_config = $_config[$key];
					}
				}
				return $_config;
			}
		}
	}
}

if (!function_exists('get_404')) {
	function get_404() {
		System\Route::get_404();
	}
}

if (!function_exists('set_404')) {
	function set_404($callback) {
		if (is_nem($callback)) {
			System\Route::set_404($callback);
		}
	}
}

if (!function_exists('is_cli')) {
	function is_cli() {
		if (defined('STDIN')) {
			return true;
		}
		if (php_sapi_name() === 'cli') {
			return true;
		}
		if (array_key_exists('SHELL', $_ENV)) {
			return true;
		}
		if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
			return true;
		} 
		if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
			return true;
		}
		return false;
	}
}

if (!function_exists('_dir')) {
	function _dir($dir) {
		if (is_dir($dir) && opendir($dir)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('loader_mmvc')) {
	function loader_mmvc($class) {
		$file = APPDIR . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class) . '.php';
		$file = str_replace("//", "/", $file);

		if (file_exists($file)) {
			require_once($file);
		} else {
			$file = APPDIR . '/Libraries' . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class) . '.php';
			if (file_exists($file)) {
				require_once($file);
			}
		}
	}
}