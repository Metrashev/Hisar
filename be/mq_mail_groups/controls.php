<?php
function getmq_mail_groupsControls($type='edit') {
	
	if(!is_array($GLOBALS['email_fields'])) {
		$GLOBALS['email_fields']=array();
	}
	$con=array(

'controls'=>array(
'name'=>array(
	'control'=>array("Label"=>"Name","name"=>"in_data[name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_groups.name","matchAllValue"=>"","cond"=>"like"    ),
),
'email_column'=>array(
	'control'=>array("Label"=>"Email column","name"=>"in_data[email_column]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"email_column","userFunc"=>"","FormatString"=>"",'autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>array(""=>"")+$GLOBALS['email_fields'],))),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,'invalid_values'=>array("")),
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