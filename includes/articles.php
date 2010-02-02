<?php
if (!urlPath(1)) {
	textual();
	$sql = "SELECT * FROM article ORDER BY created DESC";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		while ($row = mysql_fetch_assoc($result)) {
			echo "<div class='article'><strong><a href='articles/{$row['url']}'>{$row['name']}</a></strong><small>Written: ".date("M d Y", strtotime($row['created']))."</small><p>".trimmer(htmlToText($row['content']),260)."</p></div>\n";
		}
	} else {
		abError("There are no articles yet!", ABWARN);
	}
} else {
	$url = urlPath(1);
	$sql = "SELECT * FROM article WHERE url = '$url' LIMIT 1";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_assoc($result);
		echo "<h1>{$row['name']}</h2>\n";
		echo "<div><em>Written ".date("M d Y", strtotime($row['created']))."</em><br /><a href='articles'>Back to Articles</a>";
		echo "</div><br />\n";
		echo $row['content'];
	} else {
		abError("The article <b>$url</b> cannot be found", ABERROR);
	}
}
	