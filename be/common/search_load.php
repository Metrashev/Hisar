<?php

$__search_loaded=1;
if(isset($filter_session_name)) {
	if(!isset($filter_post_field)) {
		$filter_post_field="in_data";
	}
	
	
	if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_SESSION[$filter_session_name])) {
		if(is_array($filter_post_field)) {
			foreach ($filter_post_field as $fpn) {
				$_POST[$fpn]=$_SESSION[$filter_session_name][$fpn];
			}
		}
		else {
			$_POST[$filter_post_field]=$_SESSION[$filter_session_name];
		}
		$_POST['use_search']=1;
		$_SERVER['REQUEST_METHOD']='POST';
	}
	else {
		if(is_array($filter_post_field)) {
			foreach ($filter_post_field as $fpn) {
				$_SESSION[$filter_session_name][$fpn]=$_POST[$fpn];
			}
		}
		else {
			$_SESSION[$filter_session_name]=$_POST[$filter_post_field];
		}
		if(isset($_SESSION[$filter_session_name]['search'])) {
			unset($_SESSION[$filter_session_name]['search']);
		}
		if(isset($_SESSION[$filter_session_name]['btSelect'])) {
			unset($_SESSION[$filter_session_name]['btSelect']);
		}
	}
	
	if(isset($_POST['btClear'])) {
		unset($_SESSION[$filter_session_name]);
	}
	

}



if (isset($_POST['search'])||$_POST['use_search']==1) {
	$_POST['use_search']=1;
	
}




if(isset($_POST['btClear'])) {
	if(!isset($search['clear_fields'])||empty($search['clear_fields'])||!is_array($search['clear_fields'])) {
		if(isset($filter_post_field)) {
			if(is_array($filter_post_field)) {
				foreach ($filter_post_field as $fpn) {
					unset($_POST[$fpn]);
				}
			}
			else {
				unset($_POST[$filter_post_field]);
			}
		}
		else {
			$_POST=array();
		}
	}
	else {
		foreach ($search['clear_fields'] as $v) {
			unset($_POST[$v]);
		}
	}
}
?>