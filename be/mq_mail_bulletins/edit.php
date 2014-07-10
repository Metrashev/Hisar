<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_groups");

$in_edit_table="mq_mail_bulletins";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);
$db=getdb();
function prepareMQ($groupId,$data,$bulletin_id=0) {
	$d=array();
	$d['name']=$data['subject'];
	$d['start_date']=$data['date_to_send'];
	$d['created_date']=date('Y-m-d H:i:s');
	$qId=ControlWriter::Write("mq_mail_heads",$d,0);
	
//	$css=file_get_contents(dirname(__FILE__)."/../../bulletin.css");
	$data['body']=<<<EOD
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style>
	{$css}
	</style>
	<BASE href="http://{$_SERVER['HTTP_HOST']}">
	</head>
	<body>
	{$data['body']}
	</body>
	</html>
EOD;

	$att=array();
	if($bulletin_id) {
		$attachments=getdb()->getassoc("select * from mq_mail_attachments where mq_mail_bulletin_id='$bulletin_id'");
		foreach ($attachments as $k=>$v) {
			$ext=FE_Utils::getFileExt($v['uploaded_file']);
			$att[]=array(
				'name'=>$v['uploaded_file'],
				'file'=>file_get_contents($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments/".$k."_uploaded_file".$ext),
			);
		}
	}
	
	$mq=new CMQ();
	$mq->addEmailGroup($qId,$groupId,$data['from_email'],$data['subject'],$data['body'],$data['date_to_send'],"","",$att,$data['from_email']);
	return $qId;
}

?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>
<?=BE_Utils::loadTinyMce("body","sp.tpl");?>
</head>
<body >
<form id='f1' method=POST enctype="multipart/form-data">
<table class="main_table" align="center">
<tr>
<td>

<?php



include(dirname(__FILE__).'/controls.php');
$con=getmq_mail_bulletinsControls();

$errors=array();

$errors=array();

if($d_id=(int)$_POST['hd_del_attachment']) {
	$row=$db->getOne("select uploaded_file from mq_mail_attachments where id='{$d_id}'");
	$ext=FE_Utils::getFileExt($row);
	@$b=unlink($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments/".$d_id."_uploaded_file".$ext);
	
	if($b===false) {
		$errors[]="Възникна грешка при изтриване на файла!";	
	}
	else {
		$db->Execute("delete from mq_mail_attachments where id='{$d_id}'");
		$errors[]="Файлът е изтрит!";	
	}
}

if(isset($_POST['btAddFile'])) {
	if(is_uploaded_file($_FILES['attachment']['tmp_name'])) {
		if(!is_dir($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments")) {
			mkdir($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments",0777);
			chmod($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments",0777);
		}
		$data=array();
		$data['mq_mail_bulletin_id']=$in_edit_id;
		$data['uploaded_file']=$_FILES['attachment']['name'];
		$a_id=ControlWriter::Write("mq_mail_attachments",$data,0);
		$ext=FE_Utils::getFileExt($_FILES['attachment']['name']);
		move_uploaded_file($_FILES['attachment']['tmp_name'],$GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments/".$a_id."_uploaded_file".$ext);
		chmod($GLOBALS['MANAGED_FILE_DIR']."/mq_mail_attachments/".$a_id."_uploaded_file".$ext,0777);
		$errors[]="Файлът е добавен";
	}
	else {
		$errors[]="Не е избран файл!";
	}
}

if(isset($_POST['btSave'])||isset($_POST['btSend'])) {

	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	
	if (isset($_POST['btSend'])) {
		if(!(int)$_POST['in_data']['mail_group_id']) {
			$wd['errors'][]="Изберете <b>Група с e-mail-и</b>";
		}
	}
	
	if (empty($wd['errors'])) {
/*
	$order=$wd['data']['order_field'];
	unset($wd['data']['order_field']);
	if(!$in_edit_id) {
		$order=(int)$order;
	}
*/
/*
			if(!$in_edit_id) {
				$wd['data']['created_date']=date('Y-m-d H:i:s');
				$wd['data']['created_by']=Users::getUserId();
			}
			$wd['data']['updated_date']=date('Y-m-d H:i:s');
			$wd['data']['updated_by']=Users::getUserId();
*/
			$wd['data']['is_sent']=0;
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
			if(USE_AUDIT_LOG) {
				CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
			}
			if(isset($_POST['btSend'])) {
				$mq_id=prepareMQ((int)$_POST['in_data']['mail_group_id'],$wd['data'],$n_id);
			}
//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
				header("Location: ?id={$n_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
				//header("Location: ".($_GET['bkp']));
				exit;		
//			}
//			if(is_array($new_relation)) {
//				$errors=$new_relation;
//			}
//			else {
//				header("Location: ".($_GET['bkp']));
//				exit;
//			}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();

if($in_edit_id) {
	$GLOBALS['attachments']=$db->getAssoc("select * from mq_mail_attachments where mq_mail_bulletin_id='{$in_edit_id}'");
}

if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
		$_POST['in_data']['mail_group_id']=$array['in_data']['mail_group_id'];
	}	
}
else {
	$array=$_POST;
}
FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);

//echo $hhh= $p->render();

//if($in_edit_id) {	
//	include(dirname(__FILE__).'/../_relations/list.php');		
//	include(dirname(__FILE__).'/../_relations/index.php');
//}

if(USE_AUDIT_LOG&&$in_edit_id) {
	echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);
}

echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>