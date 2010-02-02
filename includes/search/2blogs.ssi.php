<?php
$sql = "SELECT id, name AS title, summary FROM blog WHERE ((name LIKE '%$search%') OR (summary LIKE '%$search%') OR (content LIKE '%$search%'))";
$result = runQuery($sql);
$results = array();
$result_data = array();
$result_data['count'] = mysql_num_rows($result);
$result_data['title'] = "Found {$result_data['count']} matching blogs";
$i = 0;
if ($result_data['count'] && !empty($search)) {
	while ($row = mysql_fetch_assoc($result)) {
		$results[$i]['title'] = searchHighlight($row['title'], $search);
		$results[$i]['url'] = 'blog/' . $row['id'];
		$results[$i]['content'] = searchHighlight($row['summary'], $search);
		$i++;
	}
}