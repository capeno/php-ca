<?

	$certReady = false;

	if (get_magic_quotes_gpc()) {
		$emailAddress = stripslashes($_REQUEST['dn']['emailAddress']);
		$secret = stripslashes($_REQUEST['secret']);
		$SPKAC = stripslashes($_REQUEST['SPKAC']);
		$reqEntry = stripslashes($_REQUEST['reqEntry']);
		while (list($key, $val) = each($_REQUEST['dn'])) {
			$dn[$key] = stripslashes($val);
		}
	}
	else {
		$emailAddress = &$_REQUEST['dn']['emailAddress'];
		$secret = &$_REQUEST['secret'];
		$reqEntry = &$_REQUEST['reqEntry'];
		$SPKAC = &$_REQUEST['SPKAC'];
		$dn = &$_REQUEST['dn'];
	}

	if (!$emailAddress) {
		printHeader("Email address not found");
		print "<h1>Missing email address</h1>";
		print "Your email address was not found in the input.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply\">Try again</a><br/>\n";
		printFooter();
	}

	elseif (!$secret) {
		printHeader("Secret not found");
		print "<h1>Missing secret</h1>";
		print "Your secret was not found in the input.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply&stage=enterKey&sent=1&emailAddress=".urlencode($emailAddress)."\">Try again</a><br/>\n";
		printFooter();
	}

	elseif (md5($config['passPhrase'] . $config['entropy'] . $emailAddress) != $secret) {
		printHeader("Secret incorrect");
		print "<h1>Incorrect secret</h1>";
		print "Your secret does not seem to be correct. Make sure it has no spaces and is exactly as it appears in your email.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply&stage=enterKey&sent=1&emailAddress=".urlencode($emailAddress)."\">Try again</a><br/>\n";
		printFooter();
	}

	elseif ($SPKAC) {
		// We have the Netscape request.

		// Unfortunatly PHP does not have the functionality built in as yet to be able to support SPKAC requests.
		// If we cannot find the command line OPENSSL utility we will have to deny this request.

		$guessLocations = array(
			'/usr/bin/openssl',
			'/usr/local/bin/openssl',
			'/usr/local/openssl/bin/openssl',
			'c:/program files/openssl/bin/openssl',
			'c:/openssl/bin/openssl'
		);

		$cmdSSL = '';
		foreach ($guessLocations as $location) {
			if (file_exists($location)) {
				$cmdSSL = $location;
				break;
			}
		}

		if ($cmdSSL) {
			// Wow, we have it installed...
			$cwd = getcwd();
			@mkdir("$cwd/openssl/crypto/certs", 0700);

			$dn['SPKAC'] = preg_replace('/[\r\n]+/', '', $SPKAC);

			$spkacFile = "$cwd/openssl/crypto/certs/temp_$secret";
			$spkacOut = "$cwd/openssl/crypto/certs/spkac_$secret";
			if ($fp = fopen($spkacFile, 'w')) {
				while (list($key, $val) = each($dn)) {
					fputs($fp, "$key = $val\n");
				}
				fclose($fp);
			}
			else {
				printHeader("Error");
				print "<h1>Error</h1>";
				print "Error opening Spkac file, check permissions<br/>\n";
				printFooter();
				return;
			}

			fclose(fopen("$cwd/openssl/crypto/index.txt", "w"));

			$command = "(cd $cwd; $cmdSSL ca -spkac $spkacFile -out $spkacOut -days 365 -key \"".addslashes($config['passPhrase'])."\" -config \"".addslashes(getcwd())."/openssl/openssl.conf\") 2>&1";
			exec($command, $out, $ret);

			if ($ret != 0) {
				printHeader("Error");
				print "<h1>Error</h1>";
				print "Error executing CA command ($ret):<br/><pre>".join("\n", $out)."</pre>\n";
				printFooter();
				return;
			}
			unlink($spkacFile);

			$command = "(cd $cwd; $cmdSSL spkac -in $spkacOut -verify)";
			exec($command, $out, $ret);

			$myCert = "";
			foreach ($out as $pos => $line) {
				if (substr($line, 0,8) == "        ") {
					$myCert .= substr($line, 8)."\n";
				}
			}

			$certReady = 'spkac';
		}
	}

	elseif (!$reqEntry) {
		printHeader("No CSR sent");
		print "<h1>No CSR was sent</h1>";
		print "Your browser did not appear to send me a CSR.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply&stage=issueCert&emailAddress=".urlencode($emailAddress)."&secret=".urlencode($secret)."\">Try again</a><br/>\n";
		printFooter();
	}

	else {
		$clientCSR = "-----BEGIN CERTIFICATE REQUEST-----\n" . chunk_split(preg_replace('/[\r\n]+/', '', $reqEntry), 64) . "-----END CERTIFICATE REQUEST-----\n";
		
		
		if ($fp=fopen("openssl/crypto/cacerts/cacert.pem","r")) {
			$certData=fread($fp,8192);
			fclose($fp);
		}
		else {
			printHeader("Error");
			print "<h1>Error</h1>";
			print "Error reading my cert, check permissions<br/>\n";
			printFooter();
			return;
		}
		$caCert = openssl_x509_read($certData);
		checkError($caCert);

		if ($fp=fopen("openssl/crypto/keys/cakey.pem","r")) {
			$privKey=fread($fp,8192);
			fclose($fp);
		}
		else {
			printHeader("Error");
			print "<h1>Error</h1>";
			print "Error reading my key, check permissions<br/>\n";
			printFooter();
			return;
		}
		$caKey = openssl_get_privatekey($privKey,$config['passPhrase']);
		checkError($caKey);

		$signedCert = openssl_csr_sign($clientCSR, $caCert, $caKey, 365, $config, getSerial());
		checkError($signedCert);
		
		openssl_x509_export($signedCert, $myCert, false);

		$certReady = 'xenroll';
	}


	if ($certReady) {
		if ($certReady == 'xenroll') {
			$cert = addslashes(preg_replace('/^.*-{5}([^ ]+)-{5}.*$/', '$1', preg_replace('/\n/', '', $myCert)));
			$inhead = <<<HTML
<!-- Use the Microsoft ActiveX control to generate the certificate -->
<object classid="clsid:127698e4-e730-4e5c-a2b1-21490a70c8a1" codebase="/certcontrol/xenroll.dll" id="certHelper">
</object>
<script type="text/javascript">
<!--

function InstallCert(cert)
{
    if (!cert) {
		alert("No certificate found");
		return false;
    } 

	try {
	    certHelper.acceptPKCS7(cert);
	}
	catch (e) {
		alert ("Error accepting certificate");
		return false;
	}
}

var cert = "$cert";
InstallCert(cert);

//-->
</script>
HTML;
		}

		printHeader("Client certificate install", $inhead);
?>
<h1>Client certificate installation</h1>

<p>
<?

		if ($certReady == 'spkac') {

			print "Your client certificate is prepared. Please <a href=\"index.php?area=apply&stage=fetchSpkac&id=$secret\">Install it now</a>; Note: your browser may seem to do nothing, but the certificate will be installed.";

		}

		else {

			print "Hopefully, by the time you read this, you should have your brand new certificate all installed.\n";

		}

?>
<br/>
Once your certificate is installed, you will probably want to <a href="index.php?area=main&stage=trust"> Install this CA as a trusted root authority </a>
</p>

<b>Your certificate</b>:<br/>
<pre><?=$myCert?></pre>

<?
		printFooter();

	}

?>
