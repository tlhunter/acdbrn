<?php
mustBeAdmin();
buildAdminMenu(urlPath());
if (posting()) {
	foreach($_POST AS $name => $value) { runQuery("UPDATE site_integer SET `value` = '$value' WHERE `key` = '$name'"); }
	foreach($_POST AS $name => $value) { runQuery("UPDATE site_text    SET `value` = '$value' WHERE `key` = '$name'"); }
	foreach($_POST AS $name => $value) { runQuery("UPDATE site_varchar SET `value` = '$value' WHERE `key` = '$name'"); }
	$sql = "SELECT * FROM site_bool";
	$result = runQuery($sql);
	while ($row = mysql_fetch_assoc($result)) {
		if (isset($_POST[$row['key']])) {
			$value = 1;
		} else {
			$value = 0;
		}
		runQuery("UPDATE site_bool SET `value` = '$value' WHERE `key` = '{$row['key']}'");
	}
	redirect(urlPath(), 0, "Updating Site Settings...");
} else {
?>
<h1>Website Settings</h1>
<form id="website_settings" name="website_settings" method="post" action="">
  <table border="0" cellspacing="0" cellpadding="0" id="admin_site">
    <tr>
      <td>&nbsp;</td>
      <td>Website Title</td>
      <td><input type="text" name="title_prefix" id="title_prefix" class="text" value="<?php echo $nuke['varchar']['title_prefix']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Website Title Postfix<br /><small class='t'>Appears at the of the title</small></td>
      <td><input type="text" name="title_postfix" id="title_postfix" class="text" value="<?php echo $nuke['varchar']['title_postfix']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Meta Keywords<br /><small class='t'>Search Engine Keywords<br />Comma separated</small></td>
      <td><textarea name="meta_key" id="meta_key" class="text"><?php echo $nuke['text']['meta_key']?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Footer/Copyright Text<br /><small class='t'>HTML acceptable</small></td>
      <td><textarea name="footer_text" id="footer_text" class="text"><?php echo $nuke['text']['footer_text']?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Sidebar Content<br /><small class='t'>HTML acceptable</small></td>
      <td><textarea name="sidebar_content" id="sidebar_content" class="text"><?php echo $nuke['text']['sidebar_content']?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Admin Email</td>
      <td><input type="text" name="admin_email" id="admin_email" class="text" value="<?php echo $nuke['varchar']['admin_email']?>" /></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="use_map" <?php if ($nuke['bool']['use_map']) echo "checked"; ?> /></td>
      <td>Map Embed URL<br /><small class='t'>Don't forget http://</small></td>
      <td><input type="text" name="map_embed_code" id="map_embed_code" class="text" value="<?php echo $nuke['text']['map_embed_code']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Map Dimensions</td>
      <td>width: <input type="text" name="map_width" id="map_width" class="text shorter" value="<?php echo $nuke['integer']['map_width']?>" /> height: <input type="text" name="map_height" id="map_height" class="text shorter" value="<?php echo $nuke['integer']['map_height']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Photo Dimensions</td>
      <td>width: <input type="text" name="gallery_photo_width" id="gallery_photo_width" class="text shorter" value="<?php echo $nuke['integer']['gallery_photo_width']?>" /> height: <input type="text" name="gallery_photo_height" id="gallery_photo_height" class="text shorter" value="<?php echo $nuke['integer']['gallery_photo_height']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Thumb Dimensions</td>
      <td>width: <input type="text" name="gallery_thumb_width" id="gallery_thumb_width" class="text shorter" value="<?php echo $nuke['integer']['gallery_thumb_width']?>" /> height: <input type="text" name="gallery_thumb_height" id="gallery_thumb_height" class="text shorter" value="<?php echo $nuke['integer']['gallery_thumb_height']?>" /></td>
    </tr>
	<tr>
      <td><input type="checkbox" name="use_captcha" <?php if ($nuke['bool']['use_captcha']) echo "checked"; ?> /></td>
      <td>Contact Captcha</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" value="Save Website Settings" /></td>
    </tr>
  </table>
</form>
<?php
}
?>