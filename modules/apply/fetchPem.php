<?

//print "Install cert:<br/><br/>"; flush();
	if (get_magic_quotes_gpc()) {
		$id = stripslashes($_REQUEST['id']);
	}
	else {
		$id = &$_REQUEST['id'];
	}

	$certFile = "./openssl/crypto/certs/$id.pem";
	if (file_exists($certFile)) {
		$fp = fopen($certFile, 'r');
		$myCert = fread($fp, filesize($certFile));

		header("Content-Type: application/x-x509-user-cert");
		print $myCert;
	}
	else {
		printHeader("Certificate Retrieval");
		print "<h1>X509 user certificate not found</h1>\n";
		printFooter();
	}

?>
