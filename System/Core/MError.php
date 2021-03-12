<?php

/**
 * MError
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.8
 */

namespace System;

class MError
{

	const COLOR_DANGER = '#f44336';
	const COLOR_WARNING = '#ffeb3b';
	const COLOR_INFO = '#03a9f4';
	const COLOR_SUCCESS = '#4caf50';
	const COLOR_PRIMARY = '#2196f3';

	protected static $color = self::COLOR_PRIMARY, $title;

	protected static function templateTitle($color, $title, $text, $message, $stop, $response_code)
	{
		http_response_code($response_code);
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding("UTF-8");
		}
		echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /><title>' . ($title ?? "System Error") . '</title></head><body>';
		echo '<div style="padding: 15px; border-left: 5px solid ' . $color . '; background: #f8f8f8; margin-bottom: 10px;">';
		echo isset($text) && !empty($text) ? '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 16px; font-weight: 500; color: black;">' . $text . "</div>" : null;
		echo isset($message) && !empty($message) ? '<div style="margin-top: 15px; font-size: 14px; font-family: Consolas, Monaco, Menlo, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, sans-serif; color: #ac0e10;">' . $message . "</div>" : null; 
		echo "</div>";
		echo "</body></html>\n";
		if ($stop === true) exit();
	}

	protected static function template($color, $text, $message, $stop, $response_code)
	{
		http_response_code($response_code);
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding("UTF-8");
		}
		echo '<div style="padding: 15px; border-left: 5px solid ' . $color . '; background: #f8f8f8; margin-bottom: 10px;">';
		echo isset($text) && !empty($text) ? '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 16px; font-weight: 500; color: black;">' . $text . "</div>" : null;
		echo isset($message) && !empty($message) ? '<div style="margin-top: 15px; font-size: 14px; font-family: Consolas, Monaco, Menlo, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, sans-serif; color: #ac0e10;">' . $message . "</div>" : null; 
		echo "</div>\n";
		if ($stop === true) exit();
	}

	static function print($text=null, $message=null, $se=false, $color=null, $title=null, $stop=false, $response_code=200)
	{
		if ($color == null) {
			$color = self::$color;
		} else {
			if ($color == 'danger') {
				$color = self::COLOR_DANGER;
			} elseif ($color == 'warning') {
				$color = self::COLOR_WARNING;
			} elseif ($color == 'info') {
				$color = self::COLOR_INFO;
			}  elseif ($color == 'success') {
				$color = self::COLOR_SUCCESS;
			}  elseif ($color == 'primary') {
				$color = self::COLOR_PRIMARY;
			} else {
				$color = self::COLOR_PRIMARY;
			}
		}

		if ($title == null) {
			$title = self::$title;
		}

		if ($title) {
			self::templateTitle($color, $title, $text, $message, $stop, $response_code);
		} elseif ($se == true) {
			self::templateTitle($color, null, $text, $message, $stop, $response_code);
		} else {
			self::template($color, $text, $message, $stop, $response_code);
		}
	}

	static function set($key, $value)
	{
		if ($key == 'title') {
			self::title($value);
		} elseif ($key == 'color') {
			self::color($value);
		}
		return new self;
	}

	static function title($title)
	{
		self::$title = $title;
		return new self;
	}

	static function color($color)
	{
		if ($color == 'danger') {
			self::$color = self::COLOR_DANGER;
		} elseif ($color == 'warning') {
			self::$color = self::COLOR_WARNING;
		} elseif ($color == 'info') {
			self::$color = self::COLOR_INFO;
		}  elseif ($color == 'success') {
			self::$color = self::COLOR_SUCCESS;
		}  elseif ($color == 'primary') {
			self::$color = self::COLOR_PRIMARY;
		} else {
			self::$color = self::COLOR_PRIMARY;
		}
		return new self;
	}
}

# Initialize
new MError;