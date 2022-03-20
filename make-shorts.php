<?php
declare(strict_types=1);

$charset ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNAOPQRSTUVWXYZ1234567890-_.!~*'()";
$size = 8;

function getCode(): string {
  global $size;
  global $charset;
  $length = strlen($charset)-1;

  $result
	= $charset[rand(0,$length)]
 	. $charset[rand(0,$length)]
	. $charset[rand(0,$length)]
	. $charset[rand(0,$length)]
	. $charset[rand(0,$length)]
	;
  return $result;
}


$db = new \PDO
	( 'mysql:dbname=plc_qr-code'
	, "plc_qr-code"
	, 'plc_qr-code'
	);
$insert = $db-> prepare("insert into code(code,url,last_used,hits)values(:code,null,default,default)");
$code = getCode();
$insert->bindParam(':code',$code);

for($i=1000; $i>0; $i--) {
  $code = getCode();
  $insert-> execute() or die($insert->errorInfo()[2]);
}
echo $code;
