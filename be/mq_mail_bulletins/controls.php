<?php
function getmq_mail_bulletinsControls($type='edit') {
	$con=array(

'controls'=>array(
'subject'=>array(
	'control'=>array("Label"=>"Subject","name"=>"in_data[subject]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"subject","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_bulletins.subject","matchAllValue"=>"","cond"=>"like"    ),
),

'from_email'=>array(
	'control'=>array("Label"=>"From email","name"=>"in_data[from_email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"from_email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_bulletins.from_email","matchAllValue"=>"","cond"=>"like"    ),
),

'body'=>array(
	'control'=>array("Label"=>"Текст","name"=>"in_data[body]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"body","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'date_to_send'=>array(
	'control'=>array("Label"=>"Initination Date","name"=>"in_data[date_to_send]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"date_to_send","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M:%S"),
	'write_data'=>array("type"=>DATA_DATETIME,"required"=>false,),
),

'mail_group_id'=>array(
	'control'=>array("Label"=>"Списък с e-mail-и","name"=>"in_data[mail_group_id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"mail_group_id","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"sql","value"=>array("DataSource"=>"select id,name from mq_mail_groups order by name","addzero"=>array("key"=>0,"value"=>"","position"=>"top")))),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
	'search_data'=>array("search_name"=>"mq_mail_bulletins.mail_group_id","matchAllValue"=>0,"cond"=>"="    ),
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