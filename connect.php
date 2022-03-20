<?php declare(strict_types=1);

$db = new \PDO
	( 'mysql:dbname=plc_qr-code'
	, "plc_qr-code"
	, 'plc_qr-code'
	, [PDO::ATTR_PERSISTENT => true]
	);