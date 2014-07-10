<?php
session_start();
require_once(dirname(__FILE__)."/../../lib/fe/FormProcess.php");
require_once(dirname(__FILE__)."/../../lib/be/fe_utils.php");
require_once(dirname(__FILE__)."/../../lib/be/ControlsExNew.php");
//require_once(dirname(__FILE__)."/../../lib/mail.php");

$db=getdb();

$user_id=CMembers::getMemberId();

echo $db->getOne("select body from static_pages where cid=? and def=1",array($_GET['cid'])); 

$fp=new FormProcessor();
ob_start();
include(dirname(__FILE__).'/Register.tpl');
$str=ob_get_clean();
$fp->loadTemplate($str);

$fp->setIdAttributes();
$fp->markRequiredFields();
$fp->fillData($_POST);
$errors=array();
$errors1=array();
if(isset($_POST['btSave'])) {
	$errors=$fp->validate();
	if(empty($errors)) {
		if($_POST['in_data']['userpass']!=$_POST['userpass1']) {
			$errors['userpass1']="Невярно потвърдена парола";
		}
		else {
			
			$has_user_id=(int)$db->getOne("select id from members where username=? and id!='{$user_id}'",array($_POST['in_data']['username']));
			if($has_user_id) {
				$errors['duplicate']="В базата съществува посочененото потребителско име";
			}
			//}
		}
		$_POST['in_data']['home_phone']=trim($_POST['in_data']['phone']['home']);
		$_POST['in_data']['mobile_phone']=trim($_POST['in_data']['phone']['mobile']);
		$_POST['in_data']['work_phone']=trim($_POST['in_data']['phone']['work']);
		unset($_POST['in_data']['phone']);
		/*
		if(empty($_POST['in_data']['work_phone'])&&empty($_POST['in_data']['mobile_phone'])&&empty($_POST['in_data']['home_phone'])) {
			$errors1[]="Посочете поне 1 телефон за контакт";
		}
		*/
	}
	if(empty($errors)) {
		
		$user_id=ControlWriter::Write("members",$_POST['in_data'],$user_id);
		//$db->Execute("update codes set user_id=? where id=?",array($user_id,$code_id));
		CMembers::loadMemberData($user_id);
		header("Location: /?cid={$_GET['cid']}&ok=1");
		exit;
	}
	
}

if(!empty($errors)) {
	$errors="<div class='error'>".implode("</div><div class='error'>",$errors)."</div>";
}
else {
	$errors="";
}


echo str_replace("_#EXTRA#_",$errors,$fp->getHTML());

?>