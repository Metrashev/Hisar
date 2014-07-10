<?php
ob_start();
require_once('../libCommon.php');


$in_edit_table="news_pages";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);

if(!(int)$_GET['cid']&&!(int)$_GET['id']) {
	die("Invalid cid");
}

if(isset($_POST['pickGallery'])) {
	unset($_POST['pickGallery']);
	$_SESSION[$in_edit_table."_post"]=$_POST;
	header("Location: ".BE_DIR."gallery_head/?select=1&return_point=gallery_head&bkp=".urlencode($_SERVER['REQUEST_URI']));
	exit;
}

$db=getdb();
if(!$_GET['cid']) {
	$_GET['cid']=$cid=$db->getone("select cid from news_pages where id='{$_GET['id']}'");
}

CUserRights::checkRights("news",(int)$_GET['cid']);

if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
	}	
}
else {
	$array=$_POST;
}

if($_SERVER['REQUEST_METHOD']=='GET') {
	if(isset($_GET['return_point'])&&isset($_SESSION[$in_edit_table."_post"])) {
		
		switch ($_GET['return_point']) {
			case "gallery_head": {
				$gh=(int)$_GET['result'];
				if($gh&&$in_edit_id) {
					$db->Execute("update {$in_edit_table} set gallery_head_id=? where id=?",array($gh,$in_edit_id));
				}
				break;
			}
		}
		header("Location: ?cid={$cid}&id={$in_edit_id}&load_ses=1&bkp=".urlencode($_GET['bkp']));
		exit;
	}
	else {
		if(isset($_GET['load_ses'])) {
			if(isset($_SESSION[$in_edit_table."_post"])&&is_array($_SESSION[$in_edit_table."_post"])) {
				$array=$_POST=$_SESSION[$in_edit_table."_post"];
			}
			unset($_SESSION[$in_edit_table."_post"]);
		}		
	}	
	
}

if(isset($_POST['delGalleryHead'])&&$in_edit_id) {
	$db->Execute("update {$in_edit_table} set gallery_head_id=0 where id=?",array($in_edit_id));
}

$parameters=$db->getone("select php_data from categories where id=?",array($_GET['cid']));
$parameters=unserialize($parameters);
$GLOBALS['parameters']=$parameters['parameters'];
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>

<?=BE_Utils::loadTinyMce("body","np.tpl");?>
</head>
<body >
<?php
$t=new CURLTree("categories");
echo<<<EOD
<h3 align="center">{$t->get_node_path($_GET['cid'])}</h3>
EOD;
?>
<form id='f1' method=POST enctype="multipart/form-data">
<table class="main_table" align="center">
<tr>
<td>

<?php

include(dirname(__FILE__).'/controls.php');
$con=getControls();

$errors=array();

$errors=array();
if(isset($_POST['btSave'])) {

	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	if (empty($wd['errors'])) {
			if(!((int)$_GET['id'])) {
				$wd['data']['cid']=(int)$_GET['cid'];
			}
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
			if(USE_AUDIT_LOG) {
				CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
			}
			$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
//			$new_relation=CRelations::processRelationSave($_GET,$n_id);
//			if($new_relation===false) {
	if(empty($errors)) {
				//header("Location: ".($_GET['bkp']));
				header("Location: ?id={$n_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
				//header("Location: ".($_GET['bkp']));				
				exit;		
}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



$GLOBALS['gallery_name']="";
if($in_edit_id) {
	$gallery_head_id=(int)$db->getone("select gallery_head_id from {$in_edit_table} where id=?",array($in_edit_id));
	if($gallery_head_id) {
		$GLOBALS['gallery_name']=$db->getone("select name from gallery_head where id=?",array($gallery_head_id));
	}
}
FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);



if(USE_AUDIT_LOG&&$in_edit_id) {
	echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);
}

echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>