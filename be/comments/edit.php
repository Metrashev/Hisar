<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("comments");
$in_edit_table="comments";
$in_edit_id=(int)$_GET['id'];

/*$article_id=(int)$_GET['article_id'];
if(!$article_id) {
	FE_Utils::_die("Invalid relation ID");		
}*/
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>
</head>
<body >
<form id='f1' method=POST>
<table class="main_table" align="center">
<tr>
<td>

<?php

include(dirname(__FILE__).'/controls.php');
$con=getcommentsControls();

$errors=array();

$errors=array();
if(isset($_POST['btSave'])) {

	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	if (empty($wd['errors'])) {
/*
		$order=$wd['data']['order_field'];
		unset($wd['data']['order_field']);
		if(!$in_edit_id) {
			$order=(int)$order;
		}
*/


		if(!$in_edit_id) {
			$wd['data']['created_date']=date('Y-m-d H:i:s');
//			$wd['data']['created_by']=Users::getUserId();
		}
		$wd['data']['updated_date']=date('Y-m-d H:i:s');
//		$wd['data']['updated_by']=Users::getUserId();
			
//		$wd['data']['article_id']=$article_id;
			
			
		//$wd['data']['cid']=1;
		$n_id=ControlWriter::Write($in_edit_table,$wd['data'],$in_edit_id);
		//if(USE_AUDIT_LOG) {	CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);	}
	
	/*if((string)$order!="") {
		$co=new COrder($in_edit_table,"order_field",);
		$co->set_item_order($n_id,$order);
	}*/
	
		//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
		//$errors+=ControlValues::deleteManagedFiles($n_id,$_FILES,$con['controls']);
		if(empty($errors)) {
			header("Location: ?id={$n_id}&article_id={$article_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
			//header("Location: ".($_GET['bkp']));
			exit;
		}
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



if ($_SERVER['REQUEST_METHOD']=='GET') {
	if ($in_edit_id) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$in_edit_id}'");
	}	
}
else {
	$array=$_POST;
}

FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo Master::create($con,$con['template']['dir'],$array);
//if(USE_AUDIT_LOG&&$in_edit_id) {echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);}

echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>