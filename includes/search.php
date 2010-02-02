<?php
$search = $_REQUEST['q'];
$total_count = 0;
echo "<h1>Searching for '" . htmlentities($search) . "'</h1>";
$directory = './includes/search/';
$dir = opendir($directory);
$dl = array();
while ($f = readdir($dir)) {
	if ($f[0] != '.' && strpos($f, '.php')) {
		$dl[] = $f;
	}
}
asort($dl);

foreach($dl AS $dli) {
	include($directory.$dli);
	$total_count += $result_data['count'];
	echo "<h3>{$result_data['title']}</h3>";
	foreach($results AS $result) {
		echo "<div class='search_result'><div><a href='{$result['url']}'>{$result['title']}</a></div>\n";
		echo "<p>{$result['content']}</p>\n</div\n";
	}
}
?>