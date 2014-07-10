<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("gallery");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

class CGallery_Img {
	function getImgLink($params) {
		$img=$params[0];
		$row=$params[2]->DataSource->Rows[$params[1]];
		return <<<EOD
		<img src="{$GLOBALS['MANAGED_FILE_DIR_IMG']}gallery/{$row['id']}_img_1{$img}" height="50" />
EOD;

		
		if($row['cid']==1) {
			$s=getdb()->getone("select parameters from gallery_head where id=?",array($row['page_id']));
			@$s=unserialize($s);
			if(is_array($s)&&isset($s[1])) {
				return <<<EOD
		<a target="_blank" href="{$GLOBALS['MANAGED_FILE_DIR_IMG']}gallery/{$row['id']}_img_{$s[1]['size']}{$img}">view</a>
EOD;
			}
		}

	}
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
	$filter_session_name="";
}
else {
	$filter_session_name="filter_gallery";
	$search=getgalleryControls('search');
}
$__del_var="hdDeletegallery";
$__editTable="gallery";
$fn_Delete="";
$fn_Delete="del_gallery";

function del_gallery($del_id) {
	$db=getdb();
	require_once(dirname(__FILE__).'/controls.php');
	$con=getgalleryControls();
	ControlValues::deleteManagedImages($del_id,$con['controls'],false);	
	
	$cid=(int)$_GET['cid'];
	$page_id=(int)$_GET['page_id'];
	if($cid) {
		$co=new COrder("gallery","order_field","cid='$cid' and page_id='{$page_id}'");
		$co->delete_order($del_id);
	}
	
	$db->execute("delete from `gallery` where id='{$del_id}'");
	
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>