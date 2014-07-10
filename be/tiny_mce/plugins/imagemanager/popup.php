<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
	<head>
		<title>Image manager</title>
		<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
		<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-cookie.js"></script>
		<script type="text/javascript">
			jQuery(function(){
				var width = parseInt(jQuery.cookie('imagemanager-settings-treewidth'));
				if (isNaN(width) || width<200) width = 200;
				jQuery('#frset')[0].cols = width + ',*';
			});
		</script>
	</head>
	<frameset cols="200,*" id="frset">
		<frame frameborder="2" bordercolor="gray" name="fr_tree" id="fr_tree" src="tree.php">
		<frame name="fr_folder">
	</frameset>
</html>