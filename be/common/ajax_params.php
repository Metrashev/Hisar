<?php

if(isset($_POST['ajax_params'])) {
	$ajax_params=$_POST['ajax_params'];
	$ajax_params=urldecode($ajax_params);
	$ajax_params=base64_decode($ajax_params);
	$ajax_params=gzuncompress($ajax_params);
	
	$ajax_params=unserialize($ajax_params);

	if(is_array($ajax_params)) {
		extract($ajax_params);
		require_once(dirname(__FILE__)."/template_index.php");
		$__template_index=new IndexTemplate(0);
		$__template_index->form="";
		$__template_index->body="";
		$__template_index->html_head="";
		$__template_index->title="";
		$__template_index->meta="";
		$__template_index->scripts=array();
		$__template_index->css=array();
		$__template_index->hidden=array();
	}
}

?>