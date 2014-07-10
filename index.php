<?php


/*

cid - GET Category ID
print - Print Mode
q - GET Search Queri
p - PageBar Current Page 1 and 0 are equal
spid - Static Page ID
NewsId - News ID
HistoryBackCnt - POST

iternal global $HidePrintLink for posts mainly

*/

/* @var $FESkinPage CFESkinPage */
$FESkinPage;

/* @var $fc FrontControler */
$fc;

  require_once('lib/SysUtils.php');
  enc_require_once('config/config.php');
  enc_require_once('lib/db.php');
  enc_require_once('lib/ErrorHandling.php');
  enc_require_once('lib/fe/lib.php');
  enc_require_once('lib/fe/news.php');
  enc_require_once('lib/fe/CFEGallery.php');
  enc_require_once('lib/fe/CComments.php');
  enc_require_once('lib/fe/CMembers.php');
  enc_require_once('lib/fe/CFEProducts.php');
  enc_require_once('templates/fe_lib_custom.php');

  
	beginRequest();

	CFESession::StartConditional();

	$db = getdb();

	$GLOBALS['HidePrintLink'] = $_SERVER['REQUEST_METHOD']!=='GET';

	$fc = FrontControler::getInstance($GLOBALS['CONFIG']['DefautlCID']);

	echo $fc->run();

?>