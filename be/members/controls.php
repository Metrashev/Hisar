<?php
function getmembersControls($type='edit') {
	$con=array(

'controls'=>array(
'first_name'=>array(
	'control'=>array("Label"=>"First name","name"=>"in_data[first_name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"first_name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),

'mid_name'=>array(
	'control'=>array("Label"=>"Middle name","name"=>"in_data[mid_name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"mid_name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),

'last_name'=>array(
	'control'=>array("Label"=>"Last name","name"=>"in_data[last_name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"last_name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
),

'city'=>array(
	'control'=>array("Label"=>"City","name"=>"in_data[city]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"city","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'zip'=>array(
	'control'=>array("Label"=>"Zip","name"=>"in_data[zip]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"zip","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'address'=>array(
	'control'=>array("Label"=>"Address","name"=>"in_data[address]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"address","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'country'=>array(
	'control'=>array("Label"=>"Country","name"=>"in_data[country]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"country","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'home_phone'=>array(
	'control'=>array("Label"=>"Home phone","name"=>"in_data[home_phone]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"home_phone","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'mobile_phone'=>array(
	'control'=>array("Label"=>"Mobile phone","name"=>"in_data[mobile_phone]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"mobile_phone","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'work_phone'=>array(
	'control'=>array("Label"=>"Work phone","name"=>"in_data[work_phone]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"work_phone","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'email'=>array(
	'control'=>array("Label"=>"Email","name"=>"in_data[email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"members.email","matchAllValue"=>"","cond"=>"like"    ),
),

'username'=>array(
	'control'=>array("Label"=>"Username","name"=>"in_data[username]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"username","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'userpass'=>array(
	'control'=>array("Label"=>"Userpass","name"=>"in_data[userpass]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"userpass","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'is_active'=>array(
	'control'=>array("Label"=>"Is active","name"=>"in_data[is_active]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"is_active","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),
'is_active_search'=>array(
	'control'=>array("Label"=>"Is active","name"=>"in_data[is_active_search]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"is_active","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>array(-1=>"",0=>"No",1=>"Yes"),))),
	'search_data'=>array("search_name"=>"members.is_active","matchAllValue"=>-1,"cond"=>"="    ),
),

'send_extra_info'=>array(
	'control'=>array("Label"=>"Send extra info","name"=>"in_data[send_extra_info]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"send_extra_info","userFunc"=>"","FormatString"=>""),
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