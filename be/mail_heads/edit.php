<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_heads");
$in_edit_table="mq_mail_heads";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);

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
$con=getmail_headsControls();

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
//				$wd['data']['created_by']=Users::getUserId();
			}
			else {
				$old_date=$db->getone("select start_date from {$in_edit_table} where id='{$in_edit_id}'");
			}
//			$wd['data']['updated_date']=date('Y-m-d H:i:s');
//			$wd['data']['updated_by']=Users::getUserId();

			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
			if(USE_AUDIT_LOG) {
				CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
			}
			if($old_date) {
				$new_date=$db->getone("select start_date from {$in_edit_table} where id='{$n_id}'");
				if($old_date!=$new_date) {
					$t=(int)((strtotime($new_date)-strtotime($old_date))/60);
					if($t!=0) {
						$db->execute("update mq_mail_items set date_to_send=date_add(date_to_send,interval '{$t}' minute) where mail_head_id='{$n_id}' and status_id=0");
					}
				}
			}
				header("Location: ?id={$n_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
				//header("Location: ".($_GET['bkp']));
				exit;		
//			}
//			if(is_array($new_relation)) {
//				$errors=$new_relation;
//			}
//			else {
//				header("Location: ".($_GET['bkp']));
//				exit;
//			}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
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

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);

//echo $hhh= $p->render();

//if($in_edit_id) {	
//	include(dirname(__FILE__).'/../_relations/list.php');		
//	include(dirname(__FILE__).'/../_relations/index.php');
//}

if(USE_AUDIT_LOG&&$in_edit_id) {
	echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);
}

echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>