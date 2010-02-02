<?php

if (urlPath(1)) {
	$sql = "SELECT * FROM blog WHERE id = " . urlPath(1) . " LIMIT 1";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_assoc($result);
		echo "<div class='blog_entry'>";
		echo "<div class='blog_date'><div class='month'>".date("M",strtotime($row['created']))."</div><div class='day'>".date("j",strtotime($row['created']))."</div></div>";
		echo "<div class='blog_title'><h3>{$row['name']}</h3>\n<div><small class='t'>Posted on ".date("m/j/Y h:iA",strtotime($row['created']))."</small> | <a href='blog'>Back to Blogs</a></div></div>\n";
		echo "<div class='content'>{$row['content']}</div\n";
		echo "</div>";
	} else {
		rmError("Invalid Blog Entry", RMERROR);
	}
} else {
	textual(urlPath());
	$sql = "SELECT id, name, created, summary FROM blog ORDER BY created DESC";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		while ($row = mysql_fetch_assoc($result)) {
			echo "<div class='blog_entry'>";
			echo "<div class='blog_date'><div class='month'>".date("M",strtotime($row['created']))."</div><div class='day'>".date("j",strtotime($row['created']))."</div></div>";
			echo "<div class='blog_title'><h3>{$row['name']}</h3>\n<div><small class='t'>Posted on ".date("m/j/Y h:iA",strtotime($row['created']))."</small></div></div>\n";
			echo "<div class='content'>{$row['summary']} <a href='blog/{$row['id']}'>Read More...</a></div\n";
			echo "</div>";
		}
	} else {
		rmError("No blogs yet!", RMWARN);
	}
}