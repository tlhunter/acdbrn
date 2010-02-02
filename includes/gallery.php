<style type="css">
.gallery_thumb {
	width: <?php echo $nuke['integer']['gallery_thumb_width'] + 2 ?>px;
	height: <?php echo $nuke['integer']['gallery_thumb_height'] + 22 ?>px;
}
.gallery_thumb img {
	width: <?php echo $nuke['integer']['gallery_thumb_width'] ?>px;
	height: <?php echo $nuke['integer']['gallery_thumb_height'] ?>px;
}
</style>
<?php
if (urlPath(1)) {
	?>
	<link rel="stylesheet" href="scripts/lightbox/lightbox.css" type="text/css" media="screen" />
	<script src="scripts/lightbox/prototype.js" type="text/javascript"></script>
	<script src="scripts/lightbox/scriptaculous.js?load=effects" type="text/javascript"></script>
	<script src="scripts/lightbox/lightbox.js" type="text/javascript"></script>
	<div id="gallery_holder">
	<?php
	$cat = urlPath(1);
	$sql = "SELECT * FROM gallery_cat WHERE id = $cat";
	$result = runQuery($sql);
	$row = mysql_fetch_assoc($result);
	?>
	<h1><?php echo $row['name']?> Pictures</h1>
	<p><a href="gallery">Back to Photo Gallery's</a></p>
	<?php
	$sql = "SELECT * FROM gallery WHERE category = $cat";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		while ($row = mysql_fetch_assoc($result)) {
			echo "<div class=\"gallery_thumb\"><a href=\"pictures/gallery/{$row['id']}_full.jpg\" rel=\"lightbox[gallery]\" title=\"" . addslashes($row['name']) . "<div class='nobold'>{$row['description']}</div>\"><img src=\"pictures/gallery/{$row['id']}_thumb.jpg\" /><br />{$row['name']}</a></div>\n";
		}
	} else {
		abError("There are currently no pictures in this gallery.", ABWARN);
	}
	?>
	</div>
	<?php
} else {
	textual();
	?>
	<div id="gallery_holder">
	<?php
	$sql = "SELECT * FROM gallery_cat ORDER BY name";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		while ($row = mysql_fetch_assoc($result)) {
			echo "<div class=\"gallery_thumb\"><a href=\"gallery/{$row['id']}\"><img src=\"pictures/gallery/{$row['thumb']}_thumb.jpg\" /><br />{$row['name']}</a></div>\n";
		}
	} else {
		abError("There are currently no gallery's.", 1);
	}
	?>
	</div>
	<?php
}