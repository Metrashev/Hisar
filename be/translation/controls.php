<?php


function gettranslationControls($type='edit') {


$tmp = array();

//$tmp['']='';

$tmp['0'] = 'General';

$GLOBALS['TranslationCategories'] = $tmp+getdb()->getAssoc("SELECT id, concat(repeat('&nbsp;', (level-1)*2), value) as value FROM categories WHERE id>1 ORDER BY l");


	$con=array(

'controls'=>array(
'cid'=>array(
	'control'=>array("Label"=>"Cid","name"=>"in_data[cid]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"cid","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$GLOBALS['TranslationCategories'],))),
	'write_data'=>array("type"=>DATA_INT,"required"=>true,"invalid_value"=>""),
	'search_data'=>array("search_name"=>"translation.cid","matchAllValue"=>"","cond"=>"="    ),
),

'translation_key'=>array(
	'control'=>array("Label"=>"Translation key","name"=>"in_data[translation_key]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"translation_key","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
	'search_data'=>array("search_name"=>"translation.translation_key","matchAllValue"=>"","cond"=>"like"    ),
),
'is_html'=>array(
	'control'=>array("Label"=>"HTML","name"=>"in_data[is_html]","isHTML"=>false,"tagName"=>"CheckBox","states"=>array("on"=>1,"off"=>0),"bound_field"=>"is_html","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),

'hints'=>array(
	'control'=>array("Label"=>"Translation key","name"=>"in_data[hints]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"hints","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
	'search_data'=>array("search_name"=>"translation.hints","matchAllValue"=>"","cond"=>"like"    ),
),

)
);

foreach ($GLOBALS['CONFIG']['SiteLanguages'] as $k=>$v){

	$con['controls']['value_'.$k] = array(
		'control'=>array("Label"=>"Value {$v}","name"=>"in_data[value_{$k}]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"value_{$k}","userFunc"=>"","FormatString"=>""),
		'write_data'=>array("type"=>DATA_LONGTEXT,"required"=>false,),
		'search_data'=>array("search_name"=>"translation.value_{$k}","matchAllValue"=>"","cond"=>"like"    ),
	);

	$kwFields[] = "translation.value_{$k}";
}

$kwFields = implode(',', $kwFields);

$con['controls']['keywords']=array(
	'control'=>array('Label'=>'Keywords','name'=>'in_data[keywords]','tagName'=>'Input','bound_field'=>'keywords','userFunc'=>'','FormatString'=>'',),
	'search_data'=>array('search_name'=>$kwFields,'cond'=>'keywords','matchAllValue'=>'')
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