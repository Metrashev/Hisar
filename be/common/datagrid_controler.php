<?php

class CDataGridControler {
	
	public $pagesize;
	public $pagebar_id;
	public $unique_id;
	public $table;
	public $work_path;
	
	public $ta_xml;
	
	
	public $pagebar;
	public $dg;
	
	function __construct($table,$work_path,$pagesize=25,$pagebar_id="",$unique_id="") {
		$this->table=$table;
		$this->work_path=$work_path;
		$this->pagesize=$pagesize;
		$this->unique_id=empty($unique_id)?"dg_".$this->table:$unique_id;
		$this->pagebar_id=empty($unique_id)?"pb_".$this->table:$pagebar_id;		
	}
	
	function autoprepare($filter_session_name,$search_controler,$search_field="search",$clear_field="btClear") {
		$this->init();
		$this->processSession($filter_session_name,$search_field,$clear_field);
		$this->initPageBar();
		$this->setSearchFilter($search_controler,$search_field);
		$this->alterCount();
	}
	
	function init($ta_xml="",$postData=null) {
		if(empty($ta_xml)) {
			require_once($this->work_path."/table_desc.php");
		}
		
		if(is_null($postData)) {
			$postData=$_POST;
		}
		$this->ta_xml=$ta_xml;
		$this->dg=new DataGridNew($this->unique_id,$this->ta_xml['template'],$this->pagesize,$postData);
		
		if($_SERVER['REQUEST_METHOD']=='GET') {
			if(!empty($this->ta_xml['DataTable']['order_fields'])) {
				$this->dg->setOrder($this->dg->parseOrderString($this->ta_xml['DataTable']['order_fields']));
			}
			else {
				//$dg->DataSource->OrderFields=array(Control_Utils::getGetString($a,",",'',ENC_NONE,' '));
			}
		}
		
		$this->dg->createFromArray($this->ta_xml,true);
	}
	
	function processSession($filter_session_name,$search_field="search",$clear_field="btClear") {
		if(!empty($filter_session_name)) {
			if(isset($_POST[$this->unique_id]['neworder'])) {
				$_SESSION[$filter_session_name."_new_order"]=$_POST[$this->unique_id]['neworder'];
			}
			
			if(isset($_POST[$search_field])||isset($_POST[$clear_field])) {
				$_POST['_c_page'.$this->pagebar_id]=0;
			}
			
			if(isset($_POST['_c_page'.$this->pagebar_id])) {
				$_SESSION[$filter_session_name."_pb"]=$_POST['_c_page'.$this->pagebar_id];
			}
			
			if(isset($_SESSION[$filter_session_name."_pb"])) {
				$_POST['_c_page'.$this->pagebar_id]=$_SESSION[$filter_session_name."_pb"];
			}
		}
		if(!empty($filter_session_name)&&isset($_SESSION[$filter_session_name."_new_order"])&&!empty($_SESSION[$filter_session_name."_new_order"])) {
			$a=$this->dg->readOrder($_SESSION[$filter_session_name."_new_order"]);			
			$this->dg->DataSource->OrderFields=array($this->dg->getOrderString());
		}
	}

	function initPageBar() {
		$this->pagebar=new CPageBar($this->pagebar_id,$this->dg->DataSource->getCount(),$this->pagesize);

		$this->dg->setCurrentPage($this->pagebar->getCurrentPage());
		$this->dg->old_page=$this->pagebar->old_page;
		
		$this->dg->DataSource->Limit="limit ".$this->dg->getPageSize()*$this->dg->getCurrentPage().','.$this->dg->getPageSize();

	}
	
	function setSearchFilter($search_controls,$search_field="search") {
		/* @var $search_controler CSearchControler */		
		if (isset($_POST[$search_field])||$_POST['use_'.$search_field]==1) {
			if(!empty($search_controls)) {
				$this->dg->DataSource->AddWhere(ControlValues::getSearchString($search_controls,$_POST,array(),array()));	
				$_POST['use_'.$search_field]=1;
			}
		}
	}
	
	function alterCount() {
		$this->pagebar->totalItems=$this->dg->DataSource->getCount();
		$this->pagebar->alterPageCount();
	}
	
	function getAjaxScript() {
		$be=BE_DIR;
		$app_name=urlencode($this->ta_xml['app']);
		if((int)$this->ta_xml['use_ajax']) {
			$this->pagebar->custom_postback="myRender";	//definirana v /common/index.php
			return <<<EOD
<script type="text/javascript" src="{$be}ajax_test/ajax.js"></script>
<script type="text/javascript">

function myRender(obj,page,element) {
var p=collectForm(obj);
	if(!element) {
		element="";
	}
	else {
		element="&"+element;
	}
	showLoader();
	//loadRequest("{$be}ajax_test/request.php","app={$app_name}"+element+"&p="+page+"&bkp="+escape(self.location.pathname)+escape(self.location.search),testfun);
	loadRequest("{$be}ajax_test/request.php","app={$app_name}&"+p+"&bkp="+escape(self.location.pathname)+escape(self.location.search),testfun);
}

function showLoader() {
	var d=document.getElementById('dv_{$this->unique_id}');
	d.innerHTML="<div style='color:#993300;font-weight:bold;text-align:center;padding-top:15px;'><img align='absmiddle' src='{$be}i/loader.gif' />Loading ...</div>";
}

function testfun(request) {
	var d=document.getElementById('dv_{$this->unique_id}');
	//var d=document.getElementById('{$this->unique_id}');
	//var new_node=document.createElement("<div>");
	//alert(request.responseText);
	//alert(d.parentElement.tagName);
	//d.parentElement.replaceChild(new_node,d);
	//new_node.innerHTML=request.responseText;
	//alert(request.responseText);
	d.innerHTML=request.responseText;
	//drag_init();loadOrder("dg");reorderCells('dg',dragObj.order['dg']);
}

</script>
EOD;

			
	//define($c->control_id.'script','1');
		}
		return ""; 
	}
	
	function getBackButton($skip_back_button=false) {
		if(isset($_GET['bkp'])&&!$skip_back_button) {
			echo <<<EOD
			<table class="main_table" align="center" style="background:#E6E6E6;">
			<tr><td align="right"><input type="button" onclick="self.location='{$_GET['bkp']}';" value="Назад" /></td></tr>
			</table>
EOD;
		}
	}
	
	function render($skip_back_button=false) {
		$h=FE_Utils::getSeparator();
		$s=$this->getBackButton($skip_back_button).$this->getAjaxScript()."<div id='dv_{$this->unique_id}'>";
		$s.=$this->pagebar->render();
		$s.= $h;
		
		$this->dg->DataSource->BuildQuery();
		$s.=$this->dg->render();
		
		$s.=$h;
		$s.= $this->pagebar->render();
		$s.= "</div>";
		return $s;
	}
	
}

?>