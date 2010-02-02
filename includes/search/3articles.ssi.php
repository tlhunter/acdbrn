<?php
$sql = "SELECT name AS title, content AS description, url FROM article WHERE ((name LIKE '%$search%') OR (content LIKE '%$search%'))";
$result = runQuery($sql);
$results = array();
$result_data = array();
$result_data['count'] = mysql_num_rows($result);
$result_data['title'] = "Found {$result_data['count']} matching articles";
$i = 0;
if ($result_data['count'] && !empty($search)) {
	while ($row = mysql_fetch_assoc($result)) {
		$results[$i]['title'] = searchHighlight($row['title'], $search);
		$results[$i]['url'] = 'articles/' . $row['url'];
		$results[$i]['content'] = searchHighlight(trimmer(htmlToText($row['description']), 400), $search);
		$i++;
	}
}