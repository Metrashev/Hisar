<?php


class CallManager {
	static $IsReturn = false;
	static $IsCall = false;
	static $returnParams = array();
	static $callParams = array();
	
	static private $InitDone=false;
	
	static function Init(){
		if(self::$InitDone) return ; // SingleTon Init
		//session_start(); // Razchita se che e pusnata
		self::$IsReturn = isset($_GET['BackKey']) && isset($_SESSION['CallStack'][$_GET['BackKey']]);
		self::$IsCall = isset($_GET['GoKey']) && isset($_SESSION['CallStack'][$_GET['GoKey']]);
		
		if(self::$IsCall){
			self::$callParams = $_SESSION['CallStack'][$_GET['GoKey']]['Params'];
		}
		
		if(self::$IsReturn){
			$backKey = $_GET['BackKey'];
			$preserveData = $_SESSION['CallStack'][$backKey];
			$_SESSION['CallStack'][$backKey] = '';
			unset($_SESSION['CallStack'][$backKey]);
			$_POST = $preserveData['POST'];
			$_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
			self::$returnParams = isset($_GET['ReturnParams']) ? $_GET['ReturnParams'] : $preserveData['ReturnParams'];
			$_SERVER['REQUEST_METHOD'] = $preserveData['REQUEST_METHOD'];
		}
		
		self::garbageCollector();
		self::$InitDone = true;
	}
	
	static function garbageCollector(){
		// TO be
	}
	
	static function getCrumbsPath(){
		$key = $_GET['GoKey'];
		$Path = array();
		while ($q = $_SESSION['CallStack'][$key]){
			$Path[] = $q['Label'];
			$key = $q['PrevKey'];
		}
		return implode(' >> ', $Path);
	}
	
	static function goToUrl($URL, $Params=array(), $Label=''){
		$backKey = time();
		
		$_GET['BackKey'] = $backKey;
		
		$preserveData = array();
		$preserveData['Label'] = $Label;
		$preserveData['PrevKey'] = $_GET['GoKey'];
		$preserveData['POST'] = $_POST;
		$preserveData['Params'] = $Params;
		$preserveData['URI'] = $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET,null,'&');
		$preserveData['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
		
		$_SESSION['CallStack'][$backKey] = $preserveData;
		
		header("Location: $URL&GoKey=$backKey");
		exit();
	}
	
	static function goBack($Params=array()){
		if(!self::$IsCall){
			throw new Exception('Not a call');
		}
		$backKey = $_GET['GoKey'];
		if($Params!==null) $_SESSION['CallStack'][$backKey]['ReturnParams'] = $Params;
		$url = $_SESSION['CallStack'][$backKey]['URI'];
		header("Location: $url");
		exit();
	}
	
	static function getBackLink($Params=array()){
		if(!self::$IsCall){
			throw new Exception('Not a call');
		}
		$backKey = $_GET['GoKey'];
		//$_SESSION['CallStack'][$backKey]['ReturnParams'] = $Params;
		$url= $_SESSION['CallStack'][$backKey]['URI'];
		if(isset($Params)){
			$url.='&amp;'.http_build_query(array('ReturnParams'=>$Params));
		}
		return  $url;
	}
	
	static function setReturnParams($Params){
		if(!self::$IsCall){
			throw new Exception('Not a call');
		}
		$_SESSION['CallStack'][$_GET['GoKey']]['ReturnParams'] = $Params;
	}
	
	static function isReturn(){
		return self::$IsReturn;
	}
	
	static function isCall(){
		return self::$IsCall;
	}
}

?>