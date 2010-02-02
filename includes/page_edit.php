<?php
mustBeAdmin();

if (posting()) {
	$title = htmlentities($_POST['title']);
	$text = $_POST['text'];
	$meta_desc = $_POST['meta_desc'];
	$sql = "UPDATE textual SET title = '$title', text = '$text', meta_desc = '$meta_desc' WHERE section = '".urlPath(1)."' LIMIT 1 ";
	runQuery($sql);
	redirect(urlPath(1),0,"Changes have been set. <a href=\"" . urlPath(1) . "\">View page</a>.");
} else {
	wysiwyg();
	$section = urlPath(1);
	$sql = "SELECT * FROM textual WHERE section = '$section' LIMIT 1";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_assoc($result);
		echo "<div id=\"edit_section\">\n<form action=\"page_edit/$section\" method=\"post\">\n";
		echo "<h2>Editing page {$row['section']}</h2>\n";
		echo "<br />\n<table border='0'><tr><td><strong>Page Title:</strong></td><td><input type=\"text\" name=\"title\" value=\"{$row['title']}\" maxlength=\"100\" size=\"60\" /></td></tr>\n";
		echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"Save Changes\" /></td></tr>\n";
		echo "</table>\n";
		
		echo "<br />\n<br />\n<div id=\"wysiwyg\">\n<textarea name=\"text\" id=\"wysiwyg_textarea\">{$row['text']}</textarea>\n</div>\n";
		if (!strpos($row['section'],"_sidebar")) {
			echo "<br />\n<br />\n<b>Meta Description</b>:<div>\n<textarea name=\"meta_desc\" id=\"meta_desc\" style=\"width: {$nuke['integer']['content_width']}px;\">{$row['meta_desc']}</textarea>\n</div>\n";
		}
		echo "</form>\n</div>\n";
	} else {
		echo "<div class=\"error\">The page '$section' does not exist!<br />\n<a href='page_add/$section'>Creat Page</a>\n</div>\n";
	}
}