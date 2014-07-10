<?php
ob_start();
require_once('../libCommon.php');
//Users::checkUserRights('Users');
CUserRights::checkRights("pools");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_polls";
	$search=getpollsControls('search');
}
$__del_var="hdDeletepolls";
$__editTable="polls";
$fn_Delete="";
$fn_Delete="del_polls";

function del_polls($del_id) {
	$db=getdb();
		
	$db->execute("delete from `polls` where id='{$del_id}'");
	$db->execute("delete from `polls_options` where poll_id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>