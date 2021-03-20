<?php

$_config['general'] = [
	'timezone' => 'Europe/Istanbul',
	'environment' => 'development', // production | development
	'log' => false,
	'view' => [
		'cache' => false,
		'cacheExpire' => 120, // Second
		'blade' => false // Blade theme engine support -- FileName.blade.php if true is selected
	]
];

$_config['db'] = [
	'active' => false,
	'host' => 'localhost',
	'name' => '',
	'user' => '',
	'pass' => ''
];

$_config['default'] = [
	'module' => 'default',
	'controller' => 'Main',
	'method' => 'index',
	'lang' => 'tr'
];

$_config['helpers'] = [];

$_config['libraries'] = [];