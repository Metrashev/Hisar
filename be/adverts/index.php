<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("adverts");
//Users::checkUserRights('Users');

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_adverts";
	$search=getadvertsControls('search');
}
//$__del_var="hdDeleteadverts";
//$__editTable="adverts";
//$fn_Delete="";
$fn_Delete="del_adverts";

function del_adverts($del_id) {
	$db=getdb();
		require_once(dirname(__FILE__).'/controls.php');
	$con=getadvertsControls();
	ControlValues::deleteManagedImages($del_id,$con['controls'],false);	
	ControlValues::deleteManagedFiles($del_id,$con['controls'],false);	
	$db->execute("delete from `adverts` where id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>