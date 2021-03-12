<?php

if (!function_exists('error_print')) {
	function error_print($title=null, $text=null, $message=null, $stop=true, $response_code=404) {
		http_response_code($response_code);
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding("UTF-8");
		}
		?><!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
			<title><?php echo $title ? $title : 'System Error'; ?></title>
			<style type="text/css">
				.error-msg-content { padding: 15px; border-left: 5px solid #2196f3; background: #f8f8f8; margin-bottom: 10px; }
				.error-text { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; font-weight: 500; color: black; }
				.error-msg { margin-top: 15px; font-size: 14px; font-family: Consolas, Monaco, Menlo, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, sans-serif; color: #ac0e10; }
			</style>
		</head>
		<body>
			<div class="error-msg-content">
				<?php echo isset($text) && !empty($text) ? "<div class='error-text'>" . $text . "</div>\n" : null;
				echo isset($message) && !empty($message) ? "<div class='error-msg'>" . $message . "</div>\n" : null; ?>
			</div>
		</body>
		</html><?php
		if ($stop === true)
			exit();
	}
}

if (!function_exists('is_nem')) {
	function is_nem($data) {
		return isset($data) && !empty($data);
	}
}

if (!function_exists('nis_em')) {
	function nis_em($data) {
		return !isset($data) && empty($data);
	}
}

if (!function_exists('is')) {
	function is($data) {
		return isset($data);
	}
}

if (!function_exists('nis')) {
	function nis($data) {
		return !isset($data);
	}
}

if (!function_exists('nem')) {
	function nem($data) {
		return !empty($data);
	}
}

if (!function_exists('em')) {
	function em($data) {
		return empty($data);
	}
}