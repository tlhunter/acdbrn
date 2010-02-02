<?php
mustBeAdmin();
buildAdminMenu(urlPath());
if (!empty($_POST)) {
	$max_nav = nukeNum($_POST['max_nav']);
	$sql = "UPDATE `site_integer` SET `value` = $max_nav WHERE `key` = 'max_nav'";
	runQuery($sql);
	foreach($_POST AS $key => $value) {
		if (strpos($key, 'page') === 0) {
			$id = substr($key, 4);
			$sql = "UPDATE textual SET nav_order = $value WHERE id = $id";
			runQuery($sql);
		}
	}	
	redirect("admin_navigation", 0, "Updating Site Navigation...");
} else {
	?>
<h1>Navigation Manager</h1>
<div class="page_controls">
	<?php
	$sql = "SELECT `value` FROM `site_integer` WHERE `key` = 'max_nav' LIMIT 1";
	$result = runQuery($sql);
	$row = mysql_fetch_assoc($result);
	?>
<form method="post">
<table align='center'><tr><td>Max Navigation Items:<br /><small class='t'>Template may override</small></td><td><input name="max_nav" value="<?php echo $row['value']?>" style='width: 20px;' /></td><td><input value="Save Navigation" type="submit" /></td></tr></table>
</div>
	<?php
	$sql = "SELECT id, title, nav_order FROM textual WHERE hidden = 0 ORDER BY nav_order ASC";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		while ($row = mysql_fetch_assoc($result)) {
			echo "<div class='nav_admin_button'><input name='page{$row['id']}' value='{$row['nav_order']}' style='width: 20px; font-size: 10px;' /> {$row['title']}</div>\n";
		}
		?>
</form>
<div style="line-height: 0px; clear: both;">&nbsp;</div>
	<?php
	} else {
		abError("You don't have any navigation defined! Create some pages first.", ABWARN);
	}
}