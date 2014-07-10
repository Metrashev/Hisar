<?php
function getpollsControls($type='edit') {
	$con=array(

'controls'=>array(
'question'=>array(
	'control'=>array("Label"=>"Question","name"=>"in_data[question]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"question","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'active_from_date'=>array(
	'control'=>array("Label"=>"Active от","name"=>"in_data[active_from_date]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"active_from_date","userFunc"=>"","FormatString"=>"%d/%m/%Y"),
	'write_data'=>array("type"=>DATA_DATE,"required"=>false,),
),

'active_to_date'=>array(
	'control'=>array("Label"=>"to","name"=>"in_data[active_to_date]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"active_to_date","userFunc"=>"","FormatString"=>"%d/%m/%Y"),
	'write_data'=>array("type"=>DATA_DATE,"required"=>false,),
),

'position'=>array(
	'control'=>array("Label"=>"Possition","name"=>"in_data[position]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"position","userFunc"=>"","FormatString"=>"",'autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>array(1=>1,2=>2),'addzero'=>array('key'=>0,'value'=>'')))),
	'write_data'=>array("type"=>DATA_SMALLINT,"required"=>false,),
),

'visible'=>array(
	'control'=>array("Label"=>"Visible","name"=>"in_data[visible]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"visible","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),
'keywords'=>array(
	'control'=>array('Label'=>'Keywords','name'=>'in_data[keywords]','tagName'=>'Input','bound_field'=>'keywords','userFunc'=>'','FormatString'=>'',),
	'search_data'=>array('search_name'=>'polls.question','cond'=>'keywords','matchAllValue'=>'')
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