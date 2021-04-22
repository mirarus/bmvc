<?php

$_config['general'] = [
	'timezone' => 'Europe/Istanbul',
	'environment' => 'development', // production | development
	'log' => true,
	'view' => [
		'cache' => false,
		'cacheExpire' => 120 // Second
	],
	'lang' => 'en'
];

$_config['db'] = [
	'host' => 'localhost',
	'name' => '',
	'user' => '',
	'pass' => ''
];

$_config['init'] = [
//	"BMVC\Core\Model",
	"BMVC\Libs\MError",
	"BMVC\Libs\Lang",
];