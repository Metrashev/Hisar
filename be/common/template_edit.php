<?php

class EditTemplate {
	public $html_head='';
	
	public $body="<body>";
	public $form="<form  method='POST'>";
	public $title="<title></title>";
	public $pref_table_text='';	//tekst koito se izpiswa predi otwarqneto na tablicata (ex. Errors)
	
	public $main_table='<table width="main_table" align="center">
<tr>
<td>';
	
	public $meta=array();
	public $scripts=array();
	public $css=array();
	
	public $edit_id=0;
	
	public $hidden=array();
	
	public $openTemplateFunc='';
	public $closeTemplateFunc='';
	
	function __construct($edit_id) {
		$this->edit_id=$id;
		$this->init();
	}
	
	function init() {
		$this->html_head=<<<EOD
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

EOD;
		$this->meta=array(
			'charset'=>'<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset='.(isset($GLOBALS['CONFIG']['SITE_CHARSET'])?$GLOBALS['CONFIG']['SITE_CHARSET']:'UTF-8').'">',
		);
		
		$hidden=array(
		);
		
		/*$this->scripts=array(
		'/js/UT.js'=>"<script src='/js/UT.js'></script>",
		'/js/lib.js'=>"<script src='/js/lib.js'></script>",
		'Calendar'=>array(
				'/js/Calendar/calendar.js'=>"<script src='/js/Calendar/calendar.js'></script>",
				'/js/Calendar/calendar-setup.js'=>"<script src='/js/Calendar/calendar-setup.js'></script>",
				'/js/Calendar/lang/calendar-en.js'=>"<script src='/js/Calendar/lang/calendar-en.js'></script>",
			),
		);*/
		
		$this->scripts=array(
			'default'=>BE_Utils::includeDefaultJs()
		);
		
		
		if(JS_VALIDATION) {
			$scripts['/js/js_validator.js']="<script src='/js/js_validator.js'></script>";
		}
		
		$this->css=array(
		//	'Calendar'=>"<link rel='stylesheet' href='/js/Calendar/calendar-win2k-cold-1.css'>",
		//	'/be/lib.css'=>'<link rel="stylesheet" href="/be/lib.css">',
		);

	}

	function addScript($name,$file) {
		$this->scripts[$name]=$file;
	}
	
	function addCss($name,$file) {
		$this->css[$name]=$file;
	}
	
	function removeScript($name) {
		unset($this->scripts[$name]);
	}
	
	function removeCss($name) {
		unset($this->css[$name]);
	}
	
	function addMeta($text,$name='') {
		if(empty($name)) {
			$this->meta[]=$text;
		}
		else {
			$this->meta[$name]=$text;
		}
	}
	
	function removeMeta($name) {
		unset($this->meta[$name]);
	}
	
	
	
	function addHidden($name,$text) {
		$this->hidden[$name]=$text;
	}
	
	function removeHidden($name) {
		unset($this->hidden[$name]);
	}
	
	function openHead() {
		return $this->html_head;
	}
	
	function closeHead() {
		return empty($this->html_head)?'':'</head>';
	}
	
	function renderArray($array) {
		if(!is_array($array))
			return '';
		$str='';
		foreach ($array as $v) {
			if(is_array($v)) {
				$str.=$this->renderArray($v);
			}
			else {
				$str.=$v."\r\n";
			}
		}
		return $str;
	}
	
	function renderMeta() {
		return $this->renderArray($this->meta);
	}
	
	function renderScripts() {
		return $this->renderArray($this->scripts);
	}
	
	function renderCss() {
		return $this->renderArray($this->css);
	}
	
	function renderHidden() {
		return $this->renderArray($this->hidden);
	}
	
	function openBody() {
		return $this->body;
	}
	
	function closeBody() {
		return empty($this->body)?'':'</body>';
	}
	
	function openForm() {
		return $this->form;
	}
	
	function closeForm() {
		return empty($this->form)?'':'</form>';
	}
	
	function openTable() {
		return $this->main_table;
	}
	
	function closeTable() {
		return empty($this->main_table)?'':'</td>
</tr>
</table>';
	}
	
	function openTemplate() {
		if(!empty($this->openTemplateFunc)) {
			return call_user_func($this->openTemplateFunc,$this);
		}
		return $this->openHead().
		$this->title.
		$this->renderMeta().
		$this->renderScripts().
		$this->renderCss().
		$this->closeHead().
		$this->openBody().
		$this->openForm().
		$this->renderHidden().
		$this->openTable()
		;
	}
	
	function closeTemplate() {
		if(!empty($this->closeTemplateFunc)) {
			return call_user_func($this->closeTemplateFunc,$this);
		}
		return $this->closeTable().
		$this->closeForm().
		$this->closeBody()."\r\n</html>";
	}
}
?>