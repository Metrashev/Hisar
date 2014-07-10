<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_groups");

if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_mq_mail_bulletins";
	$search=getmq_mail_bulletinsControls('search');
}
$__del_var="hdDeletemq_mail_bulletins";
$__editTable="mq_mail_bulletins";
$fn_Delete="";
$fn_Delete="del_mq_mail_bulletins";

function del_mq_mail_bulletins($del_id) {
	$db=getdb();
	$attachments=$db->getassoc("select id,uploaded_file from mq_mail_attachments where mq_mail_bulletin_id='{$del_id}'");
	foreach ($attachments as $k=>$v) {
		$ext=FE_Utils::getFileExt($v);
		@$b=unlink($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments/".$k."_uploaded_file".$ext);
	}
	$db->execute("delete from `mq_mail_attachments` where mq_mail_bulletin_id='{$del_id}'");
	$db->execute("delete from `mq_mail_bulletins` where id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
?>