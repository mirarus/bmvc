<?php

if (!function_exists('ct')) {
	function ct($url, $return=true) {
		if ($return == true) {
			return $url . '?ct=' . time();
		} else {
			echo $url . '?ct=' . time();
		}
	}
}

if (!function_exists('asset')) {
	function asset($type, $url, $return=true) {
		if ($return == true) {
			if ($type == 'js') {
				return '<script src="' . ct($url) . '" type="text/javascript"></script>' . "\n";
			} elseif ($type == 'css') {
				return '<link rel="stylesheet" href="' . ct($url) . '" type="text/css" />' . "\n";
			}
		} else {
			if ($type == 'js') {
				echo '<script src="' . ct($url) . '" type="text/javascript"></script>' . "\n";
			} elseif ($type == 'css') {
				echo '<link rel="stylesheet" href="' . ct($url) . '" type="text/css" />' . "\n";
			}
		}
	}
}

if (!function_exists('get_css') ) {
	function get_css($file) {
		if (file_exists($file)) {
			return '<link rel="stylesheet" href="' . $file . '">';
		} else {
			MError::title('CSS Error!')::print('CSS File Not Found!', 'File Name: ' . $file);
		}
	}
}

if (!function_exists('get_js') ) {
	function get_js($file) {
		if (file_exists($file)) {
			return '<script type="text/javascript" src="' . $file . '"></script>';
		} else {
			MError::title('Javascript Error!')::print('Javascript File Not Found!', 'File Name: ' . $file);
		}
	}
}

if (!function_exists('get_asset')) {
	function get_asset($file) {
		if (file_exists($file)) {
			return $file;
		} else {
			MError::title('Asset Error!')::print('Asset File Not Found!', 'File Name: ' . $file);
		}
	}
}

if (!function_exists('valid_email')) {
	function valid_email($email) {
		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}

if (!function_exists('_password')) {
	function _password()
	{
		$args = func_get_args();
		
		if ($args[0] == 'hash') {
			return password_hash(md5($args[1]), PASSWORD_DEFAULT, ['cost' => @$args[2] ? $args[2] : 12]);
		} elseif ($args[0] == 'verify') {
			return (bool) password_verify(md5($args[1]), $args[2]);
		}
	}
}

if (!function_exists('code_gen')) {
	function code_gen($var, $pattern='alpnum') 
	{
		$chars = []; 
		if ($pattern == 'alpnum') {
			$chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
		} elseif ($pattern == 'alpha') {
			$chars = array_merge(range('a', 'z'), range('A', 'Z'));
		} elseif ($pattern == 'num') {
			$chars = array_merge(range(0, 9));
		} elseif ($pattern == 'lowercase') {
			$chars = array_merge(range('a', 'z'));
		} elseif ($pattern == 'uppercase') {
			$chars = array_merge(range('A', 'Z'));
		}
		srand((float) microtime() * 100000); 
		shuffle($chars); 
		$result = ''; 
		for ($i=0; $i < $var; $i++) { 
			$result .= $chars[$i]; 
		} 
		unset($chars); 
		return($result); 
	}
}

if (!function_exists('unique_key')) {
	function unique_key($int=10) {
		return hash('sha512', session_id() . bin2hex(openssl_random_pseudo_bytes($int)));
	}
}

if (!function_exists('c_mb_strtoupper')) {
	function c_mb_strtoupper($str) {
		$str = str_replace('i', 'İ', $str);
		$str = str_replace('ı', 'I', $str);
		
		if (function_exists('mb_strtoupper')) {
			return @mb_strtoupper($str, 'UTF-8');
		} else {
			return $str;
		}
	}
}

if (!function_exists('slug')) {
	function slug($str, $options=[])
	{
		if (function_exists('mb_convert_encoding')) {
			$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
		}
		$defaults = array(
			'delimiter' => '-',
			'limit' => null,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => true
		);
		$options = array_merge($defaults, $options);
		$char_map = array(
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
			'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
			'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
			'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
			'ß' => 'ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
			'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
			'ÿ' => 'y',
			'©' => '(c)',
			'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
			'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
			'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
			'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
			'Ϋ' => 'Y',
			'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
			'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
			'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
			'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
			'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
			'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
			'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
			'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
			'Я' => 'Ya',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
			'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
			'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
			'я' => 'ya',
			'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
			'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
			'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
			'Ž' => 'Z',
			'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
			'ž' => 'z',
			'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
			'Ż' => 'Z',
			'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
			'ż' => 'z',
			'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
			'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
			'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
			'š' => 's', 'ū' => 'u', 'ž' => 'z'
		);
		$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
		if ($options['transliterate']) {
			$str = str_replace(array_keys($char_map), $char_map, $str);
		}
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
		if (function_exists('mb_substr')) {
			$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
		}
		$str = trim($str, $options['delimiter']);
		if (function_exists('mb_strtolower')) {
			$str = $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
		}
		return $str;
	}
}

function html_decode($par) : string {
	return htmlspecialchars_decode(html_entity_decode(htmlspecialchars_decode($par, ENT_QUOTES), ENT_QUOTES), ENT_QUOTES);
}