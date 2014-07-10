<?php
function getmail_configControls($type='edit') {
	$con=array(

'controls'=>array(
'emails_per_hour'=>array(
	'control'=>array("Label"=>"Quota per hour","name"=>"in_data[emails_per_hour]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"emails_per_hour","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
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