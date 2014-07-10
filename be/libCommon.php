<?php

	$dir=dirname(__FILE__);
	require_once($dir.'/../config/config.php');
	require_once($dir.'/config/control_classes.php');
	require_once($dir.'/../lib/be/users.php');
	define("isPostback",$_SERVER['REQUEST_METHOD']=='POST');

if(!defined("CRON_PRINT_MESSAGES")) {
	session_start();
	if(USE_OWN_USERS) {
		if(!Users::getUserId()) {
			header("Location: ".BE_DIR);
			exit;
		}
	}
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: private");
	header('Content-type: text/html; charset=UTF-8');	
}
    	
//	include($dir.'/../lib/be/phpHeaders.php');	//includes all in __autoload
	require_once($dir.'/../lib/db.php');	//getdb() ->required 
	require_once($dir.'/../lib/be/ConNew.php');	//getdb() ->required 
	require_once($dir.'/../lib/be/ControlsExNew.php');	
	require_once($dir.'/../lib/be/CSearch.php');	
	require_once($dir.'/../lib/search_utils.php');	
	require_once($dir.'/../lib/be/tree.php');	
	require_once($dir.'/../lib/ErrorHandling.php');	
	
	pPrado::beginRequest();
	
	
	//$error_handler=ErrorsManager::getInstance();
	
	/*if(isset($_GET["return_point"])&&$_SERVER['REQUEST_METHOD']=="POST") {
		CSessionStack::cleanEntry($_GET["return_point"]);			
	}
	
	if(!isset($_GET['bkp'])&&!isset($_GET["return_point"])) {
		CSessionStack::clear();
		
	}
	
	if($_SERVER['REQUEST_METHOD']=="GET"&&CSessionStack::isReturn($_GET["return_point"])) {
		$_POST=CSessionStack::getEntry($_GET["return_point"]);
		//CSessionStack::cleanEntry($_GET["return_point"]);
		$_SERVER['REQUEST_METHOD']="POST";			
	}*/
	
	
	
	
	$REQUEST_METHOD=$_SERVER['REQUEST_METHOD'];
	
	function __autoload($funcName) {
		
		if(!$funcName)
			return;
		$f=array(	
			"IndexTemplate"=>BE_DIR."common/template_index.php",		
			'CRelations'=>'/lib/be/CRelations.php',
			'CLib'=>'/lib/be/lib1.php',
			'CValidation'=>'/lib/be/lib1.php',
		
			'CSessionStack'=>'/lib/be/CSessionStack.php',
			'BE_Utils'=>'/lib/be/fe_utils.php',
			'CTab'=>'/lib/be/CTabControl.php',
			'FE_Utils'=>'/lib/be/fe_utils.php',
			'DB_Utils'=>'/lib/be/fe_utils.php',
			'pPrado'=>'/lib/be/fe_utils.php',
			'Users'=>'/lib/be/users.php',
			'CUserLogs'=>'/lib/be/CUserLogs.php',
		
			'COrder'=>"/lib/be/order.php",			
			'CMQ'=>"/lib/be/CMQ.php",			
			'CMailListProvider'=>"/lib/be/CMailListProvider.php",	
			
			'CAttributes'=>"/lib/be/CAttributes.php",	
		);
		if(is_file(dirname(__FILE__).'/..'.$f[$funcName])) {		
			require_once(dirname(__FILE__).'/..'.$f[$funcName]);
		}
		else {
			echo "<br />".dirname(__FILE__).'/..'.$f[$funcName]." not found<br />";
		}
		
	}
	
?>