<?php declare(strict_types=1);
require_once 'settings.php';

if( array_key_exists( 'code', $_GET ) ) {
	require_once 'connect.php';

	$select = $db-> prepare( 'select get_url(:code)' );
	try {
		if( $select-> execute(['code'=>$_GET['code']]) && ($url = $select-> fetchColumn() ) ) {
			$select = $db = null;

			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
			header("Location: " . $url);
			exit();
		} else {
			header("HTTP/1.1 404 Not Found");
			exit('<h1>404 Not Found');
		}
	} catch( PDOException $e ) {
		header("HTTP/1.1 500 Internal Server Error");
		exit('<h1>500 Internal Server Error');
	}
}

define('base_url', $settings['base_url']);
define('default_url', $settings['default_url']);

// test for url but ignore our own
if( array_key_exists('url', $_GET) && (false===strpos($_GET['url'], base_url)) ) {
	$url = trim($_GET['url']);
	if( strlen($url) < strlen(base_url)+5 ) {
		// don't make longer urls
		$full_url = $url;
	} else {
		// save full_url for name of the svg file
		$full_url = $url;

		require_once 'connect.php';

		$select = $db->prepare( 'select set_url(:url)' );
		$select-> execute(['url'=>$full_url]);

		//echo $select-> errorInfo()[2];
		if( $code = $select-> fetchColumn() ) {
			$url = base_url . $code;
		} else {
			$url = default_url;
		}
	}
} else {
	// nothing to do get the default
	$url = $full_url = default_url;
}
//var_dump($_GET);
?><!DOCTYPE html>
<html lang="de">
  <head>
		<title>QR maker</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./lib/qrcode.js" defer></script>
		<link rel="stylesheet" href="./assets/style.css">
  </head>
<body>

  <h1>QR Code-Generator</h1>
	<p>URL eingeben und Farben/Grösse einstellen
	<p>Klick auf QR Code für Download.
	<p>Kürzen mit Enter-Taste. Gekürzt werden nur längere URLs.
  <form id="form-container" method="get">
  	
		<label for="url">URL:</label>
		<input type="url" id="url" name="url" value="<?=$url?>">
  	
		<span>Kürzen mit Enter</span>
		<div>
			<input type=submit value="Kürzen" id="shorten" data-full-url="<?=$full_url?>"><br/>
		</div>
  	
		<label for="bg-color">Hintergrundfarbe:</label>
		<input type="color" id="bg-color" value="#8cbf35">
  	
		<label for="color">Farbe:</label>
		<input type="color" id="color" value="#000000">
  	
		<label for="size">Grösse</label>
		<input type="range" id="size" min="50" max="500" value="100">

  	<span></span>
		<button id="do-qr">OK</button>

		<span>QR-Code</span>
		<div id="container"></div>

  </form>
	<script src="./assets/main.js"></script>

</body>
</html>
