<?php
function getmail_itemsControls($type='edit') {
	$con=array(

'controls'=>array(
'subject'=>array(
	'control'=>array("Label"=>"Subject","name"=>"in_data[subject]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"subject","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_items.subject","matchAllValue"=>"","cond"=>"like"    ),
),

'from_email'=>array(
	'control'=>array("Label"=>"From email","name"=>"in_data[from_email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"from_email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_items.from_email","matchAllValue"=>"","cond"=>"like"    ),
),

'to_email'=>array(
	'control'=>array("Label"=>"To email","name"=>"in_data[to_email]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"to_email","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_items.to_email","matchAllValue"=>"","cond"=>"like"    ),
),

'cc'=>array(
	'control'=>array("Label"=>"Cc","name"=>"in_data[cc]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"cc","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'bcc'=>array(
	'control'=>array("Label"=>"Bcc","name"=>"in_data[bcc]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"bcc","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'mail_body'=>array(
	'control'=>array("Label"=>"Mail body","name"=>"in_data[mail_body]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"mail_body","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),
'status_id'=>array(
	'control'=>array("Label"=>"Status","name"=>"in_data[status_id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"status_id","userFunc"=>"","FormatString"=>"",'autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>array(-1=>"")+$GLOBALS['mq_mail_status_array'],))),
	'search_data'=>array("search_name"=>"mq_mail_items.status_id","matchAllValue"=>-1,"cond"=>"="    ),
),


'date_to_send'=>array(
	'control'=>array("Label"=>"Date to send","name"=>"in_data[date_to_send]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"date_to_send","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M:%s"),
	'write_data'=>array("type"=>DATA_DATETIME,"required"=>false,),
	'search_data'=>array("search_name"=>"mq_mail_items.date_to_send","matchAllValue"=>"","cond"=>"between"    ,"right_field"=>"_to_date_to_send"),
),
'_to_date_to_send'=>array(
	'control'=>array("Label"=>"date to send","name"=>"in_data[_to_date_to_send]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"_to_date_to_send","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M:%s",'states'=>array('on'=>1,'off'=>0)),
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