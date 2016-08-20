<h1>Create a Certificate Signing Request</h1>

<p>
Please fill in the following form which contains the basic information for your Certificate Request certificate. 
</p>

<form method="post" action="index.php">
<input type="hidden" name="area" value="csr"/>
<input type="hidden" name="stage" value="create">
	<fieldset>
		<legend><b>Certificate Request Data</b></legend>
                <table>
                <colgroup><col width="180px"></colgroup>
                <tr><th>Name</th><td><input type="text" name="name" value="" size="40"></td></tr>
                </table>

		<p>
		This information will be displayed inside every certificate that is issued by this software, however none of this information is specifically constrained, and you can pretty much type in whatever you like.
		</p>
		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Common Name</th><td><input type="text" name="dn[commonName]" value="some.domain.foo" size="40"></td></tr>
		<tr><th>Contact Email Address</th><td><input type="text" name="dn[emailAddress]" value="some.user@domain.foo" size="30"></td></tr>
		<tr><th>Organization Name</th><td><input type="text" name="dn[organizationName]" value="Some Org" size="25"></td></tr>
		<tr><th>Department Name</th><td><input type="text" name="dn[organizationalUnitName]" value="Development" size="30"></td></tr>
		</table>
		
		<p>
		The physical location of the above entity. Again, completely up to you regarding content, except for the country code which should be a proper ISO 2 letter country code.
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>City</th><td><input type="text" name="dn[localityName]" value="<? print $config['city']; ?>" size="25"></td></tr>
		<tr><th>State</th><td><input type="text" name="dn[stateOrProvinceName]" value="<? print $config['state']; ?>" size="25"></td></tr>
		<tr><th>Country</th><td><input type="text" name="dn[countryName]" value="<? print $config['country']; ?>" size="2"></td></tr>
		</table>
		
<!--		<p>
		The validity age for the certificate
		</p>
		
		<table>
		<colgroup><col widht="180px"></colgroup>
		<tr><th>Age</th>
			<td>Age:<td><select name="age" rows="6">
			<option value="365" selected="selected">1 year</option>
			<option value="730">2 years</option>
			<option value="1825">5 years</option>
			<option value="3650">10 years</option>
			<option value="7300">20 years</option>
			</select>
		</td></tr>
		</table>
-->
                <p>
		Extra attributes (leave empty if you don't know how to use this).
                </p>

                <table>
                <colgroup><col width="180px"></colgroup>
                <tr><th>SAN IP</th><td><input type="text" name="sanIP" value="" size="40"></td></tr>
                <tr><th>SAN DNS</th><td><input type="text" name="sanDNS" value="" size="40"></td></tr>
                </table>

		<p>
		And where would we be without the pass phrase? (Leave empty for no passphrase).
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Private Key Passphrase</th><td><input type="text" name="dn[passPhrase]" value="" size="40"></td></tr>
		<tr><td colspan=2 style="text-align: center;"><input type="submit" value="Create Certificate Signing Request"></td></tr>
		</table>
	</fieldset>
</form>
