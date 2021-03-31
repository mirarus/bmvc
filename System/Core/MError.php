<?php

/**
 * MError
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 2.1
 */

namespace System;

class MError
{

	private static $instance;
	private static $getInstance;
	protected static $html;
	protected static $title;
	protected static $color;
	protected static $colors = [
		'danger' => '244 67 54',
		'warning' => '255 235 59',
		'info' => '3 169 244',
		'success' => '76 175 80',
		'primary' => '33 150 243'
	];

	function __construct()
	{
		self::reset();
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	static function getInstance(): self
	{
		if (!self::$getInstance) {
			self::$getInstance = new MErrorA;
		}
		return self::$getInstance;
	}

	private static function reset(): void
	{
		self::$html = false;
		self::$title = "System Error";
		self::$color = self::$colors['primary'];
	}

	private static function template($text, $message, $html=false, $title=null, $color=null, $stop=false, $response_code=200): void
	{
		http_response_code($response_code);
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding("UTF-8");
		}
		echo $html == true ? '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /><title>' . $title . '</title></head><body>' : null;
		echo '<div style="padding: 15px; border-left: 5px solid rgb(' . $color . ' / 80%); border-top: 5px solid rgb(' . $color . ' / 60%); background: #f8f8f8; margin-bottom: 10px; border-radius: 5px 5px 0 3px;">';
		echo isset($text) && !empty($text) ? '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 16px; font-weight: 500; color: black;">' . $text . "</div>" : null;
		echo isset($message) && !empty($message) ? '<div style="margin-top: 15px; font-size: 14px; font-family: Consolas, Monaco, Menlo, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, sans-serif; color: #ac0e10;">' . $message . "</div>" : null; 
		echo "</div>";
		echo $html == true ? "</body></html>\n" : "\n";
		if ($stop === true) exit();
	}

	static function print($text, $message=null, $html=false, $title=null, $color=null, $stop=false, $response_code=200): void
	{
		if ($color == null) {
			$color = self::$color;
		} else {
			$color = isset(self::$colors[$color]) ? self::$colors[$color] : self::$colors['primary'];
		}

		if ((self::$html == true ? self::$html : $html) == true) {
			$title = isset(self::$title) ? self::$title : $title;
		}

		self::template($text, $message, $html, $title, $color, $stop, $response_code);
		self::reset();
	}

	static function set(array $array): self
	{
		return self::getInstance()->setData($array);
	}

	static function color(string $color): self
	{
		return self::getInstance()->setColor($color);
	}

	static function html(bool $bool): self
	{
		return self::getInstance()->setHtml($bool);
	}

	static function title(string $title): self
	{
		return self::getInstance()->setTitle($title);
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

class MErrorA extends MError
{

	function __construct()
	{
		parent::__construct();
	}

	public function setData(array $array): self
	{
		array_map(function ($key, $value) {
			if ($key == 'color') {
				return $this->setColor($value);
			} if ($key == 'html') {
				return $this->setHtml($value);
			} if ($key == 'title') {
				return $this->setTitle($value);
			}
		}, array_keys($array), array_values($array));
		return $this;
	}

	public function setColor(string $color): self
	{
		self::$color = self::$colors[$color] ? self::$colors[$color] : self::$colors['primary'];
		return $this;
	}

	public function setHtml(bool $bool): self
	{
		self::$html = $bool;
		return $this;
	}

	public function setTitle(string $title): self
	{
		self::$title = $title;
		return $this;
	}
}

# Initialize - AutoInitialize
# new MError;