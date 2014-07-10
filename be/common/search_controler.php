<?php

class CSearchControler {
	public $work_path;
	
	public $table;
	public $filter_session_name;
	public $search_controls;
	public $is_multiform=false;	
	
	function __construct($table,$work_path,$filter_session_name) {
		$this->table=$table;
		$this->work_path=$work_path;
		$this->filter_session_name=$filter_session_name;		
		$this->search_controls=array();
	}
	
	function autoprepare($filter_post_field="in_data",$search_field="search",$clear_field="btClear") {
		//$this->search_controls=$this->getControls();
		$this->getSearch();
		$this->prepareSession($filter_post_field,$search_field,$clear_field);
		$this->processClear($filter_post_field);
	}
	
	function getControls($type='search') {
		require_once($this->work_path."/controls.php");
		$func="get".$this->table."Controls";
		return $func($type);
	}
	
	function getSearch($check_multiform=true) {		
		if($check_multiform) {
			$this->search_controls= $this->is_multiform?array():$this->getControls("search");
		}
		else {
			$this->search_controls= $this->getControls("search");
		}
		return $this->search_controls;
	}
	
	function prepareSession($filter_post_field="in_data",$search_field="search",$clear_field="btClear") {
		if(!empty($this->filter_session_name)) {
			if(isset($_POST[$clear_field])) {
				unset($_SESSION[$this->filter_session_name]);
			}
			else {
				if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_SESSION[$this->filter_session_name])) {
					$_POST[$filter_post_field]=$_SESSION[$this->filter_session_name];
					$_POST['use_'.$search_field]=1;
					$_SERVER['REQUEST_METHOD']='POST';
				}
				else {				
					$_SESSION[$this->filter_session_name]=$_POST[$filter_post_field];
					if(isset($_SESSION[$this->filter_session_name]['search'])) {
						unset($_SESSION[$this->filter_session_name]['search']);
					}
					if(isset($_SESSION[$this->filter_session_name]['btSelect'])) {
						unset($_SESSION[$this->filter_session_name]['btSelect']);
					}
				}
			}
		}
		if (isset($_POST[$search_field])||$_POST['use_'.$search_field]==1) {
			$_POST['use_'.$search_field]=1;	
		}
	}
	
	function processClear($filter_post_field="in_data",$clear_button="btClear") {
		if(isset($_POST[$clear_button])){
			if(!isset($this->search_controls['clear_fields'])||empty($this->search_controls['clear_fields'])||!is_array($this->search_controls['clear_fields'])) {
				if(isset($filter_post_field)) {
					unset($_POST[$filter_post_field]);
				}
				else {
					$_POST=array();
				}
			}
			else {
				foreach ($this->search_controls['clear_fields'] as $v) {
					unset($_POST[$v]);
				}
			}
		}
	}
	
	function render($is_search=false) {
		$search="";
		//if(isset($_GET['search'])) {
		if($is_search) {
			$search=<<<EOD
			<div width="100%" align="right"><input type="submit" value="Select" name="btSelect" /></div>
EOD;
		}

		if(!empty($this->search_controls)) {
			return $search.Master::create($this->search_controls,$this->search_controls['template']['dir'],$_POST,null,false);
		}
		return $search;		
	}
	
}

?>