<?php
function getuser_group_rightsControls($type='edit') {
	
	
	$cat=array();
	if($type=="edit") {
		$c=getdb()->getassoc("select id,value,level from categories where pid!=0 order by l");
		foreach ($c as $k=>$v) {
			if((int)$v['level']>1) {
				$str=str_repeat("&nbsp;",$v['level']-1);
				$v['value']=$str.$v['value'];
			}
			
			$cat[$k]=$v['value']."({$v['id']})";			
		}
		
		
	}
	else {
		$cat=getdb()->getassoc("select id,value from categories where pid!=0 order by l");
	}
	
	$con=array(

'controls'=>array(
'name'=>array(
	'control'=>array("Label"=>"User group","name"=>"in_data[name]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"name","userFunc"=>"","FormatString"=>"",),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>true,),
	'search_data'=>array("search_name"=>"user_group_rights.name","matchAllValue"=>"","cond"=>"like"    ),
),

'resources'=>array(
	'control'=>array("Label"=>"Resources","name"=>"in_data[resources][]","isHTML"=>false,"tagName"=>"MultiSelect","bound_field"=>"resources","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$GLOBALS['resources_array'],))),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'cids'=>array(
	'control'=>array("Label"=>"Cids","name"=>"in_data[cids][]","isHTML"=>false,"tagName"=>"MultiSelect","bound_field"=>"cids","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$cat,))),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),
/*
'attribute_cluster_ids'=>array(
	'control'=>array("Label"=>"Product groups","name"=>"in_data[attribute_cluster_ids][]","isHTML"=>false,"tagName"=>"MultiSelect","bound_field"=>"attribute_cluster_ids","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"sql","value"=>array("DataSource"=>"select id,name from attribute_clusters order by name",))),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),
*/
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