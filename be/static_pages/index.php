<?php
ob_start();
require_once('../libCommon.php');
//Users::checkUserRights('Users');

$filter_session_name="static_pages";

include(dirname(__FILE__).'/table_desc.php');

//include(dirname(__FILE__).'/controls.php');


$search=array();
$__del_var="hdDelete";
$__editTable="static_pages";

/*
$fn_Delete="del_static_pages";

function del_static_pages($del_id) {
	$db=getdb();
	$db->execute("delete from `static_pages` where id='{$del_id}'");
}
*/
$__template_index=new IndexTemplate(0);
$__template_index->clear();
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>