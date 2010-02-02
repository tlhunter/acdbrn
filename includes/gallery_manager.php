<?php
mustBeAdmin();
buildAdminMenu(urlPath());
?>
<h1>Photo Gallery Manager</h1>
<?php
if (urlPath(1, 'remove_gallery')) {
	$remove = urlPath(2);
	$sql = "SELECT id FROM gallery WHERE category = '$remove'";
	$result = runQuery($sql);
	while ($row = mysql_fetch_assoc($result)) {
		unlink("pictures/gallery/{$row['id']}_thumb.jpg");
		unlink("pictures/gallery/{$row['id']}_full.jpg");
	}
	runQuery("DELETE FROM gallery WHERE category = '$remove'");
	runQuery("DELETE FROM gallery_cat WHERE id = '$remove' LIMIT 1");
	rmError("Gallery has been deleted.", RMSUCCESS);
} else if (urlPath(1, 'remove_picture')) {
	$remove = urlPath(2);
	runQuery("DELETE FROM gallery WHERE id = '$remove' LIMIT 1");
	unlink("pictures/gallery/{$remove}_thumb.jpg");
	unlink("pictures/gallery/{$remove}_full.jpg");
	rmError("Photo has been deleted.", RMSUCCESS);
} else if (urlPath(1, 'new_gallery')) {
	if (!posting()) {
		?>
<form method="post">
<table>
<tr><td colspan='2'><h3>Creating a new gallery</h3></td></tr>
<tr><td>Gallery Name</td><td><input name="name" /></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Create Gallery" /></td></tr>
</table>
</form>
<?php
	} else {
		$name = nukeNumWords($_POST['name']);
		runQuery("INSERT INTO gallery_cat SET name = '$name', thumb = 0");
		rmError("Gallery $name has been created", RMSUCCESS);
	}
} else if (urlPath(1, 'new_picture')) {
	if (!posting()) {
		$result = runQuery("SELECT COUNT(*) AS count FROM gallery_cat");
		$row = mysql_fetch_assoc($result);
		if ($row['count'] > 0) {
		?>
<form method="post" enctype="multipart/form-data">
<table>
<tr><td colspan='2'><h3>Creating a new image</h3></td></tr>
<tr><td>Picture Name</td><td><input name="name" /></td></tr>
<tr><td>Gallery</td><td><?php dropdownGeneric('gallery_cat'); ?></td></tr>
<tr><td>Picture File <span class="small">(<1.5MB)</span></td><td><input name="uploadedfile" type="file" style="font-family: arial; font-size: 11px;" /></td></tr>
<tr><td>Picture Description</td><td><textarea name="description"></textarea></td></tr>
<tr><td>Make Thumbnail?</td><td><input type="checkbox" name="thumbnail" /> (Makes this picture the new<br />thumbnail for the selected gallery)</td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Create Picture" /></td></tr>
</table>
</form>
<?php
		} else {
			rmError("You must create a photo gallery before you can create a photo!", RMWARN);
		}
	} else {
		$name = htmlentities($_POST['name']);
		$description = htmlentities($_POST['description']);
		$category = nukeNum($_POST['gallery_cat']);
		runQuery("INSERT INTO gallery SET name = '$name', description = '$description', category = '$category'");
		$id = mysql_insert_id();
		if (posting('thumbnail'))
			runQuery("UPDATE gallery_cat SET thumb = '$id' WHERE id = '$category' LIMIT 1");
		rmError("Picture has been uploaded", RMSUCCESS);
		if ($_FILES['uploadedfile']['size']) {
			$destination = "pictures/gallery/{$id}_thumb.jpg";
			createThumb($_FILES['uploadedfile']['tmp_name'], $destination, $nuke['integer']['gallery_thumb_width'], $nuke['integer']['gallery_thumb_height'], true, 85, true);
			$destination = "pictures/gallery/{$id}_full.jpg";
			createThumb($_FILES['uploadedfile']['tmp_name'], $destination, $nuke['integer']['gallery_photo_width'], $nuke['integer']['gallery_photo_height'], false, 85, true);
		}
	}
} else if (urlPath(1, 'edit_picture')) {
	if (!posting()) {
		$id = urlPath(2);
		$sql = "SELECT * FROM gallery WHERE id = '$id' LIMIT 1";
		$result = runQuery($sql);
		$row = mysql_fetch_assoc($result);
		$sql = "SELECT thumb FROM gallery_cat WHERE id = '{$row['category']}' LIMIT 1";
		$result = runQuery($sql);
		$row2 = mysql_fetch_assoc($result);
		?>
<form method="post" enctype="multipart/form-data">
<table">
<tr><td colspan='2'><h3>Editing an existing image</h3></td></tr>
<tr><td>Picture Name</td><td><input name="name" value="<?php echo $row['name']?>" /></td></tr>
<tr><td>Gallery</td><td><?php dropdownGeneric('gallery_cat', $row['category']); ?></td></tr>
<tr><td>Picture File <span class="small">(<1.5MB)</span></td><td><input name="uploadedfile" type="file" style="font-family: arial; font-size: 11px;" /></td></tr>
<tr><td>Picture Description</td><td><textarea name="description"><?php echo $row['description']?></textarea></td></tr>
<tr><td>Make Thumbnail?</td><td><input type="checkbox" name="thumbnail" <?php if ($row2['thumb'] == $row['id']) { ?>checked="checked"<?php } ?> /> (Makes this picture the new<br />thumbnail for the selected gallery)</td></tr>
<tr><td><input name="id" value="<?php echo $row['id']?>" type="hidden" /></td><td><input type="submit" value="Save Changes" /></td></tr>
</table>
</form>
<?php
	} else {
		$id = $_POST['id'] + 0;
		$name = htmlentities($_POST['name']);
		$description = htmlentities($_POST['description']);
		$category = nukeNum($_POST['gallery_cat']);
		runQuery("UPDATE gallery SET name = '$name', description = '$description', category = '$category' WHERE id = '$id'");
		#$id = mysql_insert_id();
		if ($_POST['thumbnail'] == 'on')
			runQuery("UPDATE gallery_cat SET thumb = '$id' WHERE id = '$category' LIMIT 1");
		rmError("Picture has been uploaded", RMSUCCESS);
		if ($_FILES['uploadedfile']['size']) {
			$destination = "pictures/gallery/{$id}_thumb.jpg";
			createThumb($_FILES['uploadedfile']['tmp_name'], $destination, $nuke['integer']['gallery_thumb_width'], $nuke['integer']['gallery_thumb_height'], true, 85, true);
			$destination = "pictures/gallery/{$id}_full.jpg";
			createThumb($_FILES['uploadedfile']['tmp_name'], $destination, $nuke['integer']['gallery_photo_width'], $nuke['integer']['gallery_photo_height'], false, 85, true);
		}
	}
} if (urlPath(1, 'edit_gallery')) {
	$id = urlPath(2);
	$sql = "SELECT * FROM gallery_cat WHERE id = '$id' LIMIT 1";
	$result = runQuery($sql);
	$row = mysql_fetch_assoc($result);
	if (!posting()) {
		?>
<form method="post">
<table>
<tr><td colspan='2'><h3>Modifying a gallery</h3></td></tr>
<tr><td>Gallery Name</td><td><input name="name" value="<?php echo $row['name']?>" /></td></tr>
<tr><td>Thumb</td><td><?php dropdown_gallery_picker($row['id'], $row['thumb']) ?></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Save Changes" /></td></tr>
</table>
<input name="id" type="hidden" value="<?php echo $row['id']?>" />
</form>
<?php
	} else {
		$name = htmlentities($_POST['name']);
		$thumb = $_POST['thumb'] + 0;
		$id = $_POST['id'] + 0;
		runQuery("UPDATE gallery_cat SET name = '$name', thumb = '$thumb' WHERE id = '$id'");
		rmError("Gallery $name has been modified", RMSUCCESS);
	}
}
$iw = ceil($nuke['integer']['gallery_thumb_width'] / 2);
$ih = ceil($nuke['integer']['gallery_thumb_height'] / 2);
?>
<link rel="stylesheet" href="scripts/lightbox/lightbox.css" type="text/css" media="screen" />
<script src="scripts/lightbox/prototype.js" type="text/javascript"></script>
<script src="scripts/lightbox/scriptaculous.js?load=effects" type="text/javascript"></script>
<script src="scripts/lightbox/lightbox.js" type="text/javascript"></script>
<div class="page_controls">[ <a href="gallery_manager/new_gallery"><?php echo icon('gallery_add')?> New Gallery</a> |
<a href="gallery_manager/new_picture"><?php echo icon('photo_add')?> New Picture</a> ]
</div>
<table width='100%'><tr><td valign='top' width="50%">
<h2>Gallery's</h2>
<?php
	$sql = "SELECT * FROM gallery_cat ORDER BY id";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		echo "<table cellspacing='1'>\n";
		echo "<tr><th colspan='3'>&nbsp;</th><th>Icon</th></tr>\n";
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($i++ % 2) $data = 'e'; else $data = 'o';
			echo "<tr class=\"$data\">";
			echo "<td class='admin_table_action'><a href=\"gallery_manager/edit_gallery/{$row['id']}\">" . icon('gallery_edit') . "</a></td>";
			echo "<td class='admin_table_action'><a href=\"javascript:cd('gallery_manager/remove_gallery/{$row['id']}')\">" . icon('gallery_delete') . "</a></td>";
			echo "<td class='admin_table_action'><a href=\"gallery/{$row['id']}\">" . icon('gallery_view') . "</a></td>";
			echo "<td><img src=\"pictures/gallery/{$row['thumb']}_thumb.jpg\" width='$iw' height='$ih' /></td><td valign='middle'>" . trimmer($row['name'], 40) . "</td></tr>\n";
		}
		echo "</table>";
	} else {
		rmError("No Gallery Categories Defined", RMWARN);
	}
?>

</td><td valign='top' width="50%">
<h2>Photo's</h2>
<?php
	$sql = "SELECT g.*, gc.name AS gallery_name FROM gallery AS g, gallery_cat AS gc WHERE gc.id = g.category ORDER BY gallery_name, id";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		echo "<table cellspacing='1'>\n";
		echo "<tr><th colspan='3'>&nbsp;</th><th>Icon</th><th>Name</th><th>Gallery</th></tr>\n";
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($i++ % 2) $data = 'e'; else $data = 'o';
			echo "<tr class=\"$data\"><td class='admin_table_action'><a href=\"gallery_manager/edit_picture/{$row['id']}\">" . icon('photo_edit') . "</a></td><td class='admin_table_action'>";
			echo "<a href=\"javascript:cd('gallery_manager/remove_picture/{$row['id']}')\">" . icon('photo_delete') . "</a>";
			echo "</td><td class='admin_table_action'><a href=\"pictures/gallery/{$row['id']}_full.jpg\" rel=\"lightbox[gallery{$row['category']}]\" title=\"" . addslashes($row['name']) . "</em><div class='nobold'>{$row['description']}</div>\">" . icon('photo_view') . "</a></a></td>";
			echo "<td><img src=\"pictures/gallery/{$row['id']}_thumb.jpg\" width='$iw' height='$ih' /></td><td>" . trimmer($row['name'], 40) . "</td><td>{$row['gallery_name']}</td></tr>\n";
		}
		echo "</table>";
	} else {
		rmError("No Pictures Defined", RMWARN);
	}
?>
</td></tr></table>