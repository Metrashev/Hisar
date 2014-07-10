<?php
function getControls($type='edit') {

	$sizes=array(
		'pic'=>array(95,65,"size=95x65"),
		
	);
	$con=array(

'controls'=>array(
'title'=>array(
	'control'=>array("Label"=>"title","name"=>"in_data[title]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"title","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),
'subtitle'=>array(
	'control'=>array("Label"=>"short description","name"=>"in_data[subtitle]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"subtitle","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),
'picture'=>array(
	'control'=>array("Label"=>"picture","name"=>"in_data[picture]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"picture","userFunc"=>"","FormatString"=>"","parameters"=>array("table"=>"news_pages","field"=>"picture","id"=>$_GET['id'],"dir"=>$GLOBALS['MANAGED_FILE_DIR'],"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],'resize'=>true,'overwrite'=>false,
'sizes'=>$sizes
)),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),
'due_date'=>array(
	'control'=>array("Label"=>"due date","name"=>"in_data[due_date]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"due_date","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M"),
	'write_data'=>array("type"=>DATA_DATETIME,"required"=>true,),
	'search_data'=>array("search_name"=>"news_pages.due_date","matchAllValue"=>"","cond"=>"between"    ,"right_field"=>"_to_due_date"),
),
'_to_due_date'=>array(
	'control'=>array("Label"=>"due date","name"=>"_to_in_data[due_date]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"_to_due_date","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M",'states'=>array('on'=>1,'off'=>0)),
),
'is_visible'=>array(
	'control'=>array("Label"=>"visible","name"=>"in_data[is_visible]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"is_visible","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),
'body'=>array(
	'control'=>array("Label"=>"body","name"=>"in_data[body]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"body","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),
'keywords'=>array(
	'control'=>array('Label'=>'Keywords','name'=>'in_data[keywords]','tagName'=>'Input','bound_field'=>'keywords','userFunc'=>'','FormatString'=>'',),
	'search_data'=>array('search_name'=>'news_pages.title,news_pages.subtitle,news_pages.body','cond'=>'keywords','matchAllValue'=>'')
),)
);

if($type=='search') {
    	$con['template']=array('dir'=>dirname(__FILE__).'/search.tpl');
    }
else {
	$con['template']=array('dir'=>dirname(__FILE__).'/edit.tpl');
}
    return $con;
}

?>