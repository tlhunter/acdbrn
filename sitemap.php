<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.90">
<?php
define('INCLUDE_DIR', './includes/');

require_once(INCLUDE_DIR . "constants.ssi.php");
require_once(INCLUDE_DIR . "config.ssi.php");
require_once(INCLUDE_DIR . "functions.ssi.php");

$sql = "SELECT * FROM textual WHERE hidden = 0";
$result = runQuery($sql);
while ($row = mysql_fetch_assoc($result)) {
?>
	<url>
		<loc><?php echo CMS_BASEHREF . $row['section']; ?></loc>
		<lastmod><?php echo date("Y-m-d\TH:i:s\+00:00", strtotime($row['date'])); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>0.5</priority>
	</url>
<?php
}
?>
</urlset>