<?php

/**
 * Hash
 *
 * Mirarus BMVC
 * @package BMVC\Libs
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.1
 */

namespace BMVC\Libs;
use BMVC\Core\MError;

class Hash
{

	private static $cost = 10;

	static function make($value, array $options=[])
	{
		if (!array_key_exists('cost', $options)) {
			$options['cost'] = self::$cost;
		}
		$hash = password_hash($value, PASSWORD_DEFAULT, $options);
		if ($hash === false) {
			MError::title('Hash Error!')::print('Bcrypt hash is not supported.');
		}
		return $hash;
	}

	static function check($value, $hashedValue)
	{
		return password_verify($value, $hashedValue);
	}

	static function rehash($hashedValue, array $options=[])
	{
		if (!array_key_exists('cost', $options)) {
			$options['cost'] = self::$cost;
		}
		return password_needs_rehash($hashedValue, PASSWORD_DEFAULT, $options);
	}
}