<?
if (get_magic_quotes_gpc()) {
  $crtFile = stripslashes($_REQUEST['name']);
} else {
  $crtFile = &$_REQUEST['name'];
}
$certFile = "./openssl/crypto/keys/" . $crtFile . ".key";
if (file_exists($certFile)) {
//  $myCert = join("", file($certFile));
//  header("Content-Type: application/x-x509-server-cert");
//  print $myCert;
  $fp = fopen($certFile, "r");
  $theirCert = fread($fp, filesize($certFile));
  fclose($fp);
//  $theirCert = openssl_x509_parse($theirCert);
  openssl_pkey_export($theirCert, $printOUT );
//  openssl_x509_export($theirCert, $printOUT, false );
  printHeader("Private Key encoded view");
  print "<h1>Private Key encoded view</h1>\n";
//print "<b>Your certificate:</b>\n<pre>$printOUT</pre>\n";
?>
<p>
<b>Certificate name: <?=htmlspecialchars($crtFile)?></b><br/>
<!-- <pre style="font-size: 100%;"><?=join("", file($certFile))?></pre> -->
<pre style="font-size: 100%;"><?=$printOUT ?></pre>
</p>

<?
printFooter();

} else {
  printHeader("Private Key Retrieval");
  print "<h1>X509 certificate not found</h1>\n";
  printFooter();
}

?>
