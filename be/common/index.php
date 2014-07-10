<?php

$h=FE_Utils::getSeparator();
//$post=$_POST;
/*if(isset($_SESSION[$_SERVER['REQUEST_URI'].'_filter'])&&!isset($_POST['btClear'])&&!isset($_POST['search'])) {
	$_POST['in_data']=$_SESSION[$_SERVER['REQUEST_URI'].'_filter'];
	$_POST['use_search']=1;
}


if (isset($_POST['search'])||$_POST['use_search']==1) {
	$_POST['use_search']=1;
	$_SESSION[$_SERVER['REQUEST_URI'].'_filter']=$_POST['in_data'];
}
*/

$real_request_method=$_SERVER['REQUEST_METHOD'];

if(!$__search_loaded) {
	include(dirname(__FILE__)."/search_load.php");
	$__search_loaded=0;	//podgotwq se eventualno za nov load
}


$errors=array();


if(!isset($__del_var)) {
	$__del_var="hdDelete";
}


if (!empty($_POST[$__del_var])) {
	
	if (function_exists($fn_Delete)) {

		$a=$fn_Delete($_POST[$__del_var]);
		if (is_array($a)&&count($a)>0) {
			$errors=$a;
		}
	}
	else {
		
		if(!empty($__editTable)) {
			$db=getdb();
			$db->execute("delete from `{$__editTable}` where id='{$_POST[$__del_var]}'");
		}
	}
}





require_once(dirname(__FILE__).'/template_index.php');

if(isset($__template_index)) {
	$template_index=$__template_index;	
}
else {
	$template_index=new IndexTemplate($id);	
}

if(isset($__del_var)) {
	if(!isset($template_index->hidden[$__del_var])) {
		$template_index->hidden[$__del_var]='<input type="hidden" name="'.$__del_var.'" id="'.$__del_var.'" value="0"/>';
	}
}

$__template_index->hidden['use_search']="<input type='hidden' name='use_search' id='use_search' value='{$_POST['use_search']}'/>";


/* @var $template_index IndexTemplate*/

//$template_index->removeScript('Calendar');

$template_index->pref_table_text=FE_Utils::renderErrors($errors);
echo $template_index->openTemplate();	//izpisva <html><head><meta><scripts><css></head><body><form><hidden><table><tr><td>
if(isset($template_index->hidden[$__del_var])) {
	unset($template_index->hidden[$__del_var]);	//podgotvq se za sledva6t tab
}
unset($__del_var);
?>


<?php
if(!isset($pagesize)) {
	$pagesize=25;
}
if(!isset($pagebar_id)) {
	$pagebar_id='pb';
}
if(!isset($unique_id)) {
	$unique_id='dg';
}

if($real_request_method=="GET") {
	
//	if(isset($_GET['search'])) {	
//		CRelations::loadRelationsForSelect($_GET,$unique_id);
//	}
}

include(dirname(__FILE__).'/datagrid.php');


//$p=new Page();

/*
$dg=new DataGridNew($unique_id,$ta_xml['template'],$pagesize,$_POST);

$dg->createFromArray($ta_xml,true);
*/
if(isset($_POST['btSelect'])) {
	$selected_set=$dg->getControls();
	if(is_array($selected_set['_ch_sel_'])&&!empty($selected_set['_ch_sel_'])) {
		$keys=implode(',',array_keys($selected_set['_ch_sel_']));
		header("Location: ".$_GET['bkp']."&return_point={$_GET['return_point']}&selected_keys=".$keys);
		return;
	}
	
	/*
	if(is_array($selected_set['_ch_sel_'])&&!empty($selected_set['_ch_sel_'])) {
		$_nr=CRelations::processRelationSelect($_GET,array_keys($selected_set['_ch_sel_']));
		$bkp=isset($_GET['bkp'])?($_GET['bkp']):"";
		if(is_array($_nr)) {
			
				echo <<<EOD
<script>
	if(!window.confirm('Възникна гршка при запис на някоя от релациите! Вериятна причина е невалидно ИД или гршно параметри от GET! Натиснете ОК, за да останите в избор или Cancel, за да се върнете на предишната форма!')) {
		window.open("{$bkp}","_self");
	}
</script>
EOD;
		}
		else {
			if($_nr) {
				header("Location: {$bkp}");
				exit;
			}
			else {
				echo <<<EOD
<script>
	if(!window.confirm('Не са създадени нови записи! Всички избрани релации вече съществуват! Натиснете ОК, за да останите в избор или Cancel, за да се върнете на предишната форма!')) {
		window.open("{$bkp}","_self");
	}
</script>
EOD;
			}
		}
	}
	*/
}

/*

$c=new CPageBar($pagebar_id,$dg->DataSource->getCount(),$pagesize);
//$c->_setAttribute('class','page_bar');

$dg->setCurrentPage($c->getCurrentPage());
$dg->old_page=$c->old_page;

$dg->DataSource->Limit="limit ".$dg->getPageSize()*$dg->getCurrentPage().','.$dg->getPageSize();

$templateParams=isset($__templateParams)&&is_array($__templateParams)?$__templateParams:array();
if (isset($_POST['search'])||$_POST['use_search']==1) {
	
//	echo ControlValues::getSearchString($search,$_POST,array(),$templateParams);
//	echo "<br />";
	$dg->DataSource->AddWhere(ControlValues::getSearchString($search,$_POST,array(),$templateParams));	
	$_POST['use_search']=1;
}
*/

//FE_Utils::setDGAttributes($dg);

/*$dg->_setAttribute('width','700');
$dg->_setAttribute('style','border:1px solid #993300');
$dg->_setAttribute('class','test1');
$dg->_setAttribute('cellspacing','0');
$dg->_setAttribute('cellpadding','0');*/

if($_SERVER['REQUEST_METHOD']=='GET') {
	//$dg->setOrder(array("booking.id DESC"));
}

//MasterForm::create($search,null,$p,$_POST,$templateParams,JS_VALIDATION);

/*
$c->totalItems=$dg->DataSource->getCount();
$c->alterPageCount();

*/

//echo $p->render();

if($_GET['cid']>0){
$t=new CURLTree("categories");
echo<<<EOD
<h3 align="center">{$t->get_node_path($_GET['cid'])}</h3>
EOD;
}

if(!empty($search)) {
	
	echo Master::create($search,$search['template']['dir'],$_POST,null,false);
}

$btn_select="";
if(isset($_GET['search'])) {
	$btn_select= <<<EOD
	<div width="100%" align="left" style="padding:5px 0px;"><input type="submit" class="submit" value="Select" name="btSelect" /></div>
EOD;
	echo $btn_select;
}

echo $h;
if(isset($__grid_container)) {
	echo $__grid_container['start'];
}

echo "<div id='dv_{$unique_id}'>";
echo $c->render();
echo $h;

$dg->DataSource->BuildQuery();
echo $dg->render();

echo $h;
echo $c->render();
echo "</div>";
if(isset($__grid_container)) {
	echo $__grid_container['end'];
}
echo $btn_select;
echo $template_index->closeTemplate();	//izpisva </td></tr></table></form></body></html>

?>