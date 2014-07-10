<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_heads");

$in_edit_table="mq_mail_items";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);
$db=getdb();
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
$con=getmail_itemsControls();

$errors=array();

$errors=array();

if($in_edit_id&&isset($_POST['btSend'])) {
	if(empty($_POST['in_data']['subject'])) {
		$errors[]="<b>Subject</b> е задължително поле";
	}
	if(!CValidation::is_valid_email_address($_POST['in_data']['from_email'])) {
		$errors[]="Въведете валиден e-mail  в полето <b>From email</b>";
	}
	if(!CValidation::is_valid_email_address($_POST['in_data']['to_email'])) {
		$errors[]="Въведете валиден e-mail  в полето <b>From email</b>";
	}
	
	if(empty($errors)) {
		$mq=new CMQ();
		$r=$mq->sendSingleItemTest($in_edit_id,$_POST['test_email']);
		if($r===false) {
			$errors[]="Съобщението <b>не</b> е изпратено";
		}
		else {
			$errors[]="Съобщението е изпратено успешно";
		}
	}
}

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
/*
			if(!$in_edit_id) {
				$wd['data']['created_date']=date('Y-m-d H:i:s');
				$wd['data']['created_by']=Users::getUserId();
			}
			$wd['data']['updated_date']=date('Y-m-d H:i:s');
			$wd['data']['updated_by']=Users::getUserId();
*/
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
			if(USE_AUDIT_LOG) {
				CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
			}
//			$new_relation=CRelations::processRelationSave($_GET,$n_id);
//			if($new_relation===false) {
/*
				if((string)$order!="") {
	$co=new COrder($in_edit_table,"order_field");
	$co->set_item_order($n_id,$order);
}

*/

//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
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


$GLOBALS['mail_body']=$db->getone("select mail_body from {$in_edit_table} where id='{$in_edit_id}'");
@$GLOBALS['mail_body']=unserialize($GLOBALS['mail_body']);
if(is_array($GLOBALS['mail_body'])) {
	$GLOBALS['mail_body']['message']=htmlspecialchars($GLOBALS['mail_body']['message']);
}
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
$ss=print_r($GLOBALS['mail_body'],true);
echo "</td></tr>
<tr>
<td align='left'>
<pre>{$ss}</pre>
</td>
</tr>	
</table>";


echo "</form></body></html>";

ob_end_flush();
?>