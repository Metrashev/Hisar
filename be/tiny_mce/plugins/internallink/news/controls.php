<?php

class CStPage {
	function getCategories() {
		
		$db=getdb();
		$rows=$db->getAssoc("select 
		categories.id,categories.level,static_pages.def as def,static_pages.title as st,static_pages.id as spid,concat(categories.id,'_',ifnull(static_pages.id,0)) as bf, categories.value,categories.id as cid
		 from 
		categories left outer join static_pages on categories.id=static_pages.cid 
		order by categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)");
		$result=array();
		$result[0]='';
		foreach ($rows as $k=>$v) {
			if($v['level']<1)
				continue;
			$result[$k]=str_repeat($space, 2*($v['level']-1)).$v['value'];
			if(intval($v['spid'])>0&&intval($v['def'])==0) {
				$result[$k]=str_repeat($space, 2*$v['level']).$v['st'];
			}
		}
		
		return $result;
	}	
	
	function getLink($params) {
		$cid=$params[0];
		$index=$params[1];
		
		$row=$params[2]->DataSource->Rows[$index];
		
		$spid=!(int)($row['spid'])?"":"&amp;NewsId=".$row['spid'];
		
		if(intval($row['spid'])>0&&intval($row['def'])==0) {
			$title = $row['st'];
		} else {
		  $title = $row['value'];
		}
		$title = str_replace('"', "&amp;quot;", $title);
		
		$onClick = <<<EOD
{$GLOBALS['__selector']} "/?cid={$row['cid']}{$spid}", "$title"); window.close();		
EOD;
		
		//$onClick = str_replace("&", "&amp;", $onClick);
		$onClick = str_replace('"', "&quot;", $onClick);
		return <<<EOD
<a href="#" onclick="$onClick">Select</a>
EOD;
	}
}

if(!function_exists("getControls")) {
	function getControls($type='edit') {
		$con=array(
	
	'controls'=>array(
	'categories'=>array(
		'control'=>array("Label"=>"категории","name"=>"in_data[categories]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"categories","userFunc"=>"","FormatString"=>"",'autoload'=>array("type"=>"user_func",'value'=>array("DataSource"=>array('CStPage',"getCategories"),'addzero'=>array('key'=>0,'value'=>'')))),
		'search_data'=>array("search_name"=>"cid","matchAllValue"=>0,"cond"=>"="),
	),
	
	)
	);
	
	if($type=='search') {
	    	$con['template']=array('dir'=>dirname(__FILE__).'/search.tpl');
	    }	
	 return $con;
	}
}

?>