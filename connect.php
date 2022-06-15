<?php declare(strict_types=1);

$db = new \mysqli(
	$settings['db']['server'],
	$settings['db']['user'],
	$settings['db']['passwort'],
	$settings['db']['name']
	) or die('Could not connect to database');
$db-> set_charset( 'utf8mb4' );
