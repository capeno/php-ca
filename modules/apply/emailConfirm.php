<?

$domain = preg_replace('/[^@]+@/','', $config['contact']);

?>
<h1>Email confirmation page.</h1>

<p>
Basically what I'm going to do here is ask you for your email address.
</p>

<p>
I will then send you an email, and it will have a secret message inside it.
</p>

<p>
On the next page I will ask you for that secret.
</p>


<form method="post" action="index.php">
<input type="hidden" name="area" value="apply">
<input type="hidden" name="stage" value="enterKey">
	<fieldset style="width: 400px;">
		<legend>Email address validation</legend>
		<p>
		Please enter your email address in the following box:
		</p>

		<table>
		<colgroup><col width="180px"></colgroup>
		<tr><th>Email Address</th><td><input type="text" name="emailAddress" value="me@<?=htmlspecialchars($domain)?>" size="30"></td></tr>
		<tr><td colspan=2 style="text-align: right;"><input type="submit" value="Send the secret"></td></tr>
		</table>
	</fieldset>
</form>


