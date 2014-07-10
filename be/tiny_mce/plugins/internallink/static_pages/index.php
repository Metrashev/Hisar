<?php
ob_start();
$dir=dirname(__FILE__).'/../../../../';
require_once($dir.'libCommon.php');
//Users::checkUserRights('Users');

$filter_session_name="tmc_static_pages";
$_SESSION[$filter_session_name."_new_order"]="";
$__custom_where="";
include(dirname(__FILE__).'/table_desc.php');

//include(dirname(__FILE__).'/controls.php');

if(!isset($force_search)) {
	$search=array();
}
else {
	include(dirname(__FILE__).'/controls.php');
	
	$search=getControls('search');
}

$__editTable="static_pages";

/*
$fn_Delete="del_static_pages";

function del_static_pages($del_id) {
	$db=getdb();
	$db->execute("delete from `static_pages` where id='{$del_id}'");
}
*/
echo "<form method=post><select name='type' onchange='getForm(this).submit();'><option value='1' selected>Static pages</option><option value='2'>News</option></select></form>";
include($dir.'/common/index.php');

ob_end_flush();
?>