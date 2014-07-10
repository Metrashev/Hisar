<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_heads");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_mail_heads";
	$search=getmail_headsControls('search');
}
$__del_var="hdDeletemail_heads";
$__editTable="mq_mail_heads";
$fn_Delete="";
$fn_Delete="del_mail_heads";

function del_mail_heads($del_id) {
	$mq=new CMQ();
	$mq->deleteEmail($del_id);
//	$db=getdb();
		
//	$db->execute("delete from `mail_heads` where id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>