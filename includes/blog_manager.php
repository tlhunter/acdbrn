<?php
mustBeAdmin();
buildAdminMenu(urlPath());
if (!posting() && (urlPath(1, 'edit') || urlPath(1, 'add'))) {
	?>
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript" src="scripts/jquery.ui.js"></script>
	<script type="text/javascript" src="scripts/jquery.ui.datepicker.js"></script>
	<link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/latest/themes/base/ui.all.css" rel="stylesheet" />

	<script type="text/javascript">
	$(document).ready(function(){
		$(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
	});
	</script>
	<?php
}
if (urlPath(1, 'remove')) {
	$sql = "DELETE FROM blog WHERE id = ".urlPath(2)." LIMIT 1";
	runQuery($sql);
	rmError("Blog has been deleted.", RMSUCCESS);
} else if (urlPath(1, 'add')) {
	if (!posting()) {
	wysiwyg();
?>
<form method="post" enctype="multipart/form-data">
<h1>Adding a new Blog</h1>
<table cellpadding="2" cellspacing="1" id="admin_site">
<tr><td>Title</td><td><input name="title" type="text" class="text" /></td></tr>
<tr><td>Date</td><td><input name="date" type="text" class="datepicker" value="<?php echo date("Y-m-d");?>" /></td></tr>
<tr><td>Time</td><td><?php echo dropdownGeneric("hours", date('H'), 'hour', 0, 'text shorter');?> : <input name="minute" value="<?php echo date('i');?>" maxlength="2" class="text shorter" /></td></tr>
<tr><td>Summary</td><td><textarea name="summary" class="text"></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Save Blog" /></td></tr>
</table>
<div id="wysiwyg">
<textarea name="content" id="wysiwyg_textarea"></textarea>
</div>
</form>
<?php
	} else {
		$date = $_POST['date'];
		$hour = $_POST['hour'] + 0;
		$minute = $_POST['minute'] + 0;
		$date = "$date $hour:$minute:00";
		$name = $_POST['title'];
		$summary = htmlentities($_POST['summary']);
		$content = $_POST['content'];
		$sql = "INSERT INTO blog SET name = '$name', created = '$date', summary = '$summary', content = '$content'";
		runQuery($sql);
	}
} else if (urlPath(1, 'edit')) {
	$eid = urlPath(2);
	if (empty($_POST)) {
		$sql = "SELECT * FROM blog WHERE id = $eid LIMIT 1";
		$result = runQuery($sql);
		$row = mysql_fetch_assoc($result);
		wysiwyg();
?>
<form method="post" enctype="multipart/form-data">
<h1>Modifying a Blog</h1>
<table cellpadding="2" cellspacing="1" id="admin_site">
<tr><td>Title</td><td><input name="title" type="text" class="text" value="<?php echo $row['name']?>" /></td></tr>
<tr><td>Date</td><td><input name="date" type="text" class="datepicker" value="<?php echo date("Y-m-d", strtotime($row['created']));?>" /></td></tr>
<tr><td>Time</td><td><?php echo dropdownGeneric("hours", date('H', strtotime($row['created'])), 'hour', 0, 'text shorter');?> : <input name="minute" value="<?php echo date('i', strtotime($row['created']));?>" maxlength="2" class="text shorter" /></td></tr>
<tr><td>Summary</td><td><textarea name="summary" class="text"><?php echo $row['summary']?></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Save Blog" /></td></tr>
</table>
<div id="wysiwyg">
<textarea name="content" id="wysiwyg_textarea"><?php echo $row['content']?></textarea>
</div>
</form>
<?php
	} else {
		$date = $_POST['date'];
		$hour = $_POST['hour'] + 0;
		$minute = $_POST['minute'] + 0;
		$date = "$date $hour:$minute:00";
		$name = $_POST['title'];
		$summary = htmlentities($_POST['summary']);
		$content = $_POST['content'];
		$sql = "UPDATE blog SET name = '$name', created = '$date', summary = '$summary', content = '$content' WHERE id = $eid LIMIT 1";
		runQuery($sql);
	}
}

$count = mysql_fetch_assoc(runQuery("SELECT COUNT(id) AS count FROM blog"));
echo "<h1>Blog Manager (".$count['count']." Blogs)</h1>\n";
?>
<div class="page_controls">[ <a href="<?php echo urlPath()?>/add"><?php echo icon('blog_add')?> Add a new Blog</a> ]</div>
<?php
$sql = "SELECT id, name, created, summary FROM blog ORDER BY created DESC";
$result = runQuery($sql);
if (mysql_num_rows($result)) {
	echo "<table id=\"view\" cellpadding=\"2\" cellspacing=\"1\">";
	echo "<tr class=\"e\"><th colspan=\"3\" width=\"48\" align='center'>&nbsp;</th><th>Title</th><th>Date</th></tr>\n";
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class=\"$data\"><td class='admin_table_action'><a href=\"" . urlPath() . "/edit/{$row['id']}\">" . icon('blog_edit') . "</a></td><td class='admin_table_action'>";
		echo "<a href=\"javascript:cd('" . urlPath() . "/remove/{$row['id']}')\">" . icon('blog_delete') . "</a>";
		echo "</td><td class='admin_table_action'><a href=\"blog/{$row['id']}\">" . icon('blog_view') . "</a></td><td>{$row['name']}</td>";
		echo "<td>" . date("n/j/Y", strtotime($row['created'])) . "</td></tr>\n";
	}
	echo "</table>";
} else {
	rmError("You don't have any blogs yet!", RMWARN);
}