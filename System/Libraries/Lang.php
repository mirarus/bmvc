<?php

/**
 * Lang
 *
 * Mirarus BMVC
 * @package BMVC\Libs
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 4.9
 */

namespace BMVC\Libs;
use BMVC\Core\Route;

class Lang
{

	private static $langs = [];
	static $lang = 'en';
	private static $current_lang = 'en';
	private static $lang_dir = (APPDIR . '/Languages/');

	function __construct()
	{
		$_lang = config('default/lang');

		if ($_lang != null) {

			if (is_array($_lang)) {

				$func = array_shift($_lang);
				$_lang = call_user_func_array($func, $_lang);

				if ($_lang) {
					self::$lang = $_lang;
				}
			} else {
				self::$lang = $_lang;
			}
		}

		self::$langs = self::_get_langs();
		self::$current_lang = self::get();

		self::routes();
	}

	private static function routes()
	{
		Route::prefix('lang')::group(function() {

			Route::match(['GET', 'POST'], 'set/{lowercase}', function($lang) {
				self::set($lang);
				if (check_method('GET')) {
					redirect(url());
				}
			});

			Route::match(['GET', 'POST'], 'get/{all}', function($url) {
				$par  = explode('/', $url);
				$text = array_shift($par);

				if (isset($par[0]) && $par[0] == "true") {
					self::___($text, Request::request('replace'));
				} else {
					self::__($text, Request::request('replace'));
				}
			});
		});
	}

	private static function _init($text, $return=true, $replace=null)
	{
		if ($return == true) {
			if ($replace != null) {
				if (is_array($replace)) {
					return vsprintf(self::_get_text($text), $replace);
				} else {
					return sprintf(self::_get_text($text), $replace);
				}
			} else {
				return self::_get_text($text);
			}
		} else {
			if ($replace != null) {
				if (is_array($replace)) {
					vprintf(self::_get_text($text), $replace);
				} else {
					printf(self::_get_text($text), $replace);
				}
			} else {
				echo self::_get_text($text);
			}
		}
	}

	private static function _get_text($text)
	{
		if ($text != null) {

			if (_dir(self::$lang_dir)) {

				if (self::$current_lang == 'index') return false;

				$_config = false;

				if (file_exists($file = self::$lang_dir . 'config.php')) {

					$inc_file = include ($file);

					if (is_array($inc_file) && !empty($inc_file)) {
						
						$_config = true;
						$_lang = $inc_file[self::$current_lang];

						if (isset($_lang)) {
							$_lang = $_lang['langs'];
							if (isset($_lang[$text])) {
								return $_lang[$text];
							} else {
								return $text;
								// MError::print('Language Not Found!', 'Language Text: ' . $text);
							}
						} else {
							MError::print('Language Not Found!', 'Language Name: ' . self::$current_lang);
						}
					}
				}

				if ($_config == false) {
					if (file_exists($file = self::$lang_dir . self::$current_lang . '.php')) {

						$_lang = [];
						include $file;
						if (isset($_lang[$text])) {
							return $_lang[$text];
						} else {
							return $text;
							// MError::print('Language Not Found!', 'Language Text: ' . $text);
						}
					} else {
						MError::print('Language Not Found!', 'Language Name: ' . self::$current_lang);
					}
				}
			} else {
				MError::print('Language Dir Not Found!');
			}
		} else {
			MError::print('Language Not Found!', 'Language Text: ' . $text);
		}
	}

	private static function _get_langs()
	{
		if (_dir(self::$lang_dir)) {

			$_config = false;

			if (file_exists($file = self::$lang_dir . 'config.php')) {

				$inc_file = include ($file);
				
				if (is_array($inc_file) && !empty($inc_file)) {
					
					$_config = true;

					if (array_keys($inc_file) == 'index') return false;
					return array_keys($inc_file);
				}
			}

			if ($_config == false) {

				$files = [];
				foreach (glob(self::$lang_dir . '*.php') as $file) {

					if ($file == self::$lang_dir . 'index.php') return false;

					$_lang = [];
					include $file;
					if ($_lang != null) {
						$files[] = str_replace([self::$lang_dir, '.php'], '', $file);
					}
				}
				return $files;
			}
		} else {
			MError::print('Language Dir Not Found!');
		}
	}

	private static function _get_lang_info($_xlang, $par=null)
	{
		if (_dir(self::$lang_dir)) {

			if ($_xlang == 'index') return false;
			
			$_config = false;
			$_data = [];
			$_lang = [];

			if (file_exists($file = self::$lang_dir . 'config.php')) {

				$inc_file = include ($file);

				if (is_array($inc_file) && !empty($inc_file)) {

					$_lang_ = $inc_file[$_xlang];

					if (isset($_lang_) && isset($_lang_['info'])) {

						$_config = true;

						$_lang = $_lang_['langs'];

						$_data = [
							'code' => @$_xlang,
							'name-global' => @$_lang_['info']['name-global'],
							'name-local' => @$_lang_['info']['name-local']
						];
					} else {
						MError::print('Language Not Found!', 'Language Name: ' . $_xlang);
					}
				}
			}

			if ($_config == false) {
				if (file_exists($file = self::$lang_dir . $_xlang . '.php')) {

					include $file;

					$_data = [
						'code' => @$_xlang,
						'name-global' => @$_lang_name[0],
						'name-local' => @$_lang_name[1]
					];
				} else {
					MError::print('Language Not Found!', 'Language Name: ' . $_xlang);
				}
			}

			if (@$_lang != null && @$_data['code'] != null && @$_data['name-global'] != null && @$_data['name-local'] != null) {
				if ($par != null) {
					return $_data[$par];
				} else {
					return $_data;
				}
			}
		} else {
			MError::print('Language Dir Not Found!');
		}
	}

	static function get_lang($lang)
	{
		$info = self::_get_lang_info($lang);
		$current = self::$current_lang == $lang ? true : false;
		$name = $current ? $info['name-local'] : $info['name-global'];
		$url = url('lang/set/' . $info['code']);

		if ($info == null) return false;

		return [
			'info' => $info,
			'name' => $name,
			'url' => $url,
			'current' => $current
		];
	}

	static function get_langs()
	{
		$_langs = [];
		foreach (self::$langs as $lang) {
			$_langs[$lang] = self::get_lang($lang);
		}
		return $_langs;
	}

	static function get()
	{
		if (isset($_SESSION[md5('language')])) {
			return $_SESSION[md5('language')];
		}
		$_SESSION[md5('language')] = self::$lang;
		return self::$lang;
	}

	static function set($lang='')
	{
		if (!is_string($lang)) {
			return false;
		} if (empty($lang)) {
			$lang = self::$current_lang;
		} if (in_array($lang, self::$langs)) {
			$_SESSION[md5('language')] = $lang;
		}
	}

	static function __($text, $replace=null)
	{
		self::_init($text, false, $replace);
	}

	static function ___($text, $replace=null)
	{
		return self::_init($text, true, $replace);
	}
}

# Initialize - AutoInitialize
# new Lang;