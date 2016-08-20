<h1>Welcome to PHP-CA</h1>

<p>
PHP-CA is a simple CA (Certificate Authority) meant to be run by organizations that have a simple need for a CA to issue certificates to employees, without the need for a full scale authority.
</p>

<p>
Simply put PHP-CA is a basic authority that supports proof of identity by sending an email with a key to a specified email address.
</p>

<p>
Reciept of this email proves identity and then that person can issue certificates based upon the email address.
</p>

<p>
The certificates issued can be used as proof of person owning the email address in the signature, and trust of the issuer can be used to allow email address holders to be allowed access to various corporate sites/administration areas.
</p>

<h1>First time use of this software</h1>

<p>
Since this is the first time you have used this software, it is time to setup your Organization, and base CA cert.
</p>

<p>
Please fill in the following form which contains the basic information for your CA's certificate. 
</p>

<form method="post" action="index.php">
<input type="hidden" name="stage" value="create">
	<fieldset>
		<legend>CA Certificate Data</legend>
		<p>
		This is where you type in information about your organization, and the department that is responsible for the running of your CA
		</p>

		<p>
		This information will be displayed inside every certificate that is issued by this software, however none of this information is specifically constrained, and you can pretty much type in whatever you like.
		</p>
		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Company Name</th><td><input type="text" name="dn[commonName]" value="ABC Widgets Certificate Authority" size="40"></td></tr>
		<tr><th>Contact Email Address</th><td><input type="text" name="dn[emailAddress]" value="cert@abcwidgets.com" size="30"></td></tr>
		<tr><th>Organization Name</th><td><input type="text" name="dn[organizationName]" value="ABC Widgets" size="25"></td></tr>
		<tr><th>Department Name</th><td><input type="text" name="dn[organizationalUnitName]" value="Certification" size="30"></td></tr>
		</table>
		
		<p>
		The physical location of the above entity. Again, completely up to you regarding content, except for the country code which should be a proper ISO 2 letter country code.
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>City</th><td><input type="text" name="dn[localityName]" value="Beverly Hills" size="25"></td></tr>
		<tr><th>State</th><td><input type="text" name="dn[stateOrProvinceName]" value="California" size="25"></td></tr>
		<tr><th>Country</th><td><input type="text" name="dn[countryName]" value="US" size="2"></td></tr>
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
		And where would we be without the pass phrase?
		</p>

		<p>
		This is your key to the administration area, and also the phrase that encrypts the private key.
		</p>

		<p>
		It should be relatively long if at all possible, however it is stored on the drive in cleartext, because we need to be able to sign certs on-demand.
		</p>

		<p>
		Please make sure that the configuration file is well protected.
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Passphrase</th><td><input type="text" name="passPhrase" value="One, two, three, four, and maybe a few words even more!" size="60"></td></tr>
		<tr><td colspan=2 style="text-align: right;"><input type="submit" value="Create CA"></td></tr>
		</table>
	</fieldset>
</form>
