<?php

if(isset($_GET['bkp'])&&!isset($__skip_back_button)) {
	echo <<<EOD
	<table class="main_table" align="center">
	<tr><td align="left"><input type="button" onclick="self.location='{$_GET['bkp']}';" class="submit" value="Back" /></td></tr>
	</table>
EOD;
}

if(!isset($pagesize)) {
	$pagesize=25;
}
if(!isset($pagebar_id)) {
	$pagebar_id='pb';
}
if(!isset($unique_id)) {
	$unique_id='dg';
}

$dg=new DataGridNew($unique_id,$ta_xml['template'],$pagesize,$_POST);

if($_SERVER['REQUEST_METHOD']=='GET') {
	
	if(!empty($ta_xml['DataTable']['order_fields'])) {
		
		$dg->setOrder($dg->parseOrderString($ta_xml['DataTable']['order_fields']));
	}
	else {
		
		//$dg->DataSource->OrderFields=array(Control_Utils::getGetString($a,",",'',ENC_NONE,' '));
	
	}
}

$dg->createFromArray($ta_xml,true);

if(isset($filter_session_name)&&!empty($filter_session_name)) {
	if(isset($_POST[$unique_id]['neworder'])) {
		$_SESSION[$filter_session_name."_new_order"]=$_POST[$unique_id]['neworder'];
	}
	
	if(isset($_POST['search'])||isset($_POST['btClear'])) {
		$_POST['_c_page'.$pagebar_id]=0;
	}
	
	if(isset($_POST['_c_page'.$pagebar_id])) {
		$_SESSION[$filter_session_name."_pb"]=$_POST['_c_page'.$pagebar_id];
	}
	
	if(isset($_SESSION[$filter_session_name."_pb"])) {
		$_POST['_c_page'.$pagebar_id]=$_SESSION[$filter_session_name."_pb"];
	}
}

//if(isset($_POST['btClear'])) {
//	$_SESSION[$filter_session_name."_new_order"]="";
//	unset($_SESSION[$filter_session_name."_new_order"]);	
//}


if(isset($filter_session_name)&&!empty($filter_session_name)&&isset($_SESSION[$filter_session_name."_new_order"])&&!empty($_SESSION[$filter_session_name."_new_order"])) {
	
	$a=$dg->readOrder($_SESSION[$filter_session_name."_new_order"]);
	
	$dg->DataSource->OrderFields=array($dg->getOrderString());
	
}


$c=new CPageBar($pagebar_id,$dg->DataSource->getCount(),$pagesize);
//$c->_setAttribute('class','page_bar');

$dg->setCurrentPage($c->getCurrentPage());
$dg->old_page=$c->old_page;

$dg->DataSource->Limit="limit ".$dg->getPageSize()*$dg->getCurrentPage().','.$dg->getPageSize();
/* @var $dg->DataSource DataTable*/
$templateParams=isset($__templateParams)&&is_array($__templateParams)?$__templateParams:array();
if (isset($_POST['search'])||$_POST['use_search']==1) {
	
//	echo ControlValues::getSearchString($search,$_POST,array(),$templateParams);
//	echo "<br />";

	if(!empty($search)) {
		$dg->DataSource->AddWhere(ControlValues::getSearchString($search,$_POST,array(),$templateParams));	
		$_POST['use_search']=1;
	}
}

$c->totalItems=$dg->DataSource->getCount();
$c->alterPageCount();


/*  PATCH za AJAX */

$app_name=urlencode($ta_xml['app']);
if((int)$ta_xml['use_ajax']) {
	$be=BE_DIR;
echo <<<EOD
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
	var d=document.getElementById('dv_{$unique_id}');
	d.innerHTML="<div style='color:#993300;font-weight:bold;text-align:center;padding-top:15px;'><img align='absmiddle' src='{$be}i/loader.gif' />Loading ...</div>";
}

function testfun(request) {
	var d=document.getElementById('dv_{$unique_id}');
	//var d=document.getElementById('{$unique_id}');
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

$c->custom_postback="myRender";	//definirana v /common/index.php
//define($c->control_id.'script','1');
}
/*  */

?>