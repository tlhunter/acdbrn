<?php
mustBeAdmin();

if (!posting()) {
	echo "<h2>Add a new page</h2>\n";
	wysiwyg();
	$name = urlPath(1);
	if ($name) {
		$sectionwrite = "";
		$sectionreadonly = " readonly";
	} else {
		$sectionwrite = " onblur=\"this.form.name.value = url_friendly(this.value, 40)\"";
		$sectionreadonly = "";
	}
	echo "<script src=\"scripts/url_friendly.js\" language=\"Javascript\"></script>";
	echo "<div id=\"edit_section\">\n<form action=\"page_add\" method=\"post\">\n";
	echo "<table border='0'>";
	echo "<tr><td><strong>Page Title:</strong></td><td><input type=\"text\" name=\"title\"$sectionwrite maxlength=\"100\" size=\"60\" /></td></tr>\n";
	echo "<tr><td><strong>Simple Name:</strong></td><td><input type=\"text\" name=\"name\" value=\"$name\" maxlength=\"40\" size=\"40\"$sectionreadonly /></td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"Create Page\" /></td></tr>";
	echo "</table>\n";
	echo "<br />\n<br />\n<strong>Content:</strong>\n<div id=\"wysiwyg\">\n<textarea name=\"text\" id=\"wysiwyg_textarea\"></textarea>\n</div>\n";
	if (!strpos($name,"_sidebar")) {
		echo "<br />\n<br />\n<b>Meta Description</b>:<div>\n<textarea name=\"meta_desc\" id=\"meta_desc\" style=\"width: {$nuke['integer']['content_width']}px;\"></textarea>\n</div>\n";
	}
	echo "</form>\n</div>\n";
} else {
	$section = nukeAlphaNum($_POST['name']);
	$title = htmlentities($_POST['title']);
	$text = $_POST['text'];
	$meta_desc = $_POST['meta_desc'];

	if (strpos($section,"_sidebar")) {
		$hidden = 1;
	} else {
		$hidden = 0;
	}
	$sql = "INSERT INTO textual SET title = '$title', text = '$text', section = '$section', meta_desc = '$meta_desc', hidden = $hidden";
	runQuery($sql);

	if (strpos($section, "_sidebar")) {
		$original_page = substr($section, 0, strlen($section)-8);
		abError("Sidebar has been added. You can link to it by using the file name <a href=\"$original_page\">$original_page</a>.", ABSUCCESS);
	} else {
		abError("Section has been added. You can link to it by using the file name <a href=\"$section\">$section</a>.\n", ABSUCCESS);
		pingGoogleSitemap();
	}
}