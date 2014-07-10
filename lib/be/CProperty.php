<?php

class CProperty {
	public $id;
	
	function __construct($id) {
		$this->id=$id;
	}
	
	function controlTemplates($attribute,$use_to=false) {
		if(!((int)$attribute['type'])) {
			return array();
		}
		$label=$attribute["name"];
		$select=array(
			'control'=>array("Label"=>"{$label}","name"=>"attributes[{$attribute['id']}]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"{$attribute['id']}","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>array(-1=>"")+explode(",",$attribute['values'])))),
			'write_data'=>array("type"=>DATA_VARCHAR),
			'search_data'=>array("search_name"=>"id","matchAllValue"=>-1,"cond"=>"="),
		);
		$text=array(
			'control'=>array("Label"=>"{$label}","name"=>"attributes[{$attribute['id']}]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"{$attribute['id']}","userFunc"=>"","FormatString"=>"",),
			'write_data'=>array("type"=>DATA_VARCHAR),
			'search_data'=>array("search_name"=>"id","matchAllValue"=>"","cond"=>"like"),
		);
		$number=array(
			'control'=>array("Label"=>"{$label}","name"=>"attributes[{$attribute['id']}]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"{$attribute['id']}","userFunc"=>"","FormatString"=>"",),
			'write_data'=>array("type"=>DATA_FLOAT),
			'search_data'=>array("search_name"=>"id","matchAllValue"=>"","cond"=>"between","right_field"=>"_to_{{$attribute['id']}}"),
		);
		$_to_number=array(
			'control'=>array("Label"=>"-","name"=>"attributes[_to_{$attribute['id']}","isHTML"=>false,"tagName"=>"Input","bound_field"=>"_to_{$attribute['id']}","userFunc"=>"","FormatString"=>"",),
		);
		$checkbox=array(
			'control'=>array("Label"=>"{$label}","name"=>"attributes[{$attribute['id']}]","states"=>array("on"=>1,"off"=>0),"isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"{$attribute['id']}","userFunc"=>"","FormatString"=>""),
			'write_data'=>array("type"=>DATA_TINYINT,),
			'search_data'=>array("search_name"=>"id","matchAllValue"=>"","cond"=>"="),
		);
		$date=array(
				'control'=>array('Label'=>"{$label}",'name'=>"attributes[{$attribute['id']}]",'tagName'=>'DateControl','bound_field'=>"{$attribute['id']}",'FormatString'=>'%d/%m/%Y'),
				'write_data'=>array('type'=>DATA_DATE,),
				'search_data'=>array('search_name'=>'id','matchAllValue'=>'','cond'=>'between','right_field'=>"_to_{$attribute['id']}",),
		);
		$_to_date=array(
	            'control'=>array('Label'=>"-",'name'=>"attributes[_to_{$attribute['id']}]",'tagName'=>'DateControl','bound_field'=>"_to_{$attribute['id']}",'FormatString'=>'%d/%m/%Y','attributes'=>array('name'=>"_to_{$attribute['id']}")),
	     );
	     $list=array(
			'control'=>array("Label"=>"{$label}","name"=>"attributes[{$attribute['id']}]","isHTML"=>false,"tagName"=>"GroupCheckBox","bound_field"=>"{$attribute['id']}","userFunc"=>"","FormatString"=>"","autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>explode(",",$attribute['values']),"columns"=>2))),
			'write_data'=>array("type"=>DATA_VARCHAR),
			'search_data'=>array("search_name"=>"id","matchAllValue"=>-1,"cond"=>"="),
		);
		$templates=array(
			1=>$checkbox,
			2=>$text,
			3=>$number,
			4=>$date,
			5=>$select,
			6=>$list
		);
		
		return array($attribute['id']=>$templates[$attribute['type']]);
	}
	
	function getSimpleTemplate($title="",$columns=2,$type="edit") {
		return array(
		'template_title'=>$title,	
		'template_colums'=>$columns,	
		'template_type'=>$type,
		'buttons'=>"",
		);
	}
	
	function prepareAttributesToWrite($attributes) {
		if(!is_array($attributes)) {
			return array();
		}
		$db=getdb();
		$att=$db->getassoc("select id,type from attributes");
		$to_write=array();
		$to_del=array();
		foreach ($attributes as $k=>$v) {
			switch ((int)$att[$k]) {
				case 1:	{	//checkbox
					if((int)$v) {
						$to_write[$k]=$v;
					}
					else {
						$to_del[$k]=$k;
					}
					break;
				}
				case 2: {	//text
					$v=(string)$v;
					if($v!="") {
						$v=trim($v);
					}
					if($v!="") {
						$to_write[$k]=$v;
					}
					else {
						$to_del[$k]=$k;
					}
					break;
				}
				case 3: {	//number
					if((string)$v==""||!is_numeric($v)) {
						$to_del[$k]=$k;
					}
					else {
						$to_write[$k]=(float)$v;						
					}
					break;
				}
				case 4: {	//date
					if($v=="0000-00-00"||empty($v)) {
						$to_del[$k]==$k;
					}
					else {
						$to_write[$k]=$v;
					}
					break;
				}
				case 5: {	//DropDown
					if($v==-1) {
						$to_del[$k]==$k;
					}
					else {
						$to_write[$k]=$v;
					}
					break;
				}
				case 6: {	//list
					
					if("$v"=="") {
						$to_del[$k]==$k;
					}
					else {
						$to_write[$k]=$v;
					}
					break;
				}
			}
		}
		
		return array('w'=>$to_write,'d'=>$to_del);
	}
	
	function getEmptyArray() {
		$arr=array("controls"=>array());
		return $arr+$this->getSimpleTemplate();
	}
	
	function getAttributeIds() {
		$db=getdb();
		$property_type=(int)$db->getone("select group_id from properties where id=?",array($this->id));
		if(empty($property_type)) {
			return "0";
		}
		$attributes=$db->getrow("select parent_attributes,own_attributes from property_groups where id=?",array($property_type));
		if(empty($attributes)) {
			return "0";
		}
		$pa=$attributes['parent_attributes'];
		$oa=$attributes['own_attributes'];
		if(empty($pa)) {
			$pa="0";
		}
		if(empty($oa)) {
			$oa="0";
		}
		return $pa.','.$oa;
	}
	
	function renderAttributesForEdit($columns=2) {
		$db=getdb();
		$a_ids=$this->getAttributeIds();
		if((int)$columns<1) {
			$columns=2;
		}
		$attributes=$db->getassoc("select distinct * from attributes where id in ({$a_ids}) order by type");
		if(empty($attributes)) {
			return $this->getEmptyArray();
		}
		
		$controls=array();
		$old_type=0;
		foreach ($attributes as $k=>$v) {
			if(in_array((int)$v['type'],array(2,3,4,5))) {
				$tp=2;
			}
			else {
				$tp=(int)$v['type'];
			}
			if($old_type==0) {
				$old_type=$tp;
			}
			if($old_type!=$tp) {
				if(count($controls)%$columns) {
					$i=0;
					while(count($controls)%$columns&&$i<10) {
						$controls+=array("dc{$i}".count($controls)=>array());
						$i++;
					}
				}
				$old_type=$tp;
			}
			$controls+=$this->controlTemplates($v);
		}
		
		return array('controls'=>$controls)+$this->getSimpleTemplate("Attributes");		
	}
	
}

?>