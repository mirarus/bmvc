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
	function res($data=null) {
		return System\Request::filter($data, 'server', false);
	}
}

if (!function_exists('req')) {
	function req($data=null) {
		return System\Request::filter($data, 'request', false);
	}
}

if (!function_exists('rep')) {
	function rep($data=null) {
		return System\Request::filter($data, 'post', false);
	}
}

if (!function_exists('reg')) {
	function reg($data=null) {
		return System\Request::filter($data, 'get', false);
	}
}

if (!function_exists('ref')) {
	function ref($data=null) {
		return System\Request::filter($data, 'files', false);
	}
}

if (!function_exists('rea')) {
	function rea($type=null, $data=null) {
		return System\Request::filter($data, $type, false);
	}
}

if (!function_exists('filter')) {
	function filter($data=null, $type='post', $db_filter=true) {
		return System\Request::filter($data, $type, $db_filter);
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