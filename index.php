<?php declare(strict_types=1);

define('base_url', 'https://qr.sbw.media/');
if( array_key_exists( 'code', $_GET ) ) {
	$db = new PDO
		( 'mysql:host=localhost;dbname=plc_qr-code'
		, 'plc_qr-code'
		, 'plc_qr-code'
		);
	
	$select = $db->prepare('select get_url(:code)');
	$select-> bindValue(':code', $_GET['code']);
	if( $select-> execute() && ($url = $select-> fetchColumn() ) ) {
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");

		header("Location: " . $url);
		die();
	} else {
		header("HTTP/1.1 404 Not Found");
		die('invalid code');
	}
}

// test for url but ignore our own
if( array_key_exists('url', $_GET) && (false===strpos($_GET['url'], base_url)) ) {
	if(strlen($_GET['url']) < strlen(base_url)+5) {
		// don't make longer urls
		$url = $full_url = $_GET['url'];
	} else {
		$full_url = $_GET['url'];
		$db = new PDO
			( 'mysql:host=localhost;dbname=plc_qr-code'
			, 'plc_qr-code'
			, 'plc_qr-code'
			);
		$select = $db->prepare('select set_url(:url)');
		$select-> bindParam(':url', $full_url);
		$select-> execute();
		//echo $select-> errorInfo()[2];
		if( $code = $select-> fetchColumn() ) {
			$url = base_url . $code;
		} else {
			$url = "https://sbw.media";
		}
	}
} else {
	$url = $full_url = "https://sbw.media";
}
//var_dump($_GET);
?><!DOCTYPE html>
<html>
  <head>
	<title>SBW QR maker</title>
	<meta charset="utf-8">
    <script src="lib/qrcode.js" defer></script>
    <style>
      :root {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: small;
        background-color: white;
        color: black;
      }
      #form-container {
        max-width: 300px;
        display: grid;
        grid-template-columns: 120px 1fr;
		grid-row-gap: 1em;
      }
      input:not([type=submit]) {
        color: inherit;
        background-color: inherit;
        border-color: white;
        border-style: ridge;
        border-width: 1px;
        border-radius: 3px;
      }

      input:focus {
        background-color: inherit;
      }
      #container {
        margin: 0 auto;
      }
    </style>
  </head>
<body>
  <h1>QR Code-Generator</h1>
	<p>URL eingeben und Farben/Grösse einstellen
	<p>Klick auf QR Code zum Downloaded.
	<p>Kürzen mit Enter-Taste. Gekürzt werden nur längere URLs.
  <form id="form-container" method="get">
  <label for="url">URL:</label></label><input type="text" id="url" name="url" value="<?=$url?>" onchange="doQR();">
  <span>Kürzen mit Enter</span>
	<div><input type=submit value="Kürzen" id="shorten" data-full-url="<?=$full_url?>"><br/> Nur ab gewisse Länge.</div>
  <label for="bg-color">Hintergrundfarbe:</label><input type="color" id="bg-color" value="#FFD700" onchange="doQR();">
  <label for="color">Farbe:</label><input type="color" id="color" value="#0057B8" onchange="doQR();">
  <label for="size">Grösse</label><input type="range" id="size" min="50" max="500" value="100" onchange="doQR();">
  <span></span><button id="do-qr">OK</button>
  <span>QR-Code</span>
	<a href='' onclick='downloadSVG();'><div id="container"></div></a>
  </form>

</body>
<script>
  document.addEventListener("DOMContentLoaded", ev => {
    document.getElementById("do-qr").onclick = doQR;
	doQR();
	const url = document.getElementById('url');
	url.addEventListener('focus', ev=> ev.target.select());
	//url.selectionStart = 0;
    //url.selectionEnd = url.value.length;
	history.pushState({},'','/');
	url.select();
	url.focus();
  });
  
  const doShorten = ev => {
	  const url = document.getElementById('url').value;
	  window.location.assign = `https://qr.sbw.media/?url=${url}`;
	  window.location.reload( );
  };
  const doQR = ev => {
	const url = document.getElementById('url').value;
	if( url.length === 0 ) return;
    let qrcode = new QRCode({
      content: url,
      padding: 2,
      width: document.getElementById('size').value, height: document.getElementById('size').value,
      join: false,
      color: document.getElementById('color').value,
      background: document.getElementById('bg-color').value,
      ecl: "L"
    });
    document.getElementById("container").innerHTML = qrcode.svg();
  };
  const downloadSVG = ()=> {
	  const svg = document.getElementById('container').outerHTML;
	  const blob = new Blob([svg.toString()]);
	  const element = document.createElement("a");
	  try {
	    const encode = new URL('', document.getElementById('shorten').dataset.fullUrl);
	    element.download = encode.hostname.replaceAll('.','_') + ".svg";
	  } catch {
		element.download = 'qr.svg';
	  }
	  element.href = window.URL.createObjectURL(blob);
	  element.click();
	  element.remove();
	}
</script>
</html>
