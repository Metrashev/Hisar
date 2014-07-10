<?php
if(isset($_GET['send'])) {
	echo "Your password was sent to the provided email";
	return;
}
?>
<form method="post" action="">
<?php

require_once(dirname(__FILE__)."/../../lib/be/fe_utils.php");
require_once(dirname(__FILE__)."/../../lib/be/lib1.php");

$errors=array();
if(isset($_POST['btSend'])) {
	$mail=$_POST['email'];
	if(!CValidation::is_valid_email_address($mail)) {
		$errors['email']="Invalid E-mail";
	}
	else {
		$e=getdb()->getrow("select username,userpass from members where email=?",array($mail));
		if(empty($e)) {
			$errors[]="Не е намерен потребител с посочената поща!";
		}
	}
	if(empty($errors)) {
		$data=array();
		$data['subject']="Your password for ".$_SERVER['HTTP_HOST'];
		$data['from']=EMAIL;
		$data['return_path']=EMAIL;
		$data['to']=$mail;
		$data['body']="<h3>Забравена парола</h3><b>Username:</b>{$e['username']}<br /><b>Password:</b>{$e['userpass']}";
		FE_Utils::send_mail($data);
		header("Location: /?cid={$_GET['cid']}&send=1");
		exit;
	}
}

if(!empty($errors)) {
	foreach ($errors as $k=>$v) {
		echo "<div class='error'>{$v}</div>";
	}
}
include(dirname(__FILE__)."/ForgotenPass.tpl");

?>
</form>