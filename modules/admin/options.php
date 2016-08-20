
<h1>Admin options</h1>
<p>
<fieldset>
<legend><b>Re-issue CA Certificate</b></legend>
<form action="index.php" method="post">
<input type="hidden" name="area" value="<?=htmlspecialchars($_REQUEST['area'])?>"/>
<input type="hidden" name="stage" value="renewCert"/>
<table style="width: 350px;">
<tr><td width=100>Passphrase:<td><input type="password" name="pass"/>
<tr><td>Age:<td><select name="age" rows="6">
<option value="365" selected="selected">1 year</option>
<option value="730">2 years</option>
<option value="1825">5 years</option>
<option value="3650">10 years</option>
<option value="7300">20 years</option>
</select>
<tr><td><td><input type="submit" value="Reissue CA Cert"/>
</table>
</form>
</fieldset>
</p>
<p>
<fieldset>
<legend><b>Sign a Certificate Request</b></legend>
<form action="index.php" method="post">
<input type="hidden" name="area" value="<?=htmlspecialchars($_REQUEST['area'])?>"/>
<input type="hidden" name="stage" value="certSign"/>
<table style="width: 350px;">
<tr><td width=100>Passphrase:<td><input type="password" name="pass"/>
<tr><td>Name:<td><input type="text" name="name"/>
<tr><td>Age:<td><select name="age" rows="6">
<option value="365" selected="selected">1 year</option>
<option value="730">2 years</option>
<option value="1825">5 years</option>
<option value="3650">10 years</option>
<option value="7300">20 years</option>
</select>
<tr><td colspan=2>Request:<br/>
<textarea name="request" cols="50" rows="6"></textarea><br/>
<tr><td>SAN IP:<td><input type="text" name="sanIP">
<tr><td>SAN DNS:<td><input type="text" name="sanDNS">
<tr><td><td><input type="submit" value="Sign Cert"/>
</table>
</form>
</fieldset>
</p>

<p>
<fieldset>
<legend><b>Download a Certificate</b></legend>
<form action="index.php" method="post">
<input type="hidden" name="area" value="main"/>
<input type="hidden" name="stage" value="getCert"/>
<table style="width: 350px;">
<tr><td>Name:<td><select name="name" rows="6">
<option value="">--- Select a certificate</option>
<?

$dh = opendir("./openssl/crypto/certs");
while (($file = readdir($dh)) !== false) {                                     
        if (substr($file, -4) == ".crt") {
                $name = substr($file, 0, -4);
		$fmtime = date ("Ymd H:i:s", filemtime("./openssl/crypto/certs/" . $file));
                print "<option value=\"$name\">$name ($fmtime)</option>";
        }
}

?>
</select>
<tr><td><td><input type="submit" value="Get Cert"/>
</table>
</form>
</fieldset>
</p>

<p>
<fieldset>
<legend><b>Download a Private key</b></legend>
<form action="index.php" method="post">
<input type="hidden" name="area" value="main"/>
<input type="hidden" name="stage" value="getKey"/>
<table style="width: 350px;">
<tr><td>Name:<td><select name="name" rows="6">
<option value="">--- Select a private key</option>
<?

$dh = opendir("./openssl/crypto/keys");
while (($file = readdir($dh)) !== false) {
       	if (substr($file, -4) == ".key") {
               	$name = substr($file, 0, -4);
               	$fmtime = date ("Ymd H:i:s", filemtime("./openssl/crypto/keys/" . $file));
               	print "<option value=\"$name\">$name ($fmtime)</option>";
       	}
}
?>
</select>
<tr><td><td><input type="submit" value="Get Private Key"/>
</table>
</form>
</fieldset>
</p>

<p>
<fieldset>
<legend><b>Re-sign a Certificate Request</b></legend>
<form action="index.php" method="post">
<input type="hidden" name="area" value="<?=htmlspecialchars($_REQUEST['area'])?>"/>
<input type="hidden" name="stage" value="certSign"/>
<table style="width: 350px;">
<tr><td width=100>Passphrase:<td><input type="password" name="pass"/>
<tr><td>Name:<td><select name="name" rows="6">
<option value="">--- Select a certificate</option>
<?

$dh = opendir("./openssl/crypto/requests");
while (($file = readdir($dh)) !== false) {
	if (substr($file, -4) == ".csr") {
		$name = substr($file, 0, -4);
                $fmtime = date ("Ymd H:i:s", filemtime("./openssl/crypto/requests/" . $file));
                print "<option value=\"$name\">$name ($fmtime)</option>";
	}
}

?>
</select>
<tr><td>Age:<td><select name="age" rows="6">
<option value="365" selected="selected">1 year</option>
<option value="730">2 years</option>
<option value="1825">5 years</option>
<option value="3650">10 years</option>
<option value="7300">20 years</option>
</select>
<tr><td><td><input type="submit" value="Sign Cert"/>
</table>
</form>
</fieldset>
</p>

<p>
<b>Process to generate a key+signing request:</b><br/>
<pre>
echo -n "Enter your domain name: "; read DOMAIN && \
openssl genrsa -out $DOMAIN.key 2048 && \
openssl req -new -key $DOMAIN.key -out $DOMAIN.csr && \
cat $DOMAIN.csr
</pre>
</p>

<p>
<b>My Certificate (<a href="index.php?area=main&stage=trust">open</a>)</b><br/>
<pre style="font-size: 75%;"><?=join("", file("./openssl/crypto/cacerts/cacert.pem"))?></pre>
</p>
