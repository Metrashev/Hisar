<?php
	
	$cid = (int)$_GET['cid'];
	$db = getdb();
	if(isset($_GET['spid'])){
		$data = $db->getRow("SELECT * FROM static_pages WHERE id=?",array($_GET['spid']));
		include(dirname(__FILE__).'/../Core/StaticPage.php');
		return ;
	}

	$data = $db->getRow("SELECT * FROM static_pages WHERE cid={$cid}");

	$GLOBALS['HidePrintLink'] = true;
	
	require_once(dirname(__FILE__).'/../../lib/fe/FormProcess.php');
	require_once(dirname(__FILE__).'/../../lib/be/fe_utils.php');
	require_once(dirname(__FILE__).'/../../lib/fe/CAntiSpam.php');


	CFESession::start();

	$fp=new FormProcessor();
	ob_start();
	include(dirname(__FILE__).'/form_'.LNG_CURRENT.'.html');
	$str=ob_get_clean();
	
	$fp->loadTemplate($str);
	$fp->autoProcessFields(true,true,true);
	
	if($_SERVER['REQUEST_METHOD']=='GET'){
		$_POST['agree']='1';
	}
	$fp->fillData($_POST);

	$errors=array();
	if(isset($_POST['Submit'])) {
		
		$errors = $fp->validate();
		

		$errors = $fp->validate();

		if(!isset($errors['spam_code']) && !CAntiSpam::checkCode($_POST['spam_code'])) {
			$errors['spam_code']='<b>Spam Code</b> is invalid!';
		}
		
		if(empty($errors)) {




				$arr=$_POST;
				unset($arr['Submit']);
				unset($arr['spam_code']);
				$arr['themes'] = implode(',',$arr['themes']);
				$arr['regtime'] = date("Y-m-d H:i:s");
				$db->autoExecute('subscribers', $arr, CDB::AUTOQUERY_INSERT_ON_DUPLICATE);

				

  				 require_once(dirname(__FILE__).'/../../lib/mime_mail/htmlMimeMail.php');
  				
				$file = $fp->getReadOnlyVersion();
    			$mail = new htmlMimeMail();
    			$mail->setHtmlCharset('UTF-8');
   				$mail->setSubject("Register My Interests Form");
   				$mailBody = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><style>".file_get_contents("lib.css")."</style><body>$file</body>";
    			$mail->setHtml($mailBody);
   				$bla=$mail->send(array(PROGRAMME_FEEDBACK_EMAIL));  
    			
				if($bla){
					$spid = $db->getOne("SELECT id FROM static_pages WHERE cid=? AND def=0", array($cid));
					if($spid){
						header("Location: /?cid={$cid}&spid={$spid}");
					} else {
						header("Location: /");
					}
					exit();

				}
		}

	}
	if(!empty($errors)) {
		$str_errors=FE_Utils::renderErrors($errors);
	}
	else {
		$str_errors='';
	}

	$str = $fp->getHTML();
	$_SESSION[CAntiSpam::$session_var]=CAntiSpam::generateCode();
echo <<<EOD
	<p style="color:red;">$str_errors</p>
	$str

EOD;
?>

