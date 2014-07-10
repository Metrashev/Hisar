<?php
define('ITTI_VERSION', '1');
define('ITTI_VERSION_MIN', '3');

define('DATE_FORMAT','%d.%m.%Y');
define('TIME_FORMAT','%H:%M:%S');

define('LNG_BG','bg');	
define('LNG_EN','en');
define('LNG_DE','de');

define('DEFAULT_LANGUAGE','bg');

define('USE_OWN_USERS', false);
define('USE_AUDIT_LOG', false);
define('GALLERIES_ENABLED', true);
define('MEMBERS_ENABLED', false);
define('COMMENTS_ENABLED', false);
define('MQ_ENABLED', false);
define('ATTRIBUTES_ENABLED', false);
define('POLLS_ENABLED', false);
define('ADVERTS_ENABLED', false);
define('PRODUCTS_ENABLED', false);
define('FLASH_UPLOAD_ENABLED', true);

include(dirname(__FILE__)."/dir_config.php");


define('DEBUG_MODE', strpos($_SERVER['HTTP_HOST'], 'itti.bg')>0);

if(DEBUG_MODE){
	$CONFIG['DSN']="mysql://root:kustendil@localhost/hisar";
} else {
	$CONFIG['DSN']="mysql://template1:template1@localhost/nodb";
}

$CONFIG['NAMES_CHARACTERS_SET']='UTF8';
$CONFIG['SITE_CHARSET']='UTF-8';
mb_internal_encoding($CONFIG['SITE_CHARSET']);

$CONFIG['ApplicationState']= DEBUG_MODE ? 'Debug' : '';
$CONFIG['ErrorLevel'] = E_ALL & ~E_NOTICE & E_WARNING & ~E_STRICT & E_DEPRECATED;



$CONFIG['SiteLanguages'] = array(LNG_BG=>'Bulgarian', LNG_EN=>'English');

$CONFIG['DefautlCID'] = 4;

$CONFIG["login_cid_bg"]=48;
$CONFIG["login_cid_en"]=48;


$CONFIG['Skins'] = array();
$CONFIG['Skins'][1] = array('name'=>'Main', 'file'=>'templates/Core/template.php');

$CONFIG['PrintSkin'] = 'templates/Core/printSkin.php';
$CONFIG['Error404Skin'] = 'Error404.php';


$CONFIG['FEPageTypes'] = array();
$CONFIG['FEPageTypes'][1] = array('name'=>'Static Page', 'class'=>'CFEStaticPage');
$CONFIG['FEPageTypes'][2] = array('name'=>'News', 'class'=>'CFENewsPage');
$CONFIG['FEPageTypes'][3] = array('name'=>'Redirection', 'class'=>'CFERedirectPage');
if(GALLERIES_ENABLED) $CONFIG['FEPageTypes'][4] = array('name'=>'Gallery', 'class'=>'CFEGallery');

$CONFIG['FEPageTypes'][255] = array('name'=>'Custom Page', 'class'=>'CFECustomPage');

define("PRODUCT_TEMPLATE_TYPE",5);

/*
[Template ID][] //Template ID - 0 e za vsichki templeiti

*/
$CONFIG['CFERedirectPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/redirect.php';

$CONFIG['CFENewsPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/news.php';
if(COMMENTS_ENABLED)	$CONFIG['CFENewsPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/comments.php'; 
if(GALLERIES_ENABLED)	$CONFIG['CFENewsPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/add_gallery.php';
if(COMMENTS_ENABLED)	$CONFIG['CFEStaticPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/comments.php';
if(GALLERIES_ENABLED)	$CONFIG['CFEStaticPage']['be']['tree'][0][] = BE_DIR.'categories/Custom/add_gallery.php';
if(GALLERIES_ENABLED)	$CONFIG['CFEGallery']['be']['tree'][0][] = BE_DIR.'categories/Custom/gallery.php';


$CONFIG['CFEStaticPage']['be']['menu'] = BE_DIR.'static_pages/edit.php?loadDef=1&amp;n_cid=';
$CONFIG['CFENewsPage']['be']['menu'] = BE_DIR.'news_pages/?loadDef=1&amp;cid=';
$CONFIG['CFEGallery']['be']['menu'] = BE_DIR.'gallery/?cid=';


$CONFIG['CFEStaticPage']['templates'][1] = array('name'=>'Main', 'file'=>'templates/Core/StaticPage.php');
$CONFIG['CFENewsPage']['templates'][1] = array('name'=>'Main', 'fileList'=>'templates/News/NewsList.php', 'fileFull'=>'templates/News/FullNews.php');
$CONFIG['CFEGallery']['templates'][1] = array('name'=>'Gallery', 'file'=>'templates/Gallery/gallery.php');
//$CONFIG['CFEGallery']['templates'][2] = array('name'=>'Paged', 'file'=>'templates/Gallery/gallery2.php', 'ItemsPerPage'=>18);
$CONFIG['CFEGallery']['templates'][3] = array('name'=>'Gallery3', 'file'=>'templates/Gallery/gallery3.php');
$CONFIG['CFEGallery']['templates'][4] = array('name'=>'Gallery4', 'file'=>'templates/Gallery/gallery4.php');



$CONFIG['CFECustomPage']['templates'][1] = array('name'=>'Home Page', 'file'=>'templates/Core/HomePage.php');
/*
if(POLLS_ENABLED) $CONFIG['CFECustomPage']['templates'][2] = array('name'=>'Poll Archive', 'file'=>'templates/Poll/archive.php');
$CONFIG['CFECustomPage']['templates'][3] = array('name'=>'Members - Login', 'file'=>'templates/Members/Login.php');
$CONFIG['CFECustomPage']['templates'][4] = array('name'=>'Members - Register', 'file'=>'templates/Members/Register.php');
$CONFIG['CFECustomPage']['templates'][5] = array('name'=>'Forgotten password', 'file'=>'templates/Members/ForgotenPass.php');
*/

$CONFIG['CFECustomPage']['templates'][6] = array('name'=>'Search', 'file'=>'templates/search/index.php');
$CONFIG['CFECustomPage']['templates'][7] = array('name'=>'Site Map', 'file'=>'templates/Core/sitemap.php');
$CONFIG['CFECustomPage']['templates'][8] = array('name'=>'Gallery', 'file'=>'templates/Gallery/custom_gallery.php');


$CONFIG['CFECustomPage']['be']['menu'][1] = BE_DIR.'static_pages/edit.php?loadDef=1&amp;n_cid=_#CID#_';



/*
$CONFIG['CFECustomPage']['templates'][2] = array('name'=>'Gallery', 'file'=>'templates/Gallery/gallery.php');
$CONFIG['CFECustomPage']['be']['menu'][2] = BE_DIR.'gallery/?cid=_#CID#_';
$CONFIG['CFECustomPage']['be']['tree'][2] = BE_DIR.'categories/Custom/gallery.php';
*/




$CONFIG['CFEStaticPage']['be']['delete'] = array('file'=>BE_DIR.'categories/del_functions.php','functions'=>array('getMessage'=>array('sp_class','getMessage'),'process_delete'=>array('sp_class','processDelete'))); 
$CONFIG['CFENewsPage']['be']['delete'] = array('file'=>BE_DIR.'categories/del_functions.php','functions'=>array('getMessage'=>array('news_class','getMessage'),'process_delete'=>array('news_class','processDelete'))); 


$CONFIG['AutoLoad']=array(
	
	'CBONews'=>'lib/fe/news.php',
	'CFENewsPage'=>'lib/fe/news.php',
	'CFEGallery'=>'lib/fe/CFEGallery.php',
	'CBOGallery'=>'lib/fe/CFEGallery.php',
	'CComments'=>'lib/fe/CComments.php',
	'CMembers'=>'lib/fe/CMembers.php',

);


$GLOBALS['CONFIG']=$CONFIG;


$GLOBALS['ADVERT_POSITIONS']=array(0=>'',1=>'Център',2=>'Лява колона 1',3=>'Лява колона 2',4=>'Дясна колона 1',5=>'Дясна колона 2');


$GLOBALS['AdsPositionsSize'] = array(
	1=>array(100,100),
);

$GLOBALS['AdsTypes']=array(
	1=>"Image",
	2=>"Flash",
	3=>"Text",
);

$GLOBALS['MANAGED_FILE_DIR']=dirname(__FILE__)."/../files/mf/";
$GLOBALS['MANAGED_FILE_DIR_IMG']="/files/mf/";


/* Just for BE  */
$GLOBALS['YES_NO']=array(
		0=>"NO",
		1=>"YES"
);

$GLOBALS['VALID_IMAGE_EXTENSIONS']=array(
	'.jpg',
	'.png',
	'.gif',
);

$GLOBALS['VALID_VIDEO_EXTENSIONS']=array(
	'.avi',
	'.mpeg',
	'.flv',
);

$GLOBALS['FFMPEG_FILE'] = '/usr/local/bin/ffmpeg';


define('JS_VALIDATION',true);
define('JS_ERROR_COLOR','#FFDBA6');

define("RIGHTS_DELETE",1);
define("RIGHTS_INSERT",2);
define("RIGHTS_UPDATE",4);
define("RIGHTS_READ",8);

$GLOBALS['mq_mail_status_array']=array(
	0=>"Waiting",
	1=>"Sending started",
	2=>"Sending ended",
	3=>"Sending interupted",
	4=>"Error from function mail()",
);

$GLOBALS['user_status_array']=array(
	1=>"Admin",
	2=>"Employee",
);

define("OPERATION_ADD",1);
define("OPERATION_UPDATE",2);
define("OPERATION_DELETE",3);
define("OPERATION_EXCEL",4);

$GLOBALS['operation_array']=array(
	OPERATION_ADD=>"Record Created",
	OPERATION_UPDATE=>"Record Updated",
	OPERATION_DELETE=>"Record Deleted",
	OPERATION_EXCEL=>"Change of EXCEL data",
);

$GLOBALS['calendar_month'] = array(1=>'Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември', 'Декември');
$GLOBALS['calendar_month_short'] = array(1=>'Яну','Фев','Мар','Апр','Май','Юни','Юли','Авг','Сеп','Окт','Ное', 'Дек');

$GLOBALS['customCids'] = array(
	'bg'=>array(
		'gallery'=>28
	),
	'en'=>array(
		'gallery'=>29,
	),
);


include(dirname(__FILE__)."/resources.php");
?>
