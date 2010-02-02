<?php
session_start();

$include_dir = '../../../includes';
require_once("$include_dir/config.ssi.php");
require_once("$include_dir/functions.ssi.php");

mustBeAdmin();

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Expires: Sun, 01 Jul 2010 00:00:00 GMT');
header('Pragma: no-cache');

$directory = $include_dir . '/linker/';
$dir = opendir($directory);
$dl = array();
while ($f = readdir($dir)) {
	if ($f[0] != '.' && strpos($f, '.php')) {
		$dl[] = $f;
	}
}
asort($dl);

$supernav = array();

$j = 0;
foreach($dl AS $dli) {
	include($directory.$dli);
	$supernav[$j] = $nav;
	unset($nav);
	$j++;
}

echo json_encode($supernav);