<?php
function getusersControls($type='edit') {
	$con=array(

'controls'=>array(
'name'=>array(
	'control'=>array("Label"=>"Име","name"=>"in_data[name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
	'search_data'=>array("search_name"=>"users.name","matchAllValue"=>"","cond"=>"like"    ),
),
'email'=>array(
	'control'=>array("Label"=>"email","name"=>"in_data[email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
	'search_data'=>array("search_name"=>"users.email","matchAllValue"=>"","cond"=>"like"    ),
),
'username'=>array(
	'control'=>array("Label"=>"User name","name"=>"in_data[username]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"username","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),

'userpass'=>array(
	'control'=>array("Label"=>"Password","name"=>"in_data[userpass]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"userpass","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),

'user_rights_id'=>array(
	'control'=>array("Label"=>"Status","name"=>"in_data[user_rights_id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"user_rights_id","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"sql","value"=>array("DataSource"=>"select id,name from user_group_rights","addzero"=>array("key"=>0,"value"=>"","position"=>"top")))),
	'write_data'=>array("type"=>DATA_INT,"required"=>true,'invalid_values'=>array(0)),
	'search_data'=>array("search_name"=>"users.user_rights_id","matchAllValue"=>"","cond"=>"="    ),
),

'is_active'=>array(
	'control'=>array("Label"=>"Active","name"=>"in_data[is_active]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"is_active","userFunc"=>"","FormatString"=>""),
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