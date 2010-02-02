<?php
$nav['title'] = 'Pages';

$sql = "SELECT section, title FROM textual WHERE hidden = 0 ORDER BY title";
$result = runQuery($sql);
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$nav['children'][$i]['url'] = $row['section'];
	$nav['children'][$i]['title'] = $row['title'];
	$i++;
}
