<?php
function getmail_headsControls($type='edit') {
	$con=array(

'controls'=>array(
'name'=>array(
	'control'=>array("Label"=>"Име","name"=>"in_data[name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"name","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_heads.name","matchAllValue"=>"","cond"=>"like"    ),
),


'is_approved'=>array(
	'control'=>array("Label"=>"Approved","name"=>"in_data[is_approved]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"is_approved","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),	
),
'is_approved_s'=>array(
	'control'=>array("Label"=>"Approved","name"=>"in_data[is_approved_s]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"is_approved_s","userFunc"=>"","FormatString"=>"",'autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>array(-1=>"")+$GLOBALS['YES_NO'],))),	
	'search_data'=>array("search_name"=>"mq_mail_heads.is_approved","matchAllValue"=>-1,"cond"=>"="    ),
),


'start_date'=>array(
	'control'=>array("Label"=>"Sending initiation","name"=>"in_data[start_date]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"start_date","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M:%S"),
	'write_data'=>array("type"=>DATA_DATETIME,"required"=>true,),
	'search_data'=>array("search_name"=>"mq_mail_heads.start_date","matchAllValue"=>"","cond"=>"between"    ,"right_field"=>"_to_start_date"),
),
'_to_start_date'=>array(
	'control'=>array("Label"=>"start date","name"=>"in_data[_to_start_date]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"_to_start_date","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M:%s",'states'=>array('on'=>1,'off'=>0)),
),

'delete_after_sent'=>array(
	'control'=>array("Label"=>"Delete after sending","name"=>"in_data[delete_after_sent]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"delete_after_sent","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
	'search_data'=>array("search_name"=>"mq_mail_heads.delete_after_sent","matchAllValue"=>"","cond"=>"="    ),
),
'delete_after_sent_s'=>array(
	'control'=>array("Label"=>"Delete after sending","name"=>"in_data[delete_after_sent_s]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"delete_after_sent_s","userFunc"=>"","FormatString"=>"",'autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>array(-1=>"")+$GLOBALS['YES_NO'],))),
	'search_data'=>array("search_name"=>"mq_mail_heads.delete_after_sent","matchAllValue"=>-1,"cond"=>"="    ),
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