<?php
# AcdBrn Alpha
# Developed by RenownedMedia.com, LLC
# CMS Copyright 2005 Thomas Hunter
# Released under the MIT License
# http://code.google.com/p/acdbrn/
session_start();

define('INCLUDE_DIR', './includes/');

require_once(INCLUDE_DIR . "constants.ssi.php");
require_once(INCLUDE_DIR . "config.ssi.php");
require_once(INCLUDE_DIR . "functions.ssi.php");

if (urlPath() == 'logout') {
	unset($_SESSION['isadmin']);
	redirect(ABINDEX);
}

$inclusion = INCLUDE_DIR . urlPath() . '.php';
$inclusion_abcustom = INCLUDE_DIR . urlPath() . '.abcustom';
$query = "SELECT title, meta_desc FROM textual WHERE section = '" . urlPath() . "' LIMIT 1";

# http://renownedmedia.com/blog/renownedstats-web-analyzer-page-counter-statistic/
if (file_exists("statistics/renownedstats.php")) {
	include_once('statistics/renownedstats.php');
	add_hit(urlPath());
}

if (file_exists($inclusion)) {
	$nuke['section_type'] = ABSTFILE;
	if ($row = mysql_fetch_assoc(runQuery($query))) {
		$nuke['page_title'] = ucwords(str_replace("_"," ",$row['title']));
		$nuke['meta_desc'] = $row['meta_desc'];
	} else {
		$nuke['page_title'] = ucwords(str_replace("_"," ",urlPath()));
		$nuke['meta_desc'] = '';
	}
} else if (file_exists($inclusion_abcustom)) {
	$nuke['section_type'] = ABSTCUSTOM;
	if ($row = mysql_fetch_assoc(runQuery($query))) {
		$nuke['page_title'] = ucwords(str_replace("_"," ",$row['title']));
		$nuke['meta_desc'] = $row['meta_desc'];
	} else {
		$nuke['page_title'] = ucwords(str_replace("_"," ",urlPath()));
		$nuke['meta_desc'] = '';
	}
} else if ($row = mysql_fetch_assoc(runQuery($query))) {
	$nuke['section_type'] = ABSTTEXT;
	$nuke['page_title'] = ucwords($row['title']);
	$nuke['meta_desc'] = $row['meta_desc'];
} else {
	$nuke['section_type'] = ABSTERROR;
	$nuke['page_title'] = '404 Error';
	$nuke['meta_desc'] = '';
}

$content_types = array('integer', 'varchar', 'text', 'bool');
foreach($content_types AS $type) {
	$sql = "SELECT * FROM site_$type";
	$result = runQuery($sql);
	while ($row = mysql_fetch_assoc($result)) {
		$nuke[$type][$row['key']] = $row['value'];
	}
}
unset($content_types);

if (file_exists("template.ssi.php")) {
	include_once("template.ssi.php");
}

if ((urlPath() == ABINDEX) && (file_exists("template_splash.htm"))) {
	$template_file = "template_splash.htm";
} else {
	$template_file = "template.htm";
}

if (file_exists($template_file)) {
	$tfh = fopen($template_file, 'r');
	$template_content = fread($tfh, filesize($template_file));
} else {
	loadCmsCss();
	abError("The template file '$template_file' does not exist!", ABERROR);
	exit();
}

$template_content = explode("<acdbrn field='content' />",$template_content);

$template_content = renderAcdBrnFields($template_content);

if (sizeof($template_content) != 2) {
	loadCmsCss();
	abError("Template file does not contain exactly one content tag!", ABERROR);
	exit();
}

if ($nuke['section_type'] == ABSTERROR) {
	header("HTTP/1.0 404 Not Found");
}

echo $template_content[0];

if ($nuke['section_type'] == ABSTERROR) {
	abError("The page <b>/" . urlPath() . "</b> does not exist!", ABERROR);
	if (isAdmin()) {
		echo "<div class='page_controls'>[ <a href='page_add/" . urlPath() . "'>" . icon('add') . " Create Page</span></a> ]</div>\n";
	}
} else if ($nuke['section_type'] == ABSTFILE) {
	include_once($inclusion);
} else if ($nuke['section_type'] == ABSTCUSTOM) {
	$nhandle = fopen($inclusion_abcustom, 'r');
	$contents = fread($nhandle, filesize($inclusion_abcustom));
	echo renderAcdBrnFields($contents);
} else if ($nuke['section_type'] == ABSTTEXT) {
	textual();
}
if (CMS_DEBUG) {
	debug();
}
echo $template_content[1];