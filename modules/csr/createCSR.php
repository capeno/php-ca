<?


//$csrcertFile = "./openssl/crypto/cacerts/cacert.pem";
//$csrkeyFile = "./openssl/crypto/keys/cakey.pem";
//$indexFile = "./openssl/crypto/index.txt";
//$serialFile = "./openssl/crypto/serial";
$configFile = "./config/configuration.php";
include_once($configFile);
$folders = array(
	'openssl/crypto',
	'openssl/crypto/keys',
	'openssl/crypto/cacerts',
	'openssl/crypto/certs',
	'openssl/crypto/requests',
);

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
if ( ! $_REQUEST['name'] || $_REQUEST['name'] == "")
  $csrname = preg_replace('/\*/', 'wildcard', $dn[commonName]);
else
  $csrname = $_REQUEST['name'];

$csrFile = "./openssl/crypto/requests/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $csrname),0,30).".csr";
$sanFile = "./openssl/crypto/requests/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $csrname),0,30).".san";
$keyFile = "./openssl/crypto/keys/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $csrname),0,30).".key";

if ($_REQUEST['sanIP'] || $_REQUEST['sanDNS']) {
       	if ( ! $_REQUEST['sanIP'] == "" ) $sanIP  = explode(",", $_REQUEST['sanIP'], 20);
       	if ( ! $_REQUEST['sanDNS'] == "" ) $sanDNS = explode(",", $_REQUEST['sanDNS'], 20);
        if ( count($sanIP) > 0 )
          for ($i = 0; $i < count($sanIP); $i++) {
            if ($san)
               	$san .= ",IP:" . $sanIP[$i];
            else
                $san = "IP:" . $sanIP[$i];
          }
	if ( count($sanDNS) > 0 )
          for ($i = 0; $i < count($sanDNS); $i++) {
            if ($san)
                $san .= ",DNS:" . $sanDNS[$i];
            else
                $san = "DNS:" . $sanDNS[$i];
          }
//        $san = $_REQUEST['san'];
        if ($sanFile) {
                $fp = fopen($sanFile, "w");
                fputs($fp, $san);
                fclose($fp);
        }
}
elseif ($sanFile && is_file($sanFile)) {
       	$fp = fopen($sanFile, "w");
       	fputs($fp, $san);
       	fclose($fp);
//        $fp = fopen($sanFile, "r");
//        $san = fread($fp, filesize($sanFile));
//        fclose($fp);
}
//else {
//      	print "<b>Error: </b> Could not load SAN from disk \"$sanFile\", and none supplied in request.";
//        return;
//}

?>
<h1>Creating initial CSR certificate</h1>

<p>
This is the point where we will generate the Certificare Signing Request. The software will generate a key pair (a public key and a matching private key).
</p>

<p>
Now creating my own self-signed certificate... Please wait...
</p>
<?
// Ok, lets go. Time to create us a CA Cert.
$errorCount = 0;

print "<b>Checking your DN (Distinguished Name)...for ". $csrname ."</b><br/>";
print "<pre>DN = ".var_export($_REQUEST['dn'],1)."</pre>";

print "<b>Generating new key...</b><br/>";
$csrprivkey = openssl_pkey_new($dn);
checkError($csrprivkey);
print "Done<br/><br/>\n";

print "<b>Creating CSR...</b><br/>";
$csr = openssl_csr_new($_REQUEST['dn'], $csrprivkey);
checkError($csr);
print "Done<br/><br/>\n";

//
//print "<b>Self-signing CSR...</b><br/>";
//$sscert = openssl_csr_sign($csr, null, $privkey, 7300, array(), getSerial());
//checkError($sscert);
//print "Done<br/><br/>\n";

//print "<b>Exporting X509 Certificate...</b><br/>";
//checkError(openssl_x509_export($sscert, $myCert));
//print "Done<br/><br/>\n";

print "<b>Exporting encoded private key...</b><br/>";
checkError(openssl_pkey_export($csrprivkey, $myKey, $passPhrase));
print "Done<br/><br/>\n";

print "<b>Saving your CSR...</b><br/>";
checkError(openssl_csr_export($csr, $myCSR));
if ($fp = fopen($csrFile, 'w')) {
	fputs($fp, $myCSR) or $errorCount++;
	fclose($fp) or $errorCount++;
}

//print "<b>Saving your certificate...</b><br/>";
//if ($fp = fopen($certFile, 'w')) {
//	fputs($fp, $myCert) or $errorCount++;
//	fclose($fp) or $errorCount++;
//}
//else $errorCount++;

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

exit(); //alex
//$date = date("Y-m-d H:i:s (Z)");
//$quotedPassPhrase = addslashes($passPhrase);
//$quotedCommonName = addslashes($dn['commonName']);
//$quotedOrganization = addslashes($dn['organizationName']);
//$quotedOrganizationUnit = addslashes($dn['organizationalUnitName']);
//$quotedContact = addslashes($dn['emailAddress']);
//$quotedCity = addslashes($dn['localityName']);
//$quotedState = addslashes($dn['stateOrProvinceName']);
//$quotedCountry = addslashes($dn['countryName']);

//$myConfig = <<<ENDE
//<?
// Locally generated configfile generated on $date
//
//
//\$caSetup = true;
//\$config['passPhrase'] = "$quotedPassPhrase";
//\$config['commonName'] = "$quotedCommonName";
//\$config['orgName'] = "$quotedOrganization";
//\$config['orgNameUnit'] = "$quotedOrganizationUnit";
//\$config['contact'] = "$quotedContact";
//\$config['city'] = "$quotedCity";
//\$config['state'] = "$quotedState";
//\$config['country'] = "$quotedCountry";
//
//
//ENDE;
//$myConfig .= '?'.'>';

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
