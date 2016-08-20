<?

$certFile = "./openssl/crypto/cacerts/cacert.pem";
$keyFile = "./openssl/crypto/keys/cakey.pem";
$configFile = "./config/configuration.php";

include_once($configFile);

$config['config'] = $config['config'] . ".ca";

//$dn['commonName'] = $config['orgName'] . " Certificate Authority";
$dn['commonName'] = $config['commonName'];
$dn['organizationName'] = $config['orgName'];
$dn['organizationalUnitName'] = $config['orgNameUnit'];
$dn['emailAddress'] = $config['contact'];
$dn['localityName'] = $config['city'];
$dn['stateOrProvinceName'] = $config['state'];
$dn['countryName'] = $config['country'];

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
<h1>Renewing CA certificate</h1>

<p>
We will resign the initially created CA certificate. The software will use the current key pair (a public key and a matching private key), and then sign it's own keypair, thus creating a self-signed certificate.
</p>

<p>
Now creating my own self-signed certificate... Please wait...
</p>
<?
// Ok, lets go. Time to create us a CA Cert.
$errorCount = 0;

print "<b>Checking your DN (Distinguished Name)...</b><br/>";
print "<pre>DN = ".var_export($dn,1)."</pre>";

print "<b>Loading current key...</b><br/>";
$fp = fopen($keyFile, "r") or $errorCount++;
$myKey = fread($fp, filesize($keyFile)) or $errorCount++;
fclose($fp) or $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Decoding current key...</b><br/>";
$privkey = openssl_pkey_get_private($myKey, $passPhrase);
checkError($privkey);
print "Done<br/><br/>\n";

print "<b>Issuing CSR...</b><br/>";
print "<pre>". var_export($dn,1) . "</pre>";
$csr = openssl_csr_new($dn, $privkey);
checkError($csr);
print "Done<br/><br/>\n";

print "<b>Self-signing CSR...</b><br/>";
$sscert = openssl_csr_sign($csr, null, $privkey, $age, array('digest_alg' => 'sha512',), getSerial()); // 5 years
checkError($sscert);
print "Done<br/><br/>\n";

print "<b>Exporting X509 Certificate...</b><br/>";
checkError(openssl_x509_export($sscert, $myCert));
print "Done<br/><br/>\n";

print "<b>Saving your certificate...</b><br/>";
if ($fp = fopen($certFile, 'w')) {
	fputs($fp, $myCert) or $errorCount++;
	fclose($fp) or $errorCount++;
}
else $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Your certificate:</b>\n<pre>$myCert</pre>\n";
print "<b>Your key:</b>\n<pre>$myKey</pre>\n";

?>
<h1>Successfully resigned CA Certificate with CA Key.</h1>

