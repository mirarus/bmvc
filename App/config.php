<?php

foreach (glob(__DIR__ . "/Helpers/*.php") as $file) {
	if ($file == __DIR__ . "/Helpers/index.php") return;
	$_helpers[] = $file;
}

$_config['files'] = array_merge($_helpers, [
	__DIR__ . "/routes.php"
]);

$_config['init'] = [
//	BMVC\Core\Model::class,
	BMVC\Libs\MError::class,
	BMVC\Libs\Lang::class
];