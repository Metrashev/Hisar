<?php
ob_start();
require_once('../libCommon.php');
//Users::checkUserRights('Users');
CUserRights::checkRights("news");
$filter_session_name="news_pages";

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');


$search=getControls('search');

$__editTable="news_pages";


$fn_Delete="del_news_pages";

function del_news_pages($del_id) {
	$db=getdb();
	require_once(dirname(__FILE__).'/controls.php');
	$con=getControls();
	ControlValues::deleteManagedImages($del_id,$con['controls'],false);
	$db->execute("delete from `news_pages` where id='{$del_id}'");
}

include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>