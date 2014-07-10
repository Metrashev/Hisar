<?php
require_once 'file_manager.php';
require_once 'conf.php';

$fm = new CFManInterface_Images($GLOBALS['FMAN_IMAGES_ABS_PATH'], $_REQUEST['dir'], $_REQUEST);
$fm->file_manager->file_permissions = new CFileManPermission_Images($GLOBALS['FMAN_IMAGES_ABS_PATH']);
$fm->file_manager->URL_root_dir = $GLOBALS['FMAN_IMAGES_URL_PATH'];

?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<title>Image Manager</title>
	<link rel="stylesheet" href="styles.css">
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src='js/lib.js'></script>
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-cookie.js"></script>
	<script type="text/javascript" src="js/jquery.ae.image.resize.min.js"></script>
	<script type="text/javascript">
		//<!--
		var i = 1;
		history[history.length] = '';
		history[history.length-1] = '';

		var base_virtual_disk_URL = '<?=$GLOBALS['FMAN_IMAGES_URL_PATH']?>';

		jQuery(window).load(function(){
			jQuery('#up_simple_on').hide();
			jQuery('#up_advanced_on').hide();
			jQuery('div.thumb_floater').show();
			thumbs.updateFromCookie();
		});

		// -->
	</script>
</head>

<body>
<form id="frm_2" name="f2" method="post" enctype="multipart/form-data">
<input name="delete" type="hidden" id="delete" value="">
<input name="dir" type="hidden" value="<?=htmlspecialchars($_REQUEST['dir'])?>">

	<div style="margin: 10px; height: 34px;">
		<a href="#" onclick="thumbs.changeSize('small');  return false;" class="icon_size_small" >&nbsp;</a>
		<a href="#" onclick="thumbs.changeSize('medium'); return false;" class="icon_size_medium">&nbsp;</a>
		<a href="#" onclick="thumbs.changeSize('large');  return false;" class="icon_size_large" >&nbsp;</a>
	</div>

	<?=$fm->render()?>
	<div style="clear:both;"> </div>

	<div class="upload_panel_container">
		<div id="up_advanced_on">
			<a href="#" class="upload_panel_switch_off" onclick="jQuery('#up_advanced_on').hide(); jQuery('#up_advanced_off').show(); return false;">Advanced Image Upload</a>
			<?php require(dirname(__FILE__)."/flash/index.php"); ?>
		</div>

		<div id="up_advanced_off">
			<a href="#" class="upload_panel_switch_on" onclick="jQuery('#up_advanced_off').hide(); jQuery('#up_advanced_on').show(); jQuery(document.documentElement).attr({ scrollTop: jQuery(document.documentElement).attr('scrollHeight') }); return false;">Advanced Image Upload</a>
		</div>
	</div>

	<div class="upload_panel_container">
		<div id="up_simple_on">
			<a href="#" class="upload_panel_switch_off" onclick="jQuery('#up_simple_on').hide(); jQuery('#up_simple_off').show(); return false;">Simple Image Upload</a>
			<div style="padding: 10px;">
				<input type="file" name="userfile1" size="40">
				<input type="submit" value="Upload" name="upload">
			</div>
		</div>

		<div id="up_simple_off">
			<a href="#" class="upload_panel_switch_on" onclick="jQuery('#up_simple_off').hide(); jQuery('#up_simple_on').show(); jQuery(document.documentElement).attr({ scrollTop: jQuery(document.documentElement).attr('scrollHeight') }); return false;">Simple Image Upload</a>
		</div>
	</div>
