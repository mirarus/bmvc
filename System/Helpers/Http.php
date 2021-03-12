<?php

if (!function_exists('url')) {
	function url($url=null, $return=true) {
		if ($url) {
			if ($return == true) {
				return URL . $url;
			} else {
				echo URL . $url;
			}
		} else {
			if ($return == true) {
				return URL;
			} else {
				echo URL;
			}
		}
	}
}

if (!function_exists('redirect')) {
	function redirect($par, $time=0, $stop=true) {
		if ($time == 0) {
			header("Location: " . $par);
		} else {
			header("Refresh: " . $time . "; url=" . $par);
		}
		if ($stop === true) {
			die();
		}
	}
}

if (!function_exists('refresh')) {
	function refresh($par, $time=0, $stop=true) {
		if ($time == 0) {
			echo "<meta http-equiv='refresh' content='URL=" . $par . "'>";
		} else {
			echo "<meta http-equiv='refresh' content='" . $time . ";URL=" . $par . "'>";
		}
		if ($stop === true) {
			die();
		}
	}
}

if (!function_exists('GetIP')) {
	function GetIP() {
		if (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
			if (strstr($ip, ',')) {
				$tmp = explode (',', $ip);
				$ip = trim($tmp[0]);
			}
		} else {
			$ip = getenv("REMOTE_ADDR");
		}
		return $ip;
	}
}

if (!function_exists('res')) {
	function res($var=null) {
		if (nis_em($var)) {
			return $_SERVER;
		} else {
			return $_SERVER[$var];
		}
	}
}

if (!function_exists('req')) {
	function req($var=null) {
		if (nis_em($var)) {
			return $_REQUEST;
		} else {
			return $_REQUEST[$var];
		}
	}
}

if (!function_exists('reg')) {
	function reg($var=null) {
		if (nis_em($var)) {
			return $_GET;
		} else {
			return $_GET[$var];
		}
	}
}

if (!function_exists('rep')) {
	function rep($var=null) {
		if (nis_em($var)) {
			return $_POST;
		} else {
			return $_POST[$var];
		}
	}
}

if (!function_exists('ref')) {
	function ref($var=null) {
		if (nis_em($var)) {
			return $_FILES;
		} else {
			return $_FILES[$var];
		}
	}
}

if (!function_exists('rea')) {
	function rea($val=null, $var=null) {
		if ($val == 'server') {
			return res($var);
		} elseif ($val == 'request') {
			return req($var);
		} elseif ($val == 'get') {
			return reg($var);
		} elseif ($val == 'post') {
			return rep($var);
		} elseif ($val == 'files') {
			return ref($var);
		}
	}
}

if (!function_exists('filter')) {
	function filter($text) {
        $check[1] = chr(34); // symbol "
        $check[2] = chr(39); // symbol '
        $check[3] = chr(92); // symbol /
        $check[4] = chr(96); // symbol `
        $check[5] = "drop table";
        $check[6] = "update";
        $check[7] = "alter table";
        $check[8] = "drop database";
        $check[9] = "drop";
        $check[10] = "select";
        $check[11] = "delete";
        $check[12] = "insert";
        $check[13] = "alter";
        $check[14] = "destroy";
        $check[15] = "table";
        $check[16] = "database";
        $check[17] = "union";
        $check[18] = "TABLE_NAME";
        $check[19] = "1=1";
        $check[20] = 'or 1';
        $check[21] = 'exec';
        $check[22] = 'INFORMATION_SCHEMA';
        $check[23] = 'like';
        $check[24] = 'COLUMNS';
        $check[25] = 'into';
        $check[26] = 'VALUES';
        $check[27] = 'kill';
        $check[28] = 'union';
        $check[29] = '$';
        $check[30] = '<?php';
        $check[31] = '?>';
        $y = 1;
        $x = sizeof($check);
        while ($y <= $x) {
        	$target = strpos($text, $check[$y]);
        	if ($target !== false)
        		$text = str_replace($check[$y], "", $text);
        	$y++;
        }
        return $text;
    }
}

if (!function_exists('_filter')) {
	function _filter($data, $type='post', $db_filter=true) {
		if ($type == 'server') {
			$_SERVER = FilterClass::filterXSS($_SERVER);
			return $db_filter == true ? @filter(@$_SERVER[$data]) : @$_SERVER[$data];
		} elseif ($type == 'request') {
			$_REQUEST = FilterClass::filterXSS($_REQUEST);
			return $db_filter == true ? @filter(@$_REQUEST[$data]) : @$_REQUEST[$data];
		} elseif ($type == 'get') {
			$_GET = FilterClass::filterXSS($_GET);
			return $db_filter == true ? @filter(@$_GET[$data]) : @$_GET[$data];
		} elseif ($type == 'post') {
			$_POST = FilterClass::filterXSS($_POST);
			return $db_filter == true ? @filter(@$_POST[$data]) : @$_POST[$data];
		} elseif ($type == 'files') {
			$_FILES = FilterClass::filterXSS($_FILES);
			return $db_filter == true ? @filter(@$_FILES[$data]) : @$_FILES[$data];
		}
	}
}

if (!function_exists('check_method')) {
	function check_method($method='POST') {
		if ($_SERVER['REQUEST_METHOD'] === $method) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('expU')) {
	function expU() {
		return explode('/', $_GET['url']);
	}
}

if (!function_exists('expId')) {
	function expId($id=0) {
		return explode('/', $_GET['url'])[$id];
	}
}

if (!function_exists('expUrl')) {
	function expUrl($url=null, $id=0) {
		if (isset($_GET['url']) && !empty($_GET['url'])) {
			if (@explode('/', @$_GET['url'])[$id] == @$url) {
				return true;
			}
		}
		return false;
	}
}

if (!function_exists('rtUrl')) {
	function rtUrl($url) {
		if (rtrim('/', $_GET['url']) == $url) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('PageCheck')) {
	function PageCheck($url=null) {
		if (@$_GET['url'] == @$url) {
			return true;
		}
		return false;
	}
}

if (!function_exists('UrlCheck')) {
	function UrlCheck($url=null, $id=0) {
		if (@explode('/', @$_GET['url'])[$id] == @$url) {
			return true;
		}
		return false;
	}
}

if (!function_exists('vd')) {
	function vd($data=null) {
		if ($data == null) {
			if (isset($_REQUEST['vd'])) {
				return $_REQUEST['vd'];
			}
		} else {
			if (isset($_REQUEST['vd'][$data])) {
				return $_REQUEST['vd'][$data];
			}
		}
	}
}