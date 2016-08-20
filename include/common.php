<?
//putenv("OPENSSL_CONF=test.conf");
//apache_setenv("OPENSSL_CONF", "test.conf");
$cwd = getcwd();
$config = array(
	'config' => "$cwd/openssl/openssl.conf",
	'dir' => "$cwd/openssl/crypto",
	'version' => "3.0b",
);

function checkError($result) {
	if (!$result) {
		while (($error = openssl_error_string()) !== false) {
			if ($error == "error:0E06D06C:configuration file routines:NCONF_get_string:no value") {
				if ($nokeyError++ == 0) {
					$errors .= "One or more configuration variables could not be found (possibly non-fatal)<br/>\n";
				}
			}
			else {
				$errorCount++;
				$errors .= "Error $errorCount: $error<br/>\n";
			}
		}
	}
	if ($errorCount or (!$result and $nokeyError)) {
		print "FATAL: An error occured in the script. Possibly due to a misconfiguration.<br/>\nThe following errors were reported during execution:<br/>\n$errors";
		exit();
	}
}
	
function getSerial() {
	$fp = fopen("./openssl/crypto/serial", "r");
	list($serial) = fscanf($fp, "%x");
	fclose($fp);

	$fp = fopen("./openssl/crypto/serial", "w");
	fputs($fp, sprintf("%04X", $serial + 1) . chr(0) . chr(10) );
	fclose($fp);
	return $serial + 1;
}

function printHeaderbar() {
	global $config;
	$name = "Certificate Authority";
	if ($org = $config['orgName']) {
		$name = "$org";
	}
	$version = $config['version'];
	print <<<ENDE
  <div id="wrap">
    <div id="header">
	<p></p>
        <h1>$name</h1><br/>
        <small>$version</small><br/><br/>
	    <ul id="nav">
		<li><a href="index.php">Main page</a></li>
		<li><a href="index.php?area=admin">Admin</a></li>
		<li><a href="index.php?area=csr">CSR</a></li>
		<li><a href="index.php?area=main&stage=about">About</a></li>
		<li><a class="last" href="index.php?area=main&stage=help">Help</a></li>
	    </ul>
    </div>
ENDE;
}

function printHeader($title = "", $inhead = "") {
	print <<<ENDE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>PHP-CA: $title</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!-- <script type="text/javascript" src="scripts/common.php"></script> -->
<link rel="icon" type="image/x-icon" href="img/phpca.ico"/>
$inhead
</head>
<body>
ENDE;
	printHeaderbar();
	print "<div id=\"subheader\">Logged in as: " . ( $_SERVER['REMOTE_USER'] == "" ? guest : $_SERVER['REMOTE_USER'] ). "</div>\n";
	print "<div id=\"content\">\n";
}

function printFooter() {
	print <<<ENDE
</div>
</div>
</body>
</html>
ENDE;
}

?>
