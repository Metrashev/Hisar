<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("users");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_users";
	$search=getusersControls('search');
}
$__del_var="hdDeleteusers";
$__editTable="users";
$fn_Delete="";/*
$fn_Delete="del_users";

function del_users($del_id) {
	$db=getdb();
		
	$db->execute("delete from `users` where id='{$del_id}'");
}*/
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>