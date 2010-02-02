<?php
$nav['title'] = 'Blogs';

$sql = "SELECT id, name FROM blog ORDER BY created DESC";
$result = runQuery($sql);
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$nav['children'][$i]['url'] = 'blog/' . $row['id'];
	$nav['children'][$i]['title'] = $row['name'];
	$i++;
}
