<?php
mustBeAdmin();
buildAdminMenu(urlPath());
$count = mysql_fetch_assoc(runQuery("SELECT COUNT(id) AS count FROM textual"));
?>
<h1>Content Manager <small>(<?php echo $count['count']?> pages)</small></h1>
<div class="page_controls">[ <a href="page_add"><?php echo icon('add')?> Add a new page</a> ]</div>
<?php
$sql = "SELECT id, section, title, date, locked FROM textual ORDER BY section";
$result = runQuery($sql);
if (mysql_num_rows($result)) {
	echo "<table id=\"view\" cellpadding=\"2\" cellspacing=\"1\">";
	echo "<tr class=\"e\"><th colspan=\"3\" align=\"center\">&nbsp;</th><th>Title</th><th>URL</th><th>Last Modified</th></tr>\n";
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class=\"$data\"><td class='admin_table_action'><a href=\"page_edit/" . $row['section'] . "\">" . icon('edit') . "</a></td><td class='admin_table_action'>";
		if ($row['locked'] == 0) {
			echo "<a href=\"javascript:cd('page_remove/" . $row['section'] . "', 'Are you sure you would like to delete the page <b>{$row['title']}</b> (<em>{$row['section']}</em>)?')\">" . icon('delete') . "</a>";
		} else {
			echo icon('locked');
		}
		echo "</td><td class='admin_table_action'><a href=\"{$row['section']}\">" . icon('view') . "</a></td><td>" . trimmer($row['title'], 50) . "</td><td><a href='{$row['section']}'>{$row['section']}</a></td><td>" . date("m/d/Y h:iA", strtotime($row['date'])) . "</td></tr>\n";
	}
	echo "</table>";
} else {
	abError("You do not have any pages created!", ABWARN);
}