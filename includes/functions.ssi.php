<?php
/**
 *
 * @global <array> $nuke
 * @param <string> $query
 * @return <mixed>
 */
function runQuery($query) {
	global $nuke;
	$connect = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	if (!$connect) {
		loadCmsCss();
		die("<div class=\"error\">" . mysql_error() . "</div>");
	}
	mysql_select_db(DB_NAME, $connect);
	$result = mysql_query($query, $connect);
	if (CMS_DEBUG) {
		$handle = fopen("sql.log","a");
		$r = $result ? "SUCCESS" : "FAILURE";
		fwrite($handle, date("Y-m-d H:i:s") . " " . $_SERVER['REMOTE_ADDR'] . " " . $r . " " . $query . "\n");
		fclose($handle);
	}
	return $result;
}

/**
 *
 * @param <int> $segment URL segment you are looking at, first segment is 0 (default)
 * @param <string> $compare Compare segment to specified value, returns TRUE if equal
 * @return <type> Returns the specified URL segment, or a T/F if matches second parameter
 */
function urlPath($segment=0, $compare='') {
	$segment = (int) $segment;
	if (!empty($compare)) {
		if (isset($_GET[ABGETVAR][$segment]) && $_GET[ABGETVAR][$segment] == $compare)
			return TRUE;
		else
			return FALSE;
	} else {
		if ($segment == 0 && empty($_GET[ABGETVAR][$segment])) {
			return ABINDEX;
		} else if (isset($_GET[ABGETVAR][$segment])) {
			return nukeAlphaNum($_GET[ABGETVAR][$segment]);
		} else {
			return false;
		}
	}
}

/**
 *
 * @param <string> $index Optional, see if $_POST[$index] is defined
 * @return <type> Returns TRUE if !empty($_POST), or if specified input is set
 */
function posting($index = FALSE) {
	if ($index) {
		if (isset($_POST[$index]))
			return TRUE;
	} else {
		if (!empty($_POST) || !empty($_FILES))
			return TRUE;
	}
	return FALSE;
}

function nukeAlphaNum	($value) { return preg_replace(	"[^a-zA-Z0-9_-]", "", $value); }
function nukeAlpha		($value) { return preg_replace(	"[^a-zA-Z]"		, "", $value); }
function nukeWords		($value) { return preg_replace(	"[^a-zA-Z ]"	, "", $value); }
function nukeNumWords	($value) { return preg_replace(	"[^a-zA-Z0-9_ ]", "", $value); }
function nukeNum		($value) { return preg_replace(	"[^0-9]"		, "", $value); }
function nukeFloat		($value) { return preg_replace(	"[^0-9.]"		, "", $value); }

/**
 *
 * @param <string> $value Email Address
 * @return <type> Returns TRUE if a valid email, FALSE if otherwise
 */
function nukeValidEmail($value) {
	if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $value)) {
		return true;
	} else {
		return false;
	}
}

function nukeUrlSafe($value) {
	$value = strtolower($value);
	$value = trim($value);
	$value = str_replace(" ", "_", $value);
	return preg_replace("[^a-z0-9_]", "", $value);
}

function abError($message, $level = 0) {
	if ($level == ABERROR) {
		$type = "error";
	} else if ($level == ABWARN) {
		$type = "warn";
	} else if ($level == ABSUCCESS) {
		$type = "success";
	}
	echo "<div class=\"$type\">$message</div>\n";
}

function nukeValidWebsite($value) {
	if (eregi("^(http|ftp|https)://[-A-Za-z0-9._/]+", $value))
		return true;
	else if (empty($value) || $value == "http://")
		return true;
	else
		return false;
}

function loadCmsCss() {
	echo '<link rel="stylesheet" type="text/css" href="cms.css" />';
}

function isAdmin() {
	if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function mustBeAdmin() {
	if (!isset($_SESSION['isadmin']) || !$_SESSION['isadmin']) {
		redirect('login', 0, "You must be an administrator.");
		exit();
	}
}

function trimmer($string, $length=999) {
	if (strlen($string) > $length)
		return htmlspecialchars(trim(substr($string, 0, $length))) . '&#0133;';
	else
		return htmlspecialchars($string);
}

function dropdownYesNo($selected, $name) { ?>
<select name="<?php echo $name?>">
<option<?php if ($selected) echo ' selected'; ?> value="1">Yes</option>
<option<?php if (!$selected) echo ' selected'; ?> value="0">No</option>
</select>
<?php
}

function dropdownGeneric($item, $selected = 1, $name = null, $sort = 1, $css_class='', $css_id='') {
	if ($sort == 1)
		$sort = " ORDER BY name ASC";
	else if ($sort == 0)
		$sort = "";
	else if ($sort == -1)
		$sort = " ORDER BY name DESC";
	
	if (!isset($name))
		$name = $item;
		
    $sql = "SELECT name, id FROM $item";
    $result = runQuery($sql);
    echo "<select name=\"$name\" class=\"dropdown $css_class\" id=\"$css_id\">\n";
    while ($row = mysql_fetch_assoc($result)) {
        echo "<option ";
        if ($selected == $row['id'])
            echo "selected ";
        echo "value=\"" . $row['id'] . "\">" . ucfirst($row['name']) . "</option>\n";
    }
    echo "</select>\n";
}

function checkboxGeneric($item, $mod = '', $selected = 0) {
    echo "<div class=\"radio_box$mod\">\n";
	$sql = "SELECT name, id FROM $item ORDER BY name ASC";
    $result = runQuery($sql);
    $checked = false;
    while ($row = mysql_fetch_assoc($result)) {
        echo "<input type=\"radio\" name=\"$item$mod\" value=\"" . $row['id'] . "\"";
		if ($selected == $row['id']) {
			echo " checked";
			$checked = true;
		}
		echo " /> " . $row['name'] . "<br />\n";
    }
	echo "<input type=\"radio\" name=\"$item$mod\" value=\"0\"";
	if (!$checked)
		echo " checked";
	echo " /> None\n";
    echo "</div>\n";
}

function textual($section = false, $show_date = false) {
	if ($section) {
		$section = nukeAlphaNum($section);
	} else {
		$section = urlPath();
	}
	$sql = "SELECT * FROM textual WHERE section = '$section' LIMIT 1";
	$result = runQuery($sql);
	$row = mysql_fetch_assoc($result);

	echo "<div id=\"textual\">\n";
	if (isAdmin()) {
		echo "<div class=\"page_controls\">[ <a href=\"page_edit/$section\">".icon('edit')." Edit Page</a>";
		if ($row['locked'] == 0) {
			echo " | <a href=\"javascript:cd('page_remove/$section')\">".icon('delete')." Remove Page</a>";
		}
		echo " | <a href=\"admin\">".icon('admin_panel')." Admin Panel</a> | <a href=\"logout\">".icon('user_logout')." Logout</a> ]</div>\n";
	}
	
	echo $row['text'];
	if ($show_date)
		echo "<div class=\"timestamp\">Last updated on " . date("F j, Y g:ia", strtotime($row['date'])) . ".</div>\n";
	echo "</div>\n";
}


function debug() {
	global $nuke;
	echo "<div class=\"debug\">";
	echo "<div class='debug_head'>post</div><table cellpadding='0' cellspacing='0'>";
	$i = 0;
	foreach($_POST AS $key => $value) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class='$data'><td class='debug_key'>{$key}</td><td>{$value}</td></tr>\n";
	}
	echo "</table>\n";

	$i = 0;
	echo "<div class='debug_head'>get</div><table cellpadding='0' cellspacing='0'>";
	foreach($_GET AS $key => $value) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class='$data'><td class='debug_key'>{$key}</td><td>{$value}</td></tr>\n";
	}
	echo "</table>\n";

	$i = 0;
	echo "<div class='debug_head'>session</div><table cellpadding='0' cellspacing='0'>";
	foreach($_SESSION AS $key => $value) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class='$data'><td class='debug_key'>{$key}</td><td>{$value}</td></tr>\n";
	}
	echo "</table>\n";

	$i = 0;
	echo "<div class='debug_head'>nuke</div><table cellpadding='0' cellspacing='0'>";
	foreach($nuke AS $key => $value) {
		if ($i++ % 2) $data = 'e'; else $data = 'o';
		echo "<tr class='$data'><td class='debug_key'>{$key}</td><td>{$value}</td></tr>\n";
	}
	echo "</table>\n";
	
	echo "</div>";
}

function wysiwyg() {
	global $nuke;

?>
<script type="text/javascript">
var _editor_url  = "<?php echo CMS_BASEHREF?>wysiwyg/";
var _editor_lang = "en";
var _editor_skin = "blue-look";
var _editor_icons = "Tango";
</script>
<script type="text/javascript" src="wysiwyg/XinhaCore.js"></script>
<script type="text/javascript">
var xinha_plugins =
[
	'ImageManager', 'Linker'
];
var xinha_editors =
[
	'wysiwyg_textarea'
];
function xinha_init() {
	if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;
	var xinha_config = new Xinha.Config();
	xinha_config.width=<?php echo $nuke['integer']['content_width']?>;
	xinha_config.height=400;
	xinha_config.baseHref="<?php echo CMS_BASEHREF?>";
	xinha_config.fullScreen=false;
	xinha_config.killWordOnPaste=true;
	xinha_config.stripBaseHref=true;
	xinha_config.toolbar =
	[
		["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],
		["separator","forecolor","hilitecolor","textindicator"],
		["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
		["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
		["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
		(Xinha.is_gecko ? [] : ["cut","copy","paste","overwrite","saveas"]),
		["separator","killword","clearfonts","removeformat","toggleborders"],
		["separator","htmlmode","showhelp"]
	];

	xinha_editors = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);

	Xinha.startEditors(xinha_editors);

}
Xinha.addOnloadHandler(xinha_init);
</script>
<?php
}

function createThumb($name,$filename,$new_w,$new_h, $force_dimen=false, $quality = 85, $frame = false) {
	# Resizes the image while keeping the proper aspect ratio. Returns a JPG. Not neccecarily used for thumbnail creation.
	if (!file_exists($name)) {
		abError("File does not exist for thumbnail creation");
		return false;
	}
	$system=explode('.',$name);
	if (preg_match('/jpg|jpeg/',$system[1])){
		$src_img=imagecreatefromjpeg($name);
	} else if (preg_match('/png/',$system[1])){
		$src_img=imagecreatefrompng($name);
	} else if (preg_match('/gif/', $system[1])) {
		$src_img = imagecreatefromgif($name);
	} else { # Works if we are uploading and using .tmp files.
		$src_img=imagecreatefromjpeg($name);
	}

	$size[0]=imageSX($src_img);
	$size[1]=imageSY($src_img);
	
	if (!$force_dimen) { # Creates the image using the desired width and height as a max for new width and height.
		if ($new_w >= $size[0] && $new_h >= $size[1]){
			$scale = 1;
		} else {
			$ratio = $size[0] / $size[1];
			if ($ratio >= 1){
				$scale = $new_w / $size[0];
			} else {
				$scale = $new_h / $size[1];
			}
		}
		$new_w = $size[0] * $scale;
		$new_h = $size[1] * $scale;
		$im_out = imagecreatetruecolor($new_w, $new_h);
		imagecopyresampled($im_out, $src_img, 0, 0, 0, 0, $size[0] * $scale, $size[1] * $scale, $size[0], $size[1]);
	} else { # Creates the image and crops at the end to force the thumbnail size.
		$srcHeight = $size[1];
		$srcWidth = $size[0];
		$dstHeight = $new_h;
		$dstWidth = $new_w;
		if ($srcHeight < $srcWidth) {
            $ratio = (double)($srcHeight / $dstHeight);

            $cpyWidth = round($dstWidth * $ratio);
            if ($cpyWidth > $srcWidth) {
                $ratio = (double)($srcWidth / $dstWidth);
                $cpyWidth = $srcWidth;
                $cpyHeight = round($dstHeight * $ratio);
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            } else {
                $cpyHeight = $srcHeight;
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
            }
        } else {
            $ratio = (double)($srcWidth / $dstWidth);
            $cpyHeight = round($dstHeight * $ratio);
            if ($cpyHeight > $srcHeight) {
                $ratio = (double)($srcHeight / $dstHeight);
                $cpyHeight = $srcHeight;
                $cpyWidth = round($dstWidth * $ratio);
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            } else {
                $cpyWidth = $srcWidth;
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            }
        }
		$im_out = imagecreatetruecolor($new_w, $new_h);
		imagecopyresampled($im_out, $src_img, 0, 0, $xOffset, $yOffset, $dstWidth, $dstHeight, $cpyWidth, $cpyHeight);
	}

	if ($frame) {
		$white = imagecolorallocate($im_out, 255, 255, 255);
		imageline($im_out, 0, 0, $new_w, 0, $white); # T
		imageline($im_out, $new_w - 1, 0, $new_w - 1, $new_h, $white); # R
		imageline($im_out, $new_w, $new_h - 1, 0, $new_h - 1, $white); # B
		imageline($im_out, 0, 0, 0, $new_h, $white); # L
	}
	
	imagejpeg($im_out, $filename, $quality); 

	imagedestroy($src_img); 
	imagedestroy($im_out); 
}

function icon($name) {
	$name = nukeAlphaNum($name);
	$xname = ucwords(str_replace("_", " ", $name));
	return "<img src=\"cms_images/icon_$name.png\" alt=\"$xname\" title=\"$xname\">";
}

function redirect($where, $time=0, $message='continue') {
	if (!headers_sent()) {
		header("Location: " . CMS_BASEHREF . "$where");
		exit();
	} else {
		echo "<div><a href=\"$where\">$message</a></div>\n<meta http-equiv=\"refresh\" content=\"$time;url=" . CMS_BASEHREF . "$where\">\n";
	}
}

function htmlToText($string) {
	$pattern=array("'<[\/\!]*?[^<>]*?>'si");
	$replace=array("");
	$string = preg_replace ($pattern, $replace, $string);
	$pattern = array("&amp;nbsp;", "&amp;quot;", "&amp;amp;", "&nbsp;", "&amp;");
	$replace = array(" ", "\"", "&", " ", "&");
	return (str_replace($pattern, $replace, $string));
}

function searchHighlight($string, $term) {
	$pattern=array("'$term'si");
	$replace=array("<span class=\"highlight\">$term</span>");
	return preg_replace ($pattern, $replace, $string);
}

function humanReadableFilesize($size) {
    // Adapted from: http://www.php.net/manual/en/function.filesize.php
    $mod = 1024;
    $units = explode(' ','B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }
    return round($size, 2) . ' ' . $units[$i];
}

function buildNavigation($section, $listmode = false, $limit = 10) {
	$sql = "SELECT title, section FROM textual WHERE nav_order > 0 AND hidden = 0 ORDER BY nav_order LIMIT $limit";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		$nav = '';
		$i = 1;
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['section'] == $section) { $extra = "active"; } else { $extra = ''; }
			if ($i == 1) { $extra .= " first"; }
			if ($listmode) {
				$nav .= "<li><a href='{$row['section']}' id='nav$i' class='$extra'><span>{$row['title']}</span></a></li>\n";
			} else {
				$nav .= "<a href='{$row['section']}' id='nav$i' class='$extra'><span>{$row['title']}</span></a>\n";
			}
			$i++;
		}
		return $nav;
	} else {
		return "<a href='admin'>NO NAVIGATION DEFINED!</a>";
	}
}

function renderNucleoFields($nucleo_fields) {
	global $nuke;
	
	$sql = "SELECT text FROM textual WHERE section = '".urlPath()."_sidebar' LIMIT 1";
	$result = runQuery($sql);
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_assoc($result);
		$sidebar_content = '';
		if (isAdmin()) {
			$sidebar_content = "<div class='page_controls'>[ <a href='page_edit/".urlPath()."_sidebar'>".icon('sidebar')." Edit Sidebar</a> ]</div>\n";
		}
		$sidebar_content .= $row['text'];
	} else {
		$sidebar_content = $nuke['text']['sidebar_content'];
		if (isAdmin()) {
			$sidebar_content .= "<div class='page_controls'>[ <a href='page_add/".urlPath()."_sidebar'>".icon('sidebar')." Add Sidebar</a> ]</div>";
		}
	}
	$admin_head = '';
	if (isAdmin()) {
		$admin_head .= "<script type=\"text/javascript\" src=\"scripts/jquery.js\"></script>\n";
		$admin_head .= "<script type=\"text/javascript\" src=\"scripts/facebox/facebox.js\"></script>\n";
		$admin_head .= "<script type=\"text/javascript\" src=\"scripts/cd.js\"></script>\n";
	}
	$admin_head .= "<link href=\"scripts/facebox/facebox.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	$admin_head .= "<link href=\"cms.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	$admin_head .= "<link href=\"sitemap.xml\" rel=\"sitemap\" type=\"text/xml\" />\n";

	$nav_text = buildNavigation(urlPath(), false, $nuke['integer']['max_nav']);
	$nav_text_list = buildNavigation(urlPath(), true, $nuke['integer']['max_nav']);
	$parser_items = array(
		"<acdbrn field='pretitle' />",
		"<acdbrn field='title' />",
		"<acdbrn field='posttitle' />",
		"<acdbrn field='metakeywords' />",
		"<acdbrn field='metadescription' />",
		"<acdbrn field='nav' />",
		"<acdbrn field='nav' type='ul' />",
		"<acdbrn field='footer' />",
		"<acdbrn field='section' />",
		"<acdbrn field='basehref' />",
		"<acdbrn field='head' />",
		"<acdbrn field='sidebar' />"
	); # add: different date formats, users online, total visitors

	foreach($nuke['integer'] AS $key => $value) { $parser_items[] = "<acdbrn field='integer' item='$key' />"; }
	foreach($nuke['varchar'] AS $key => $value) { $parser_items[] = "<acdbrn field='varchar' item='$key' />"; }
	foreach($nuke['text']    AS $key => $value) { $parser_items[] = "<acdbrn field='text' item='$key' />";    }
	foreach($nuke['bool']    AS $key => $value) { $parser_items[] = "<acdbrn field='bool' item='$key' />";    }

	$parser_replace = array(
		$nuke['varchar']['title_prefix'],
		$nuke['page_title'],
		$nuke['varchar']['title_postfix'],
		$nuke['text']['meta_key'],
		$nuke['meta_desc'],
		$nav_text,
		$nav_text_list,
		$nuke['text']['footer_text'],
		urlPath(),
		CMS_BASEHREF,
		$admin_head,
		$sidebar_content
	);
	foreach($nuke['integer'] AS $key => $value)	{ $parser_replace[] = $value; }
	foreach($nuke['varchar'] AS $key => $value)	{ $parser_replace[] = $value; }
	foreach($nuke['text'] AS $key => $value)	{ $parser_replace[] = $value; }
	foreach($nuke['bool'] AS $key => $value)	{ $parser_replace[] = $value; }

	$content = str_replace($parser_items, $parser_replace, $nucleo_fields);
	return $content;
}

function buildAdminMenu($viewing) {
	echo "<div class='admin_menu'>";
	$sql = "SELECT * FROM admin_panel ORDER BY place ASC";
	$result = runQuery($sql);
	while ($row = mysql_fetch_assoc($result)) {
		echo "<a class='";
		if ($row['url'] == $viewing)
			echo "active ";
		echo "admin_button' href='{$row['url']}'><div><img src='{$row['icon_url']}'><br />{$row['name']}</div></a>\n";
	}
	echo "<a class='admin_button admin_button_red' href='logout'><div><img src='cms_images/icon_user_logout.png'><br />Logout</div></a>";
	echo "<div style='clear: both;'></div>\n";
	echo "</div>";
	
}

function dateDb($date_format, $date_data) {
	return date($date_format, strtotime($date_data));
}

function pingGoogleSitemap() {
	$handle = @fopen("http://www.google.com/ping?sitemap=" . CMS_BASEHREF . "sitemap.xml", "rb");
	if ($handle) {
		$contents = stream_get_contents($handle);
		fclose($handle);
	}
}