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

if (!function_exists('ep')) {
	function ep($text, $message, $html=false, $title=null, $color=null, $stop=false, $response_code=200)
	{
		$colors = [
			'danger' => '244 67 54',
			'warning' => '255 235 59',
			'info' => '3 169 244',
			'success' => '76 175 80',
			'primary' => '33 150 243'
		];

		if ($color == null) {
			$color = $colors['primary'];
		} else {
			$color = isset($colors[$color]) ? $colors[$color] : $colors['primary'];
		}

		http_response_code($response_code);
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding("UTF-8");
		}
		echo $html == true ? '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /><title>' . ($title ? $title : "System Error") . '</title></head><body>' : null;
		echo '<div style="padding: 15px; border-left: 5px solid rgb(' . $color . ' / 80%); border-top: 5px solid rgb(' . $color . ' / 60%); background: #f8f8f8; margin-bottom: 10px;border-radius: 5px 5px 0 3px;">';
		echo isset($text) && !empty($text) ? '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 16px; font-weight: 500; color: black;">' . $text . "</div>" : null;
		echo isset($message) && !empty($message) ? '<div style="margin-top: 15px; font-size: 14px; font-family: Consolas, Monaco, Menlo, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, sans-serif; color: #ac0e10;">' . $message . "</div>" : null; 
		echo "</div>";
		echo $html == true ? "</body></html>\n" : "\n";
		if ($stop === true) exit();
	}
}

if (!function_exists('_ob')) {
	function _ob($file) {
		ob_start();
		require_once $file;
		$ob_content = ob_get_contents();
		ob_end_clean();
		return $ob_content;
	}
}