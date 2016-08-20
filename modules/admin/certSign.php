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
flush();
$fp = fopen($keyFile, "r") or $errorCount++;
$myKey = fread($fp, filesize($keyFile)) or $errorCount++;
fclose($fp) or $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
}
print "Done<br/><br/>\n";

print "<b>Decoding CA key...</b><br/>";
flush();
$privkey = openssl_pkey_get_private($myKey, $passPhrase);
checkError($privkey);
print "Done<br/><br/>\n";


print "<b>Loading CA cert...</b><br/>";
flush();
$fp = fopen($certFile, "r") or $errorCount++;
$myCert = fread($fp, filesize($certFile)) or $errorCount++;
fclose($fp) or $errorCount++;
if ($errorCount) {
	print "FATAL: an error occured in the script. Possibly due to inadequate file permissions.";
}
print "Done<br/><br/>\n";

print "<b>Signing CSR...</b><br/>";

$csr = "";
$csrFile = "";
if ($_REQUEST['name'])
	$csrFile = "./openssl/crypto/requests/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $_REQUEST['name']),0,30).".csr";
	$sanFile = "./openssl/crypto/requests/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $_REQUEST['name']),0,30).".san";

if ($_REQUEST['request']) {
	$csr = $_REQUEST['request'];
	if ($csrFile) {
		$fp = fopen($csrFile, "w");
		fputs($fp, $csr);
		fclose($fp);
	}
}
elseif ($csrFile && is_file($csrFile)) {
	$fp = fopen($csrFile, "r");
	$csr = fread($fp, filesize($csrFile));
	fclose($fp);
}
else {
	print "<b>Error: </b> Could not load CSR from disk \"$csrFile\", and none supplied in request.";
	return;
}

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
                $san .=	",DNS:" . $sanDNS[$i];
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
        $fp = fopen($sanFile, "r");
        $san = fread($fp, filesize($sanFile));
        fclose($fp);
}
//else {
//      	print "<b>Error: </b> Could not load SAN from disk \"$sanFile\", and none supplied in request.";
//        return;
//}

if ( $san && ! $san == "" ) {
  $config['config'] = $config['config'] . ".san";
  //"./openssl/openssl_san.conf";
  putenv("SAN=$san");
}
//echo "count=" . count($sanIP) . "," . count($sanDNS) . " san: " . $san;
checkError($csr);

print "<pre>$csr</pre><br/>";
flush();
$scert = openssl_csr_sign($csr, $myCert, $privkey, $age, $config, getSerial());
flush();

checkError($scert);
print "Done<br/><br/>\n";

print "<b>Exporting X509 Certificate...</b><br/>";
flush();
checkError(openssl_x509_export($scert, $theirCert));
print "Done<br/><br/>\n";

print "<b>Saving X509 Certificate...</b><br/>";
$crtFile = "./openssl/crypto/certs/".substr(preg_replace('/[^a-z0-9\.-]+/', '_', $_REQUEST['name']),0,30).".crt";
if ($crtFile) {
                $fp = fopen($crtFile, "w");
                fputs($fp, $theirCert);
                fclose($fp);
        }
print "Done<br/><br/>\n";

checkError(openssl_x509_export($scert, $printOUT, false ));
//print "<b>Your certificate:</b>\n<pre>$theirCert</pre>\n";
print "<b>Your certificate:</b>\n<pre>$printOUT</pre>\n";

?>
<h1>Successfully signed certificate request with CA key.</h1>

