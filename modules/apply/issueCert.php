<?

	if (get_magic_quotes_gpc()) {
		$emailAddress = stripslashes($_REQUEST['emailAddress']);
		$secret = stripslashes($_REQUEST['secret']);
	}
	else {
		$emailAddress = &$_REQUEST['emailAddress'];
		$secret = &$_REQUEST['secret'];
	}

	if (!$_REQUEST['emailAddress']) {
		printHeader("Error");
		print "<h1>Error</h1>\n";
		print "Your email address was not found in the input.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply\">Try again</a><br/>\n";
		printFooter();
	}

	elseif (!$_REQUEST['secret']) {
		printHeader("Error");
		print "<h1>Error</h1>\n";
		print "Your secret was not found in the input.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply&stage=enterKey&sent=1&emailAddress=".urlencode($emailAddress)."\">Try again</a><br/>\n";
		printFooter();
	}

	elseif (md5($config['passPhrase'] . $config['entropy'] . $emailAddress) != $secret) {
		printHeader("Error");
		print "<h1>Error</h1>\n";
		print "Your secret does not seem to be correct. Make sure it has no spaces and is exactly as it appears in your email.<br/>\n";
		print "--&gt; <a href=\"index.php?area=apply&stage=enterKey&sent=1&emailAddress=".urlencode($emailAddress)."\">Try again</a><br/>\n";
		printFooter();
	}

	else {
		$inhead = <<<HTML
<!-- Use the Microsoft ActiveX control to generate the certificate -->
<object classid="clsid:127698e4-e730-4e5c-a2b1-21490a70c8a1" codebase="/certcontrol/xenroll.dll" id="certHelper">
</object>
<script type="text/javascript">
<!--
var ie = (document.all && document.getElementById);
var ns = (!document.all && document.getElementById);

function GenReq()
{
	var szName = "";
	var objID = "1.3.6.1.4.1.311.2.1.21";

	szName = "";

	if (document.GenReqForm.emailAddress.value == "") {
		alert("No email Address");
		return false;
	} 
	else szName = "E=" + document.GenReqForm.emailAddress.value;

	if (document.GenReqForm.commonName.value == "") {
		alert("No Common Name");
		return false;
	} 
	else szName = szName + ", CN=" + document.GenReqForm.commonName.value;

	if (document.GenReqForm.countryName.value == "") {
		alert("No Country");
		return false;
	}
	else szName = szName + ", C=" + document.GenReqForm.countryName.value;

	if (document.GenReqForm.stateOrProvinceName.value == "") {
		alert("No State or Province");
		return false;
	}
	else szName = szName + ", S=" + document.GenReqForm.stateOrProvinceName.value;

	if (document.GenReqForm.localityName.value == "") {
		alert("No City");
		return false;
	}
	else szName = szName + ", L=" + document.GenReqForm.localityName.value;

	if (document.GenReqForm.organizationName.value == "") {
		alert("No Organization");
		return false;
	}
	else szName = szName + ", O=" + document.GenReqForm.organizationName.value;

	if (document.GenReqForm.organizationalUnitName.value == "") {
		alert("No Organizational Unit");
		return false;
	}
	else szName = szName + ", OU=" + document.GenReqForm.organizationalUnitName.value;

	if (!ie) return true;

	certHelper.KeySpec = 1;
	certHelper.GenKeyFlags = 0x04000003;
	certHelper.ProviderName = "";

	try {
		sz10 = certHelper.CreatePKCS10(szName, objID);
	}
	catch (e) {
		alert ("Error generating request");
		return false;
	}

	if (sz10 != "") {
		document.GenReqForm.reqEntry.value = sz10;
	}
	else {
		alert("Key Pair Generation failed");
		return false;
	}
}
//-->
</script>
HTML;
		printHeader("Generate keypair and cert request", $inhead);
	
?>
<h1>Generate a key pair and client certificate request</h1>

<p>
Please fill in the following form which contains the basic information for your certificate. 
</p>

<form method="post" action="index.php" name="GenReqForm" onSubmit="return GenReq();">
<input type="hidden" name="area" value="apply">
<input type="hidden" name="stage" value="signCert">
<input type="hidden" name="secret" value="<?=htmlspecialchars($secret)?>">
	<fieldset>
		<legend>Information about you</legend>
		<p>
		This is where you type in information about yourself. Most of the fields are prefilled for you, all you need to do is adjust what is in the boxes, until it looks right.
		</p>

		<p>
		This information will be displayed inside the certificate that will be issued to you.
		</p>
		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Your full name</th><td><input type="text" id="commonName" name="dn[commonName]" value="John Smith" size="40"></td></tr>
		<tr><th>Your email Address</th><td><input type="text" id="emailAddress" name="dn[emailAddress]" value="<?=htmlspecialchars($emailAddress)?>" size="30"></td></tr>
		<tr><th>Organization Name</th><td><input type="text" id="organizationName" name="dn[organizationName]" value="<?=htmlspecialchars($config['orgName'])?>" size="25"></td></tr>
		<tr><th>Department Name</th><td><input type="text" id="organizationalUnitName" name="dn[organizationalUnitName]" value="Staff" size="30"></td></tr>
		</table>
		
		<p>
		Where abouts are you located?
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>City</th><td><input type="text" id="localityName" name="dn[localityName]" value="<?=htmlspecialchars($config['city'])?>" size="25"></td></tr>
		<tr><th>State</th><td><input type="text" id="stateOrProvinceName" name="dn[stateOrProvinceName]" value="<?=htmlspecialchars($config['state'])?>" size="25"></td></tr>
		<tr><th>Country</th><td><input type="text" id="countryName" name="dn[countryName]" value="<?=htmlspecialchars($config['country'])?>" size="2"></td></tr>
<script type="text/javascript">
if (!ns)
	document.write("<!"+"--");
</script>
		</table>
		
		<p>
		Please choose a cypher strength (we recommend the highest possible value available)
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Strength</th><td><keygen name="SPKAC" challenge="challengePassword"></td></tr>
<!-- end Netscape specific segment -->

		<tr><td colspan=2 style="text-align: right;"><input type="submit" value="Create Certificate"></td></tr>
		</table>
	</fieldset>
<input type="hidden" name="reqEntry">
</form>
<?

		printFooter();
	}

?>
