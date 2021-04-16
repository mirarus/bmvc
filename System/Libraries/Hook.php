<?php

/**
 * Hook
 *
 * Mirarus BMVC
 * @package BMVC\Libs
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

namespace BMVC\Libs;

class Hook
{

	public static function hook_load($name, $callback=null, $value=null)
	{
		static $events = [];
		if ($callback !== null) {
			if ($callback) {
				$events[$name][] = $callback;
			} else {
				unset($events[$name]);
			}
		} elseif (isset($events[$name])) {
			asort($events[$name]);
			foreach ($events[$name] as $callback) {
				$value = call_user_func($callback, $value);
			}
			return $value;
		}
	}

	public static function add_action($name, $callback=null)
	{
		return self::hook_load($name, $callback, null);
	}

	public static function do_action($name, $value=null)
	{
		return self::hook_load($name, null, $value);
	}

	public static function remove_action($name)
	{
		return self::hook_load($name, false);
	}
}