<?php
$nav['title'] = 'Articles';

$sql = "SELECT name, url FROM article ORDER BY created DESC";
$result = runQuery($sql);
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$nav['children'][$i]['url'] = 'articles/' . $row['url'];
	$nav['children'][$i]['title'] = $row['name'];
	$i++;
}
