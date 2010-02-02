<?php
if (!empty($_POST)) {

	$user = nukeAlphaNum($_POST['username']);
	$pass = ($_POST['password']);

	if (($user == CMS_USER) && ($pass == CMS_PASS)) {
		$_SESSION['isadmin'] = 1;
		redirect('admin');
	} else {
		rmError("The supplied username/password is incorrect. <a href='login'>Try Again</a>.", RMERROR);
	}
	
} else {
?>
<h1>Administrator Login</h1>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<div align="center">
<form action="login" method="post">
<table>
<tr><td align="right"><strong>Username:</strong></td><td><input type="text" name="username" /></td></tr>
<tr><td align="right"><strong>Password:</strong></td><td><input type="password" name="password" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Login" /></td></tr>
</table>
</form>
</div>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<center>RenownedCMS developed by <a href="http://www.renownedmedia.com">Renowned Media</a>.</center>
<?php
}