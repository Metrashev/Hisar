<?php
require_once 'file_manager.php';
require_once 'conf.php';

$fm = new CFManInterface_Images($GLOBALS['FMAN_IMAGES_ABS_PATH'], $_COOKIE['imagemanager-settings-lastdir'], $_REQUEST);
$fm->file_manager->file_permissions = new CFileManPermission_Images($GLOBALS['FMAN_IMAGES_ABS_PATH']);
$fm->file_manager->URL_root_dir = $GLOBALS['FMAN_IMAGES_URL_PATH'];

$fm->processCommands();

$dirs = $fm->getAllDirs();

$tree = <<<EOD
d = new dTree('d');
d.config.useCookies = false;
d.add(0, -1, 'Images', '/');

EOD;

$selected = 0;

foreach ($dirs as $k=>$v) {
	$i = $k + 1;
	$p = $v['pid'] + 1;
	$v['pr'] .= '/';

	$tree .= <<<EOD
d.add({$i},{$p},'{$v['fn']}','{$v['pr']}',null,null,'images/tree/folder.gif','images/tree/folderopen.gif');

EOD;

	if ($fm->file_manager->currentDir == $v['pr']) {
		$selected = $i;
	}
}

if ($selected==0) {
	$open = 'd.s(0);';
} else {
	$open = "d.openTo({$selected}, true);";
}

$tree .= <<<EOD
document.write(d);
{$open}

try {
	if (d.selectedNode>0) {
		GoToFolder(d.aNodes[d.selectedNode].url);
	} else {
		GoToFolder('/');
	}
} catch (ex) {}
EOD;

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<title>Image Manager</title>
	<script type="text/javascript" src='js/dtree.js'></script>
	<script type="text/javascript" src='js/lib.js'></script>
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-cookie.js"></script>
	<link rel="stylesheet" href="js/dtree.css">
	<link rel="stylesheet" href="styles.css">
	<script type="text/javascript">
		//<!--
		history[history.length] = '';
		history[history.length-1] = '';
		var base_virtual_disk_URL = '<?=$GLOBALS['FMAN_IMAGES_URL_PATH']?>';
		// -->
	</script>
</head>

<body style="background-color: #EEEEEE;" onresize="jQuery.cookie('imagemanager-settings-treewidth', jQuery(this).width(), 1)">

<form id="frm_2" name="f2" method="post" enctype="multipart/form-data">
<input name="dir" type="hidden" value="<?=htmlspecialchars($_REQUEST['dir'])?>">

<script type="text/javascript">
<?php
echo $tree;
?>
</script>

<hr size="1" noshade="noshade">
New folder:<br>
<input type="text" name="newfoldername" value="" size="20">
<input type="hidden" name="command" value="1">
<input type="submit" value="Create" name="commandbtn">

</form>

</body>
</html>