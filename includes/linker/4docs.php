<?php
$nav['title'] = 'Documents';

$sql = "SELECT filename FROM docs ORDER BY filename";
$result = runQuery($sql);
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$nav['children'][$i]['url'] = 'docs/' . $row['filename'];
	$nav['children'][$i]['title'] = $row['filename'];
	$i++;
}
