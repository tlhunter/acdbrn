<h1>Sitemap</h1>
<dl>
<?php
$sql = "SELECT * FROM textual WHERE hidden = 0 ORDER BY title";
$result = runQuery($sql);
while ($row = mysql_fetch_assoc($result)) {
	$trim_text = trimmer(htmlToText($row['text']),300);
?>
	<dt><a href="<?php echo $row['section']?>"><?php echo $row['title']?></a></dt>
	<dd style="margin-bottom: 10px;"><small><?php echo $trim_text?></small></dd>
<?php
}
?>
</dl>