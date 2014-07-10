<?php
//sesiqta trqbwa da e startirana

class CSessionStack {
	
	static function getName() {
		return "SessionStack";
	}
	
	static function addEntry($data) {
		$key=time();
		$_SESSION[CSessionStack::getName()][$key]=$data;
		return $key;
	}
	
	static function clear() {
		$_SESSION[CSessionStack::getName()]=array();
	}
	
	static function cleanEntry($key) {
		unset($_SESSION[CSessionStack::getName()][$key]);
	}
	
	static function addItemToEntry($key,$data) {
		if(!is_array($data)||!is_array($_SESSION[CSessionStack::getName()][$key])) {
			return false;
		}
		$_SESSION[CSessionStack::getName()][$key]+=$data;
	}
	
	static function addCaller($key,$value) {
		$_SESSION[CSessionStack::getName()][$key]['caller']=$value;
	}
	
	static function getEntry($key) {
		return $_SESSION[CSessionStack::getName()][$key];
	}
	
	static function findPostEntry($key,$name,$repeat_index=null) {
		$postName=ControlValues::createPostName($name,$repeat_index);
		return Control_Utils::getPostArray($postName,$_SESSION[CSessionStack::getName()][$key]);		
	}
	
	static function isReturn($key) {
		return isset($_SESSION[CSessionStack::getName()][$key]);
	}
	
	static function getCaller($key) {
		return $_SESSION[CSessionStack::getName()][$key]['caller'];
	}
}

?>