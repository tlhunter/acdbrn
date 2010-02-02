<?php
$nav['title'] = 'Gallery';

$sql = "SELECT id, name FROM gallery_cat ORDER BY name";
$result = runQuery($sql);
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$nav['children'][$i]['url'] = 'gallery/' . $row['id'];
	$nav['children'][$i]['title'] = $row['name'];
	$i++;
}
