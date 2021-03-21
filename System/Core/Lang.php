<?php

/**
 * Lang
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 3.6
 */

namespace System;

class Lang
{

	private static $lang_dir = (APPDIR . '/Languages/');
	private static $lang = 'en';

	function __construct()
	{
		if (config('default/lang') != null) {
			self::$lang = config('default/lang');
		}
	}

	private static function init($text)
	{
		$current_lang = self::get();

		if (_dir(self::$lang_dir)) {
			
			if ($current_lang == 'index') return false;

			if (file_exists($file = self::$lang_dir . $current_lang . '.php')) {
				if ($text != null) {

					$_lang = [];
					include $file;
					if (@$_lang[$text] != null) {
						return $_lang[$text];
					} else {
						MError::print('Language Not Found!', 'Language Text: ' . $text);
					}
				} else {
					MError::print('Language Not Found!', 'Language Text: ' . $text);
				}
				MError::print('Language Not Found!', 'Language Name: ' . $current_lang);
			}
		} else {
			MError::print('Language Dir Not Found!');
		}
	}

	private static function _get_lang($text, $return=true, $replace=null)
	{
		if ($return == true) {
			if ($replace != null) {
				if (is_array($replace)) {
					return vsprintf(self::init($text), $replace);
				} else {
					return sprintf(self::init($text), $replace);
				}
			} else {
				return self::init($text);
			}
		} else {
			if ($replace != null) {
				if (is_array($replace)) {
					vprintf(self::init($text), $replace);
				} else {
					printf(self::init($text), $replace);
				}
			} else {
				echo self::init($text);
			}
		}
	}

	static function get_langs()
	{
		if (_dir(self::$lang_dir)) {
			
			$files = [];
			foreach (glob(self::$lang_dir . '*.php') as $file) {

				if ($file == self::$lang_dir . 'index.php') return false;

				$_lang_ = false;
				include $file;
				if ($_lang_ == true) {
					$files[] = str_replace([self::$lang_dir, '.php'], '', $file);
				}
			}
			return $files;
		} else {
			MError::print('Language Dir Not Found!');
		}
	}

	static function get_lang()
	{
		return self::get();
	}

	static function info($lang, $par=null)
	{
		if (_dir(self::$lang_dir)) {

			if ($lang == 'index') return false;

			if (file_exists($file = self::$lang_dir . $lang . '.php')) {

				$data = [];
				include $file;

				$data = [
					'init' => @$_lang_,
					'name' => @$_lang_name,
					'name_global' => @$_lang_name_global
				];
				if ($data['init'] != null && $data['name'] != null && $data['name_global'] != null) {
					if ($par != null) {
						return $data[$par];
					} else {
						return $data;
					}
				}
			} else {
				MError::print('Language Not Found!', 'Language Name: ' . $lang);
			}
		} else {
			MError::print('Language Dir Not Found!');
		}
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
			$lang = self::get();
		} if (in_array($lang, self::get_langs())) {
			$_SESSION[md5('language')] = $lang;
		}
	}

	static function __($text, $replace=null)
	{
		self::_get_lang($text, false, $replace);
	}

	static function ___($text, $replace=null)
	{
		return self::_get_lang($text, true, $replace);
	}
}

# Initialize - AutoInitialize
# new Lang;