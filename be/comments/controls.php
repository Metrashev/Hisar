<?php
function getcommentsControls($type='edit') {
	$con=array(

'controls'=>array(
'name'=>array(
	'control'=>array("Label"=>"Name","name"=>"in_data[name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'email'=>array(
	'control'=>array("Label"=>"Email","name"=>"in_data[email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'phone'=>array(
	'control'=>array("Label"=>"Phone","name"=>"in_data[phone]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"phone","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'address'=>array(
	'control'=>array("Label"=>"Address","name"=>"in_data[address]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"address","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'subject'=>array(
	'control'=>array("Label"=>"Subject","name"=>"in_data[subject]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"subject","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'comment'=>array(
	'control'=>array("Label"=>"Comment","name"=>"in_data[comment]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"comment","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'is_visible'=>array(
	'control'=>array("Label"=>"Is visible","name"=>"in_data[is_visible]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"is_visible","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),
)
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