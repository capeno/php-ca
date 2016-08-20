<?
$certFile = "./openssl/crypto/cacerts/cacert.pem";
$keyFile = "./openssl/crypto/keys/cakey.pem";
$indexFile = "./openssl/crypto/index.txt";
$serialFile = "./openssl/crypto/serial";
$configFile = "./config/configuration.php";

$config['config'] = $config['config'] . ".ca";

$folders = array(
	'openssl/crypto',
	'openssl/crypto/keys',
	'openssl/crypto/cacerts',
	'openssl/crypto/certs',
	'openssl/crypto/requests',
);
foreach ($folders as $pos=>$folder) {
	if (file_exists($folder)) {
		if (!is_dir($folder)) {
			print "Error, $folder is in the road, please remove it<br/>\n";
			return;
		}
		if (!touch("$folder/test")) {
			print "Error, $folder appears to have insufficient permissions for the current user to write to it.<br/>\n";
			return;
		}
		unlink("$folder/test");
	}
	else {
		mkdir($folder);
		if (!is_dir($folder)) {
			print "Couldn't create $folder, please check permissions<br/>\n";
			return;
		}
	}
}

if (get_magic_quotes_gpc()) {
	$passPhrase = stripslashes($_REQUEST['passPhrase']);
	while (list($key, $val) = each($_REQUEST['dn'])) {
		$dn[$key] = stripslashes($val);
	}
}
else {
	$passPhrase = &$_REQUEST['passPhrase'];
        $dn = &$_REQUEST['dn'];
}

?>
<h1>Creating initial CA certificate</h1>

<p>
This is the point where we will generate the CA's certificate. The software will generate a key pair (a public key and a matching private key), and then sign it's own keypair, thus creating a self-signed certificate.
</p>

<p>
Now creating my own self-signed certificate... Please wait...
</p>
<?
// Ok, lets go. Time to create us a CA Cert.
$errorCount = 0;

print "<b>Checking your DN (Distinguished Name)...</b><br/>";
print "<pre>DN = ".var_export($_REQUEST['dn'],1)."</pre>";

print "<b>Generating new key...</b><br/>";
$privkey = openssl_pkey_new($config);
checkError($privkey);
print "Done<br/><br/>\n";

print "<b>Issuing CSR...</b><br/>";
$csr = openssl_csr_new($_REQUEST['dn'], $privkey);
checkError($csr);
print "Done<br/><br/>\n";

print "<b>Self-signing CSR...</b><br/>";
$sscert = openssl_csr_sign($csr, null, $privkey, 9125, array('digest_alg' => 'sha512', 'x509_extensions' => 'v3_ca', 'config' => $config['config'] ), getSerial());
checkError($sscert);
print "Done<br/><br/>\n";

print "<b>Exporting X509 Certificate...</b><br/>";
checkError(openssl_x509_export($sscert, $myCert));
print "Done<br/><br/>\n";

print "<b>Exporting encoded private key...</b><br/>";
checkError(openssl_pkey_export($privkey, $myKey, $passPhrase));
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

print "<b>Saving your encoded key...</b><br/>";
if ($fp = fopen($keyFile, 'w')) {
	fputs($fp, $myKey) or $errorCount++;
	fclose($fp) or $errorCount++;
}
else $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

$date = date("Y-m-d H:i:s (Z)");
$quotedPassPhrase = addslashes($passPhrase);
$quotedCommonName = addslashes($dn['commonName']);
$quotedOrganization = addslashes($dn['organizationName']);
$quotedOrganizationUnit = addslashes($dn['organizationalUnitName']);
$quotedContact = addslashes($dn['emailAddress']);
$quotedCity = addslashes($dn['localityName']);
$quotedState = addslashes($dn['stateOrProvinceName']);
$quotedCountry = addslashes($dn['countryName']);

$myConfig = <<<ENDE
<?
// Locally generated configfile generated on $date
//

\$caSetup = true;
\$config['passPhrase'] = "$quotedPassPhrase";
\$config['commonName'] = "$quotedCommonName";
\$config['orgName'] = "$quotedOrganization";
\$config['orgNameUnit'] = "$quotedOrganizationUnit";
\$config['contact'] = "$quotedContact";
\$config['city'] = "$quotedCity";
\$config['state'] = "$quotedState";
\$config['country'] = "$quotedCountry";


ENDE;
$myConfig .= '?'.'>';

print "<b>Saving your index file...</b><br/>";
if ($fp = fopen($indexFile, 'w')) {
	fputs($fp, '');
	fclose($fp) or $errorCount++;
} else $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Saving your serial file...</b><br/>";
if ($fp = fopen($serialFile, 'w')) {
	fputs($fp, '0101') or $errorCount++;
	fclose($fp) or $errorCount++;
}
else $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
	exit();
}
print "Done<br/><br/>\n";

print "<b>Saving your configuration file...</b><br/>";
if ($fp = fopen($configFile, 'w')) {
	fputs($fp, $myConfig) or $errorCount++;
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
<h1>Successfully created CA Certificate and CA Key.</h1>

<p>
Congratulations, you have now created your CA, and this site is live.
</p>

<p>
The next step would be for you to create your own personal certificate.<br/>
--&gt; <a href="index.php">Get a signed certificate</a>
</p>
