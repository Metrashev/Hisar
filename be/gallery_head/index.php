<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("gallery");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_gallery_head";
	$search=getgallery_headControls('search');
}
$__del_var="hdDeletegallery_head";
$__editTable="gallery_head";
$fn_Delete="";
$fn_Delete="del_gallery_head";

function del_gallery_head($del_id) {
	$db=getdb();
	$c=(int)$db->getone("select count(*) from gallery where cid=1 and page_id=?",array($del_id));		
	if($c) {
		echo "<span class='error'>Има галерии сочещи към този запис!</span>";
		return;
	}
	$db->execute("delete from `gallery_head` where id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>