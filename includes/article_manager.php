<?php
mustBeAdmin();
buildAdminMenu(urlPath());
if (urlPath(1, 'remove')) {
	runQuery("DELETE FROM article WHERE id = ".urlPath(2)." LIMIT 1");
	rmError("Article has been deleted.", RMERROR);
} else if (urlPath(1, 'add')) {
	if (!posting()) {
	wysiwyg();
?>
<script src="scripts/url_friendly.js" language="Javascript"></script>
<form method="post" enctype="multipart/form-data">
<h1>Adding a new Article</h1>
<table cellpadding="2" cellspacing="1" id="admin_site">
<tr><td>Title</td><td><input name="title" type="text" class="text" onblur="this.form.url.value = url_friendly(this.value, 40)" maxlength="100" size="60" /></td></tr>
<tr><td>Url</td><td><input name="url" type="text" class="text" maxlength="40" size="40" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Save Article" /></td></tr>
</table>
<div id="wysiwyg">
<textarea name="content" id="wysiwyg_textarea"></textarea>
</div>
</form>
<?php
	} else {
		$url = nukeAlphaNum($_POST['url']);
		$name = htmlentities($_POST['title']);
		$content = $_POST['content'];
		runQuery("INSERT INTO article SET name = '$name', url = '$url', content = '$content'");
	}
} else if (urlPath(1,'edit')) {
	
	if (!posting()) {
		$sql = "SELECT * FROM article WHERE id = ".urlPath(2)." LIMIT 1";
		$result = runQuery($sql);
		$row = mysql_fetch_assoc($result);
		wysiwyg();
?>
<form method="post" enctype="multipart/form-data">
<h1>Modifying an Article</h1>
<table cellpadding="2" cellspacing="1" id="admin_site">
<tr><td>Title</td><td><input name="title" type="text" class="text" value="<?php echo $row['name']?>" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Save Article" /></td></tr>
</table>
<div id="wysiwyg">
<textarea name="content" id="wysiwyg_textarea"><?php echo $row['content']?></textarea>
</div>
</form>
<?php
	} else {
		$name = $_POST['title'];
		$content = $_POST['content'];
		$sql = "UPDATE article SET name = '$name', content = '$content' WHERE id = ".urlPath(2)." LIMIT 1";
		runQuery($sql);
	}
}

$count = mysql_fetch_assoc(runQuery("SELECT COUNT(id) AS count FROM article"));
echo "<h1>Article Manager <small>(".$count['count']." Articles)</small></h1>\n";
?>
<div class="page_controls">[ <a href="article_manager/add"><?php echo icon('article_add')?> Add a new Article</a> ]</div>
<?php
$sql = "SELECT * FROM article ORDER BY created DESC";
$result = runQuery($sql);
if (mysql_num_rows($result)) {
	echo "<table id=\"view\" cellpadding=\"2\" cellspacing=\"1\">";
	echo "<tr class=\"e\"><th colspan=\"3\" width=\"48\" align='center'>&nbsp;</th><th>Title</th><th>URL</th><th>Date</th></tr>\n";
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class=\"$data\"><td class='admin_table_action'><a href=\"article_manager/edit/{$row['id']}\">" . icon('article_edit') . "</a></td><td class='admin_table_action'>";
		echo "<a href=\"javascript:cd('article_manager/remove/{$row['id']}')\">" . icon('article_delete') . "</a>";
		echo "</td><td class='admin_table_action'><a href=\"articles/{$row['url']}\">" . icon('article_view') . "</a></td><td>{$row['name']}</td><td>{$row['url']}</td>";
		echo "<td>" . dateDb("n/j/Y", $row['created']) . "</td></tr>\n";
	}
	echo "</table>";
} else {
	rmError("You haven't written any articles yet!", RMWARN);
}