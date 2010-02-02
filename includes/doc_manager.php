<?php
mustBeAdmin();
buildAdminMenu(urlPath());
if (urlPath(1, 'remove')) {
	$id = urlPath(2);
	$sql = "SELECT * FROM docs WHERE id = $id LIMIT 1";
	$result = runQuery($sql);
	$row = mysql_fetch_assoc($result);
	$filename = RMDIRDOCS . $row['filename'];
	if (!file_exists($filename) || !unlink($filename)) {
		rmError("Could not remove document from filesystem!", RMERROR);
	}
	$sql = "DELETE FROM docs WHERE id = $id LIMIT 1";
	if (runQuery($sql)) {
		rmError("The Document <b>{$row['filename']}</b> has been deleted.", RMSUCCESS);
	} else {
		rmError("Could not remove document entry from database", RMERROR);
	}
} else if (urlPath(1, 'add')) {
	if (!posting()) {
?>
<form method="post" enctype="multipart/form-data" action="doc_manager/add">
<table cellpadding="2" cellspacing="1" class="dotted">
<tr><th colspan="2"><center>Upload a new Document</center></th></tr>
<tr><th>File</th><td><input name="uploadedfile" type="file" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Upload Document" /></td></tr>
</table>
</form>
<?php
	} else {
		$raw_name = $_FILES['uploadedfile']['name'];
		$extension = strrchr($_FILES['uploadedfile']['name'],'.');
		$filename = nukeUrlSafe(substr($raw_name, 0, strlen($raw_name)-strlen($extension)+1));
		
		$sqlfile = $filename . $extension;
		$totfile = RMDIRDOCS . $sqlfile;
		if (file_exists($totfile)) {
			$filename .= "-" . date("YmdHis");
			$sqlfile_old = $sqlfile;
			$sqlfile = $filename . $extension;
			$totfile = RMDIRDOCS . $sqlfile;
			rmError("A file already exists named $sqlfile_old, so we named the new file $sqlfile.", RMWARN);
			unset($sqlfile_old);
		}
		if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $totfile)) {
			$sql = "INSERT INTO docs SET filename = '$sqlfile'";
			if (runQuery($sql)) {
				rmError("File was uploaded", RMSUCCESS);
			}
		} else {
			rmError("Error moving uploaded file!", RMERROR);
		}
	}
}


	$count = mysql_fetch_assoc(runQuery("SELECT COUNT(id) AS count FROM docs"));
	echo "<h1>Document Manager <small>(".$count['count']." Files)</small></h1>\n";
	?>
<div class="page_controls">[ <a href="doc_manager/add"><?php echo icon('doc_add')?> Upload a Document</a> ]</div>

<?php
	$sql = "SELECT * FROM docs ORDER BY added DESC";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		echo "<table id=\"view\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr class=\"e\"><th colspan=\"2\" width=\"48\" align='center'>&nbsp;</th><th>Document</th><th>Filesize</th><th>Uploaded</th></tr>\n";
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($i++ % 2) $data = 'e'; else $data = 'o';
			echo "<tr class=\"$data\"><td class='admin_table_action'>";
			echo "<a href=\"javascript:cd('doc_manager/remove/" . $row['id'] . "')\">" . icon('doc_delete') . "</a>";
			if (file_exists(RMDIRDOCS.$row['filename'])) {
				$filesize = humanReadableFilesize(filesize(RMDIRDOCS.$row['filename']));
			} else {
				$filesize = "<span class='red'>FILE_NOT_EXIST</span>";
			}
			echo "</td><td class='admin_table_action'><a href=\"".RMDIRDOCS."{$row['filename']}\">" . icon('doc_view') . "</a></td><td>{$row['filename']}</td><td>$filesize</td><td>" . date("n/j/Y", strtotime($row['added'])) . "</td></tr>\n";
		}
		echo "</table>";
	} else {
		rmError("You have not uploaded any documents yet!", RMWARN);
	}