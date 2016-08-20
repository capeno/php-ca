<?

$certFile = "./openssl/crypto/cacerts/cacert.pem";
$keyFile = "./openssl/crypto/keys/cakey.pem";

$configFile = "./config/configuration.php";

include_once($configFile);

$passPhrase = $config['passPhrase'];

if ($_REQUEST['pass'] != $passPhrase) {
	print "<h1>Renewing CA certificate</h1><p><b>Passphrase incorrect</b><br/>You did not enter the correct passphrase";
	return;
}

if ($_REQUEST['age']) {  
  $age = $_REQUEST['age'];
} else {
  $age = 365;
}

?>
<h1>Signing certificate request</h1>

<p>
We will sign the supplied certificate request with the CA's keys. The signed certificate will be returned.
</p>

<p>
Now signing certificate... Please wait...
</p>
<?
// Ok, lets go. Time to create us a CA Cert.
$errorCount = 0;

print "<b>Loading CA key...</b><br/>";
$fp = fopen($keyFile, "r") or $errorCount++;
$myKey = fread($fp, filesize($keyFile)) or $errorCount++;
fclose($fp) or $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Decoding CA key...</b><br/>";
$privkey = openssl_pkey_get_private($myKey, $passPhrase);
checkError($privkey);
print "Done<br/><br/>\n";

print "<b>Loading CA cert...</b><br/>";
if ($fp = fopen($certFile, "r")) {
	$myCert = fread($fp, filesize($certFile)) or $errorCount++;
	fclose($fp) or $errorCount++;
}
else $errorCount++;

if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Signing CSR...</b><br/>";
$scert = openssl_csr_sign($_REQUEST['request'], $myCert, $privkey, $age);
checkError($scert);
print "Done<br/><br/>\n";

print "<b>Exporting X509 Certificate...</b><br/>";
checkError(openssl_x509_export($scert, $theirCert));
print "Done<br/><br/>\n";

print "<b>Your certificate:</b>\n<pre>$theirCert</pre>\n";

?>
<h1>Successfully signed certificate request with CA key.</h1>

