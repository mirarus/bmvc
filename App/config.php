<?php

$_config['general'] = [
	'timezone' => 'Europe/Istanbul',
	'environment' => 'development' // production | development
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