<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_groups");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_mq_mail_groups";
	$search=getmq_mail_groupsControls('search');
}
$__del_var="hdDeletemq_mail_groups";
$__editTable="mq_mail_groups";
$fn_Delete="";/*
$fn_Delete="del_mq_mail_groups";

function del_mq_mail_groups($del_id) {
	$db=getdb();
		
	$db->execute("delete from `mq_mail_groups` where id='{$del_id}'");
}*/
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>