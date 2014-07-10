<?php
ob_start();
$dir=dirname(__FILE__).'/../../../../';
require_once($dir.'libCommon.php');
//Users::checkUserRights('Users');

$filter_session_name="tmc_news_pages";
$_SESSION[$filter_session_name."_new_order"]=array();
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


$search=getControls('search');

$__editTable="news_pages";


echo "<form method=post><select name='type' onchange='getForm(this).submit();'><option value='1'>Static pages</option><option value='2'  selected>News</option></select></form>";
include($dir.'/common/index.php');

ob_end_flush();
?>