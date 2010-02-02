<h1>Contact Us</h1>
<?php
    if (!empty($_POST)) {
		if ($nuke['bool']['use_captcha'] && $_POST['captcha'] != $_SESSION['security_code']) {
			rmError("The code you provided does not match the image.", RMERROR);
		} else if (!nukeValidEmail($_POST['email']) || strlen($_POST['name']) < 4) {
			rmError("Please provide your name and email.");
		} else {
			$to = $nuke['varchar']['admin_email'];
			$email = htmlentities($_POST['email']);
			$name = htmlentities($_POST['name']);
			$body = '';
			foreach($_POST as $key => $value) {
				$body .= "$key: $value\n";
			}
			if (@mail($to, $nuke['varchar']['title_prefix'] . " Form Submission", $body, "From: $name <$email>")) {
				textual("contact_thanks");
			} else {
				rmError("Message delivery failed. Please try again in a few minutes.", RMERROR);
			}
		}
	} else {
?>
<form action="<?php echo urlPath()?>" method="post">
<table>
<tr><td align="right">Name:</td><td><input name="name" class="contactual" /></td></tr>
<tr><td align="right">Email:</td><td><input name="email" class="contactual" /></td></tr>
<tr><td align="right" valign="top">Message:</td><td><textarea id="text" name="comment" class="contactual" style="height: 120px;"></textarea></td></tr>
<?php if ($nuke['bool']['use_captcha']) { ?>
<tr><td align="right">Code:</td><td><img src="captcha.png" /></td></tr>
<tr><td align="right">Type Code:</td><td><input name="captcha" class="contactual" /></td></tr>
<?php } ?>
<tr><td></td><td align='center'><input type="submit" value="Send Message" /></td></tr>
</table>
</form>
<?php }
textual(urlPath());
if ($nuke['bool']['use_map']) {
?>
<br />
<center>
<iframe width="<?php echo $nuke['integer']['map_width']?>" height="<?php echo $nuke['integer']['map_height']?>" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $nuke['text']['map_embed_code']?>"></iframe>
</center>
<?php
}