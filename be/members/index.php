<?php
ob_start();
require_once('../libCommon.php');
//Users::checkUserRights('Users');
CUserRights::checkRights("members");
if($__mvc) {
	require_once(dirname(__FILE__)."/../common/datagrid_controler.php");
	require_once(dirname(__FILE__)."/../common/search_controler.php");
	require_once(dirname(__FILE__)."/../common/mvc.php");
	
	$filter_session_name="filter_members";
}
else  {
	if(isset($_POST['ajax_params'])) {
		require_once(dirname(__FILE__).'/../common/ajax_params.php');
	}
	
	include(dirname(__FILE__).'/table_desc.php');
	
	include(dirname(__FILE__).'/controls.php');
	
	if(isset($in_edit_id)&&(int)$in_edit_id) {
		$search=array();
	}
	else {
		$filter_session_name="filter_members";
		$search=getmembersControls('search');
	}
}


$__del_var="hdDeletemembers";
$__editTable="members";
$fn_Delete="";/*
$fn_Delete="del_members";

function del_members($del_id) {
	$db=getdb();
		
	$db->execute("delete from `members` where id='{$del_id}'");
}*/

if($__mvc) {
	$mvc=new MVC($__editTable,$filter_session_name,"",$__del_var);
	$mvc->workpath=dirname(__FILE__);
	
	$mvc->is_multiform=isset($__render_grid_only);
	
	$mvc->has_data_grid=true;
	
	$mvc->autoprepare();
	
	$mvc->fn_Delete=$fn_Delete;
	
	$mvc->processDelete($fn_Delete);
	$mvc->processSelect();
	
	echo $mvc->render(isset($__skip_back_button));
}
else {
	include(dirname(__FILE__).'/../common/index.php');
}

ob_end_flush();
?>