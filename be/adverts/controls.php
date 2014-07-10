<?php
// ALTER TABLE `adverts` ADD `ad_text` TEXT NOT NULL AFTER `ad_file` ;

function getadvertsControls($type='edit') {
	$con=array(

'controls'=>array(
'id'=>array(
	'control'=>array("Label"=>"id","name"=>"in_data[id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"id","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.id","matchAllValue"=>"","cond"=>"="    ),
),

'advertiser'=>array(
	'control'=>array("Label"=>"advertiser","name"=>"in_data[advertiser]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"advertiser","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.advertiser","matchAllValue"=>"","cond"=>"like"    ),
),

'active_from_date'=>array(
	'control'=>array("Label"=>"active from date","name"=>"in_data[active_from_date]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"active_from_date","userFunc"=>"","FormatString"=>"%d/%m/%Y"),
	'write_data'=>array("type"=>DATA_DATE,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.active_from_date","matchAllValue"=>"","cond"=>"between"    ),
),

'active_to_date'=>array(
	'control'=>array("Label"=>"active to date","name"=>"in_data[active_to_date]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"active_to_date","userFunc"=>"","FormatString"=>"%d/%m/%Y"),
	'write_data'=>array("type"=>DATA_DATE,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.active_to_date","matchAllValue"=>"","cond"=>"between"    ),
),

'position_id'=>array(
	'control'=>array("Label"=>"position id","name"=>"in_data[position_id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"position_id","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$GLOBALS['ADVERT_POSITIONS'],))),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.position_id","matchAllValue"=>0,"cond"=>"="    ),
),

'ad_type_id'=>array(
	'control'=>array("Label"=>"ad type id","name"=>"in_data[ad_type_id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"ad_type_id","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$GLOBALS['AdsTypes'],))),
	'write_data'=>array("type"=>DATA_TINYINT,"required"=>false,),
),

'ad_image'=>array(
	'control'=>array("Label"=>"ad image","name"=>"in_data[ad_image]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"ad_image","userFunc"=>"","FormatString"=>"",
	"parameters"=>array("table"=>"adverts","field"=>"ad_image","id"=>$_GET['id'],"dir"=>$GLOBALS['MANAGED_FILE_DIR'],"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],'resize'=>true,'overwrite'=>false,
		'sizes'=>array('img'=>array(100,100)
))),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'ad_link'=>array(
	'control'=>array("Label"=>"ad link","name"=>"in_data[ad_link]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"ad_link","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'target'=>array(
	'control'=>array("Label"=>"target","name"=>"in_data[target]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"target","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>array(""=>"","_blank"=>"New Window","_self"=>"Same Window"),))),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'ad_file'=>array(
	'control'=>array("Label"=>"ad file","name"=>"in_data[ad_file]","isHTML"=>false,"tagName"=>"ManagedFile","bound_field"=>"ad_file","userFunc"=>"","FormatString"=>"","parameters"=>array("table"=>"adverts","field"=>"ad_file","id"=>$_GET['id'],"dir"=>$GLOBALS['MANAGED_FILE_DIR'],"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'])),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'ad_text'=>array(
	'control'=>array("Label"=>"ad link","name"=>"in_data[ad_text]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"ad_text","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'num_views'=>array(
	'control'=>array("Label"=>"num views","name"=>"in_data[num_views]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"num_views","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.num_views","matchAllValue"=>"","cond"=>"="    ),
),

'num_clicks'=>array(
	'control'=>array("Label"=>"num clicks","name"=>"in_data[num_clicks]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"num_clicks","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
	'search_data'=>array("search_name"=>"adverts.num_clicks","matchAllValue"=>"","cond"=>"="    ),
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