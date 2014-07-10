<?php
require_once('db.php');
require_once('mime_mail/htmlMimeMail.php');

function sendEmail($data) {
	$mail =& new htmlMimeMail();

	$mail->setFrom($data['from']);
	$mail->setSubject($data['subject']);
	
	if($data['return_path'])
		$mail->setReturnPath($data['return_path']);

	if($data['cc']!="")
		$mail->setCc($data['cc']);

	if($data['bcc']!="")
		$mail->setBcc($data['bcc']);

		
	if(is_array($data['attachments']))
	{
		foreach ($data['attachments'] as $key=>$val){
			if($val['file_name'] && $val['data'])
				$mail->addAttachment($val['data'], $val['file_name']);
				
		}
	}

	$data['body'] = eregi_replace(' href="/', " HREF=\"HTTP://{$_SERVER['HTTP_HOST']}/", $data['body']);
	
	$mail->setHtml($data['body'], null, dirname(__FILE__).'/../www/');
	
	return $mail->send(array($data['to']));
}

function getEmaildHTMLHeader(){
	return <<<EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
BODY, P, DIV, TD, TH { color:#888888; font-family: Verdana, Geneva, Arial, Sans-serif; font-size: 10pt;}
</style>
<BASE href="http://{$_SERVER['HTTP_HOST']}">
</head>
<body>
EOD;
}
?>