<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_heads");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_mail_items";
	$search=getmail_itemsControls('search');
}
$__del_var="hdDeletemail_items";
$__editTable="mq_mail_items";
$fn_Delete="";
$fn_Delete="del_mail_items";

function del_mail_items($del_id) {
	$mq=new CMQ();
	$mq->deleteMailItem($del_id);
	
}

class CMail_items {
	function gethtml($par) {
		$id=$par[0];
		$index=$par[1];
		$row=$par[2]->DataSource->Rows[$index];
		return htmlspecialchars($id);
	}
}

include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>