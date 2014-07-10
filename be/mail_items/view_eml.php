<?php
//application/octet-stream
header("Content-Type: message/rfc822");
header("Content-Disposition: attachment; filename=test.eml");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

require_once(dirname(__FILE__).'/../libCommon.php');

$db=getdb();

$row=$db->getrow("select * from mq_mail_items where id=?",array((int)$_GET['mail_item_id']));
@$b=$row['mail_body']=file_get_contents(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$row['mail_head_id']}_{$row['id']}.txt");
if(!$b) {
	throw new Exception("Cannot read /files/mf/mq_mail_items/{$row['mail_head_id']}_{$row['id']}.txt");
}
@$msg=unserialize($row['mail_body']);

$msg['additional_headers']=str_replace("\r\n","\n",$msg['additional_headers']);
$msg['additional_headers']=str_replace("\n","\r\n",$msg['additional_headers']);

$msg['message']=str_replace("\r\n","\n",$msg['message']);
$msg['message']=str_replace("\n","\r\n",$msg['message']);

echo "To: ".$msg['to']."\r\n";
echo "Subject: ".$msg['subject']."\r\n";
echo $msg['additional_headers'];
echo "\r\n\r\n".$msg['message'];	//2ta entera pred msg sa mnogo vajni :)

?>