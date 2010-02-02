<?php
textual();

$diry = './open/';
if (isset($_GET['location'])) {
	$directory = $_GET['location'];
	$directory = str_replace("..", "", $directory);
} else {
	$directory = $diry;
}
$dir = opendir($directory);
$dl = array();
while ($f = readdir($dir)) {
	if ($f[0] != '.')
		$dl[] = $f;
}
asort($dl);
echo "<small><strong>$directory</strong> (" . sizeof($dl) . " Items)</small>\n";
echo "<table cellpadding='0' cellspacing='0' id='file_browser'>";
if ($directory != $diry) {
	echo "<tr class='e'><td><a href=\"?location=$diry\">~/</a></td><td class='c'>&nbsp;</td></tr>\n";
	$parts = explode("/", $directory);
	$link = '';
	for($j = 0; $j < sizeof($parts) - 2; $j++)
		$link .= $parts[$j]."/";
	echo "<tr class='o'><td><a href=\"?location=$link\">../</a></td><td class='c'>&nbsp;</td></tr>\n";
}
$i = 1;
foreach ($dl as $f) {
	if ($i++ % 2) $data = 'e'; else $data = 'o';
	if (is_dir($directory.$f)) {
		echo "<tr class='$data'><td><a href=\"?location=$directory$f/\">$f/</a></td><td class='c'>&nbsp;</td></tr>\n";
	} else { # add a clause so if we link to a txt it views the content instead of downloading with a link to dl at the top
		echo "<tr class='$data'><td><a href=\"$directory$f\">$f</a></td><td class='c'>";
		echo humanReadableFilesize(filesize($directory.$f));
		echo "</td></tr>\n";
	}
}
echo "</table>\n";