<style>
* {
	font-family: Calibri, Verdana;
	font-size: 12px;
	color: #333;
}
h1 {
	font-size: 14px;
	color: orange;
}
h1 span {
	
}
.action {
	margin-top: 10px;
	border: 1px solid orange;
	color: orange;
	background-color: #f0f0f0;
}
td {
	border-right: 1px dotted black;
	border-bottom: 1px dotted black;
}
table {
	border-left: 1px dotted black;
	border-top: 1px dotted black;
}
</style>
<?php
	$step = $_GET['step'] + 0;
	include("includes/config.ssi.php");
	include("includes/functions.ssi.php");
	if ($step == 0) {
?>
<h1>Step 1: <span>Confirm database info from config.ssi.php</span></h1>
<table cellspacing='0' cellpadding='2'>
<tr><td>Database username</td><td><?php echo DB_USER; ?>&nbsp;</td></tr>
<tr><td>Database password</td><td><?php echo DB_PASS; ?>&nbsp;</td></tr>
<tr><td>Database hostname</td><td><?php echo DB_HOST; ?>&nbsp;</td></tr>
<tr><td>Database</td><td><?php echo DB_NAME; ?>&nbsp;</td></tr>
</table>
<form method="get" action="install.php"><input type="submit" value="Step 2: Install CMS Database &raquo;" class="action" /><input type="hidden" name="step" value="2" /></form>
<?php
} else if ($step == 2) {
?>
<h1>Step 2: <span>Installing database using install.sql</span></h1>
<?php
	if (file_exists("install.sql")) {
		$handle = fopen("install.sql", "r");
		$i = 0;
		$errors = 0;
		while (!feof($handle)) {
			$i++;
			$buffer = fgets($handle, 4096);
			if (!runQuery($buffer)) {
				$errors++;
				rmError("Error with line $i!");
			}
		}
		fclose($handle);
if ($errors) {
	rmError("There were errors installing the database.");
} else {
		?>
<p>Database Installation was a success!</p>
<form method="get" action="install.php"><input type="submit" value="Step 3: Delete Installation Files &raquo;" class="action" /><input type="hidden" name="step" value="3" /></form>
<?php
}
	} else {
		rmError("install.sql not found");
	}
} else if ($step == 3) {
	echo "Installation files have been deleted.";
	unlink("install.php");
	unlink("install.sql");
} else {
	rmError("invalid step");
}