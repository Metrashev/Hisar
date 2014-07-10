<?php

class CAttributes {
	
	static function getProductConstantString() {
		return "_#PRODUCT_ID#_";
	}
	
	static function getMatcharray() {
		return array(
				-2=>"int",
				-1=>"submit",
				1=>"int",
				2=>"dec",
				3=>"string",
				4=>"enum",
				5=>"set",
				6=>"bool",
				7=>"image",
				8=>"file",
				9=>"tinymce",
				10=>"date",
				11=>"datetime",
				12=>"enumnom",
				13=>"setnom",
				14=>"ac",
				15=>"text",
				16=>"enumlist",
				17=>"setlist",
				
		);
	}
	
	static function getAttributeMatchName($attribute_index) {
		$match=CAttributes::getMatcharray();
		return $match[$attribute_index];
	}
	
	static function createAttributeValueId($product_id,$group_id,$attribute_id,$sub_id) {
		return "{$product_id}_{$group_id}_{$attribute_id}_{$sub_id}";
	}
	
	static function getColspanControl($id,$colspan=2) {
		return array(
			"colspan_{$id}"=>array(
				'control'=>array(
					'tagName'=>'colspan','bound_field'=>$colspan
				),
			)
		);
	}
	
	static function prepareOptions($options,$sort=false) {
		if(empty($options)) {
			return array();
		}
		
		$options=explode("\r\n",$options);
		if(!empty($options)) {
			$a=array();
			foreach ($options as $k=>$v) {
				$v=explode("|",$v);
				$a[(int)$v[0]]=$v[1];				
			}
			if($sort) {
				asort($a);
			}
			return $a;
		}
		return array();
	}
	
	static function prepareSizes($sizes) {
		$sizes=explode("\r\n",$sizes);
		$s=array();
		foreach ($sizes as $k=>$v) {
			$v=trim($v);
			if(empty($v)) {
				continue;
			}
			$v=explode("=",$v);
			if(count($v)!=2) {
				continue;
			}
			$v1=explode("x",$v[1]);
			$s[$v[0]]=array($v1[0],$v1[1],isset($v1[2])?$v1[2]:"{$v1[0]}x{$v1[1]}");
		}
		return $s;
	}
	
	static function alterImageAttribute(&$con,$product_id,$group_id,$attribute_id,$sub_id,$value_id,$field_name="",$save_fields=false) {
		$id=CAttributes::createAttributeValueId($product_id,$group_id,$attribute_id,$sub_id);
		if(isset($con["image_{$id}"])) {
			$con["image_{$id}"]['control']['parameters']['id']="{$value_id}";
			if(!empty($field_name)) {
				$con["image_{$id}"]['control']['parameters']['field']=$field_name;
			}
			if($save_fields) {
				$con["image_{$id}"]['control']['parameters']['save_fields']=array(
					"file_name"=>$field_name."_name",
					"file_size"=>$field_name."_size",
					"file_type"=>$field_name."_type",
				);
			}		
			return $con["image_{$id}"];
		}
		return false;
	}
	
	static function alterFileAttribute(&$con,$product_id,$group_id,$attribute_id,$sub_id,$value_id,$field_name="",$save_fields=false) {
		$id=CAttributes::createAttributeValueId($product_id,$group_id,$attribute_id,$sub_id);
		if(isset($con["file_{$id}"])) {
			$con["file_{$id}"]['control']['parameters']['id']="{$value_id}";
			if(!empty($field_name)) {
				$con["file_{$id}"]['control']['parameters']['field']=$field_name;
			}
			if($save_fields) {
				$con["file_{$id}"]['control']['parameters']['save_fields']=array(
					"file_size"=>$field_name."_size",
					"file_type"=>$field_name."_type",
				);
			}
			return $con["file_{$id}"];
		}
		return false;
	}
	
	static function getFakeImageControl($value_id,$sizes,$table="attribute_val_str",$field="value") {
		//$options=CAttributes::prepareOptions($options,false);
		$sizes=CAttributes::prepareSizes($sizes);
		
		$con=array(
			"image"=>array(    
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[0]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"__att_0","userFunc"=>"","FormatString"=>"",
				"parameters"=>array(
					"table"=>$table,
					"field"=>$field,
					"id"=>$value_id,
					"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
					"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
					'resize'=>true,
					'overwrite'=>false,
					'sizes'=>$sizes,
					'save_fields'=>array(
					)
				)
			),    
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
			),
			
			
		);
		
		return $con;
	}
	
	static function getFakeFileControl($value_id,$table="attribute_val_str",$field="value") {
		$con=array(
			"file_0"=>array(
			    'control'=>array("Label"=>"","name"=>"__attributes[0]","isHTML"=>false,"tagName"=>"ManagedFile","bound_field"=>"__att_0","userFunc"=>"","FormatString"=>"",
			    "parameters"=>array(
			    	"table"=>$table,
			    	"field"=>$field,
			    	"id"=>$value_id,
			    	"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
			    	"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
			    	'save_fields'=>array(
					)
				)
				),
			    'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
			)
			
		);
		
		return $con;
	}
	
	static function fixDependedFields(&$con) {
		$tags=array(
			"Input",
			"CheckBox",
			"Autocomplete",
			"DateControl",
			"DateTimeControl",
			"Select",
			"MultiSelect",
			"SingleSelectButton",
			"MultiSelectButton",
		);
		$functions=array(
			"CheckBox"=>"getCheckBoxValue",
			"MultiSelect"=>"getMultiSelectValues",
		);
		$f=array();
		$f1=array();
		$f2=array();
		foreach ($con as $k=>$v) {
			if(in_array($v['control']['tagName'],$tags)) {
				if(isset($functions[$v['control']['tagName']])) {
					$f[]="{$k}: function() { return {$functions[$v['control']['tagName']]}('{$k}'); }";
					$f2[]=$k;
				}
				else {
					$f[]="{$k}: function() { return $('#{$k}').val(); }";					
					$f1[]=$k;
				}
			}
		}
		if(!empty($f)) {
			$f=implode(",\n",$f);
			$f1=implode(',',$f1);
			$f2=implode(',',$f2);
		
			foreach ($con as $k=>$v) {
				if($v['control']['tagName']=="Autocomplete") {
					if(!empty($con[$k]['control']['parameters']["depends_on"])) {
						$con[$k]['control']['parameters']["depends_on"].=",".$f;
						//$con[$k]['control']['parameters']["depends_on"].="p:function() {return getAutocompleteValues('$f1','$f2')}";
						
					}
					else {
						$con[$k]['control']['parameters']["depends_on"]=$f;
						//$con[$k]['control']['parameters']["depends_on"]="p:function() {return getAutocompleteValues('$f1','$f2')}";
						
					}
				}
			}
		}
	}
	
	static function getTableByIndex($index) {
		return $GLOBALS['attribute_type_tables'][$index];
	}
	
	static function getTableByType($type) {
		$match=CAttributes::getMatcharray();
		$match=array_flip($match);
		return CAttributes::getTableByIndex($match[$type]);
	}
	
	static function getAttributeTableFieldName() {
		return "name";
	}
	
	static function getOrderControl($product_id,$group_id,$attribute_id,$sub_id,$label,$row,$table="") {
		$id=CAttributes::createAttributeValueId($product_id,$group_id,$attribute_id,$sub_id);
		$parameters=CAttributes::prepareRowParameters($row);
		$extra_style=isset($parameters['style'])&&!empty($parameters["style"])?$parameters["style"]:"";
		$is_real_table=!empty($table);
		$con=array(
			"order_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"order[$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"__order_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"attributes"=>array("size"=>3)),
				'table'=>$table,
				'is_real_table'=>$is_real_table,
				'is_order'=>1,
			),
		);
		return $con;
	}
	
	static function prepareRowParameters($row) {
		@$php_data=unserialize($row['php_data']);
		if(!is_array($php_data)) {
			$php_data=array();
		}
		return $php_data;
	}
	
	static function getControl($attribute_index,$product_id,$group_id,$attribute_id,$sub_id,$label="",$required=false,$row=array(),$is_search=false,$table="") {
		
		$options=array();
		$php_data=array();
		$parameters=CAttributes::prepareRowParameters($row);
		if(!empty($parameters)) {
			$options=CAttributes::prepareOptions($parameters['options'],$attribute_index!=7?(int)$parameters['sort_alpha']:0);
		}
		
		
		$init_required=$required;
		
		$s=(int)$sub_id;
		if("$s"!="$sub_id") {
			$required=false;
		}
		$id=CAttributes::createAttributeValueId($product_id,$group_id,$attribute_id,$sub_id);
		if($required) {
			$enum_invalid_values=array(0);
		}
		else {
			$enum_invalid_values=array();
		}
		$save_fields=false;
		if($attribute_index==7) {	//image
			$sizes=CAttributes::prepareSizes($php_data['sizes']);
			if(isset($php_data['save_fields'])) {
				$save_fields=true;
			}						
		}
		if($attribute_index==8) {	//file
			if(isset($php_data['save_fields'])) {
				$save_fields=true;
			}			
		}
		
		if($attribute_index==9) {
			if(!empty($options)) {
				$tmp_tinymce_attributes=explode("=",$options[0]);
				for($i=0;$i<count($tmp_tinymce_attributes);$i+=2) {
					$tinymce_attributes[$tmp_tinymce_attributes[$i]]=$tmp_tinymce_attributes[$i+1];
				}				
			}
		}
		
		if(in_array($attribute_index,array(12,13))) {
			$filter="";
			$nomenclature_ids=$php_data['nomenclature_ids'];
			if(empty($nomenclature_ids)) {
				$nomenclature_ids="0";
			}
			else {
				
				$filter=array(
					'filter'=>array(
						'attribute_cluster_id'=>$nomenclature_ids,
					)
				);
				$filter=http_build_query($filter,null,"&");				
			}
		}
		
	
		
		$multisel_tag="MultiSelect";
		$multi_name="[]";
		if(isset($php_data["render_type"])) {
			switch ($php_data["render_type"]) {
				case 1:	{	//select e po default
					$multisel_tag="MultiSelect";
					$multi_name="[]";
					break;
				} 
				case 2: {
					$multisel_tag="GroupCheckBox";
					break;
				}
				case 3: {
					$multisel_tag="DoubleSelect";
					
					break;
				}
				default: {
					$multisel_tag="MultiSelect";
					$multi_name="[]";
					break;
				}
			}
		}
		
		$row_name=isset($row['control_name'])?$row['control_name']:$sub_id;
		$attr=isset($row['attributes'])&&is_array($row['attributes'])?$row['attributes']:array();
		$extra_style=isset($parameters['style'])&&!empty($parameters["style"])?$parameters["style"]:"";
		
		
		$checkbox_tag=$is_search?"Select":"CheckBox";
		
		if($is_search) {
			$enum_options=array(-1=>"All",0=>"Not set")+$options;
			//$enum_sql=$add_zero=array("key"=>-1,"value"=>"",);
			$enum_sql=$add_zero=array("key"=>array(-1=>"All",0=>"Not set"),"value"=>"",);
			//$set_options=array(-1=>"Not set")+$options;
			$set_options=$options;
		}
		else {
			$enum_options=array(0=>"")+$options;
			$enum_sql=$add_zero=array("key"=>0,"value"=>"",);
			$set_options=$options;
		}

		$is_real_table=false;
		if(empty($table)) {
			$table=CAttributes::getTableByIndex($attribute_index);
		}
		else {
			$is_real_table=true;
		}
		
		$con=array(
			"bool_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>$checkbox_tag,"states"=>array("on"=>1,"off"=>0),"bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>array(-1=>"",0=>"No",1=>"Yes")))),
				'write_data'=>array("type"=>DATA_TINYINT,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>-1,"cond"=>"="    ),
			),
			"string_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"like"    ),
			),
			"ac_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Autocomplete","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,
					"parameters"=>array(
						"depends_on"=>"__attribute_id:function(){return '{$attribute_id}';}",
					),
				),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"like"    ),
			),
			"submit_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"submit[{$row_name}][$id]","isHTML"=>false,"tagName"=>"Submit","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"attributes"=>$attr),
			),
			"int_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style),
				'write_data'=>array("type"=>DATA_INT,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"between" ,"right_field"=>"_to_int_{$id}"),
				
			),
			"_to_int_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[_to_$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"_to___att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style),
			),
			"dec_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style),
				'write_data'=>array("type"=>DATA_DECIMAL,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"between","right_field"=>"_to_dec_{$id}"),
			),
			"_to_dec_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[_to_$id]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"_to___att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style),
			),
			"tinymce_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"attributes"=>$tinymce_attributes),
				'write_data'=>array("type"=>DATA_TEXT,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),				
			),
			"text_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"attributes"=>$tinymce_attributes),
				'write_data'=>array("type"=>DATA_TEXT,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
			),
			"date_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"%d/%m/%Y","extra_style"=>$extra_style),
				'write_data'=>array("type"=>DATA_DATE,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"between","right_field"=>"_to_date_{$id}"),
			),
			"_to_date_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[_to_$id]","isHTML"=>false,"tagName"=>"DateControl","bound_field"=>"_to___att_{$id}","userFunc"=>"","FormatString"=>"%d/%m/%Y","extra_style"=>$extra_style),
			),
			"datetime_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M","extra_style"=>$extra_style),
				'write_data'=>array("type"=>DATA_DATETIME,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"between","right_field"=>"_to_datetime_{$id}"),
			),
			"_to_datetime_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[_to_$id]","isHTML"=>false,"tagName"=>"DateTimeControl","bound_field"=>"_to___att_{$id}","userFunc"=>"","FormatString"=>"%d/%m/%Y %H:%M","extra_style"=>$extra_style),				
			),
			
			
			
			
			"enum_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$enum_options,))),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"invalid_values"=>$enum_invalid_values,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),				
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>-1,"cond"=>"="),
			),
			"enumlist_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"Select","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"autoload"=>array("type"=>"sql","value"=>array("DataSource"=>$php_data["sql"],"addzero"=>$enum_sql)),
					"parameters"=>array(
						"display_sql"=>$php_data['display_sql'],
					),
				),
				'write_data'=>array("type"=>DATA_INT,"required"=>$required,"invalid_values"=>$enum_invalid_values,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),				
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>-1,"cond"=>"="),
			),
			"setlist_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id][]","isHTML"=>false,"tagName"=>"MultiSelect","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"autoload"=>array("type"=>"sql","value"=>array("DataSource"=>$php_data["sql"])),
					"parameters"=>array(
						"display_sql"=>$php_data['display_sql'],
					),
				),
				'write_data'=>array("type"=>DATA_INT,"required"=>$required,"invalid_values"=>$enum_invalid_values,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),				
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>-1,"cond"=>"find_in_set"),
			),
			
			
			"enumnom_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"SingleSelectButton","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,/*"autoload"=>array("type"=>"sql","value"=>array("DataSource"=>"select id,name from products where attribute_cluster_id in($nomenclature_ids)","addzero"=>array("key"=>0,"value"=>"",))),*/
					"parameters"=>array(
						"select_url"=>BE_DIR."products/?search=single",
						"filter"=>$filter,
						"getJsFields"=>"",
						"display_sql"=>$php_data['display_sql']
					),
				),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"invalid_values"=>$enum_invalid_values,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"="),
			),
			"setnom_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"MultiSelectButton","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,/*"autoload"=>array("type"=>"sql","value"=>array("DataSource"=>"select id,name from products where attribute_cluster_id in($nomenclature_ids)")),*/
				"parameters"=>array(
						"select_url"=>BE_DIR."products/?search=1",
						"getJsFields"=>"",
						"filter"=>$filter,
						"display_sql"=>$php_data["display_sql"]
					),
				),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("search_name"=>"{$table}.group_id='{$group_id}' AND {$table}.attribute_id='{$attribute_id}' AND {$table}.value","matchAllValue"=>"","cond"=>"in"    ),
			),
			
			
			"set_{$id}"=>array(
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]{$multi_name}","isHTML"=>false,"tagName"=>$multisel_tag,"use_reorder"=>1,"bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,"autoload"=>array("type"=>"arrayname","value"=>array("DataSource"=>$set_options,))),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>$required,"regex"=>array('pattern'=>$parameters['reg_expression'],'msg'=>$parameters['reg_msg'])),
				'search_data'=>array("extra_where"=>"{$table}.attribute_id='{$attribute_id}' AND","search_name"=>"{$table}.value","matchAllValue"=>"","cond"=>"find_in_set",/*"strict_field"=>"ch_strict_set_{$id}","exclude_field"=>"ch_exclude_set_{$id}" */),
			),
			/*"ch_strict_set_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[ch_strict_set{$id}]","isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"ch_strict_set__att_{$id}","userFunc"=>"","FormatString"=>"","states"=>array("on"=>1,"off"=>0)),				
			),
			"ch_exclude_set_{$id}"=>array(
				'control'=>array("Label"=>"To","name"=>"__attributes[ch_exclude_set_{$id}]","isHTML"=>false,"tagName"=>"CheckBox","bound_field"=>"ch_exclude_set__att_{$id}","userFunc"=>"","FormatString"=>"","states"=>array("on"=>1,"off"=>0)),				
			),*/
			
			"image_{$id}"=>array(    
				'control'=>array("Label"=>"{$label}","name"=>"__attributes[$id]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,
				"parameters"=>array(
					"table"=>"{$table}",
					"field"=>"value",
					"id"=>$product_id,
					"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
					"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
					'resize'=>true,
					'overwrite'=>false,
					'sizes'=>$sizes,
					'save_fields'=>array(
					)
				)
			),    
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),   
				'search_data'=>array("extra_where"=>"{$table}.attribute_id='{$attribute_id}' AND","search_phrase"=>"IF('_#VAL#_'=1,{$table}.value!='',{$table}.value='')","matchAllValue"=>-1,"cond"=>"select"    ),
			),
			"file_{$id}"=>array(
			    'control'=>array("Label"=>"{$label}","name"=>"__attributes[{$id}]","isHTML"=>false,"tagName"=>"ManagedFile","bound_field"=>"__att_{$id}","userFunc"=>"","FormatString"=>"","extra_style"=>$extra_style,
			    "parameters"=>array(
			    	"table"=>"{$table}",
			    	"field"=>"value",
			    	"id"=>$product_id,
			    	"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
			    	"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
			    	'save_fields'=>array(
					)
				)
				),
			    'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
			    'search_data'=>array("extra_where"=>"{$table}.attribute_id='{$attribute_id}' AND","search_phrase"=>"IF('_#VAL#_'=1,{$table}.value!='',{$table}.value='')","matchAllValue"=>-1,"cond"=>"select"    ),   
			    ),
			
		);
		
		switch ($attribute_index) {
			case 7: {	//image
				if($is_real_table) {
					if($group_id==1) {
						$image_id=$product_id;
					}
					else {
						$image_id=(int)getdb()->getone("select id from {$table} where product_id=? and sub_id=?",array($product_id,$sub_id));
					}
					$fn=getdb()->getone("select ".CAttributes::getAttributeTableFieldName()." from attributes where id='{$attribute_id}'");
				}
				else {
					$fn="";
					$image_id=(int)getdb()->getone("select id from {$table} where product_id=? and group_id=? and attribute_id=? and sub_id=?",array($product_id,$group_id,$attribute_id,$sub_id));
				}
				CAttributes::alterImageAttribute($con,$product_id,$group_id,$attribute_id,$sub_id,$image_id,$fn,$save_fields);
				
				break;
			}
			case 8: {	//file
				if($is_real_table) {
					$file_id=(int)getdb()->getone("select id from {$table} where product_id=? and sub_id=?",array($product_id,$sub_id));
					$fn=getdb()->getone("select ".CAttributes::getAttributeTableFieldName()." from attributes where id='{$attribute_id}'");
				}
				else {
					$file_id=(int)getdb()->getone("select id from {$table} where product_id=? and group_id=? and attribute_id=? and sub_id=?",array($product_id,$group_id,$attribute_id,$sub_id));
					$fn="";
				}
				CAttributes::alterFileAttribute($con,$product_id,$group_id,$attribute_id,$sub_id,$file_id,$fn,$save_fields);
				break;
			}
		}
		
		$match=CAttributes::getAttributeMatchName($attribute_index);
		if(isset($con[$match."_".$id])) {
			$con[$match."_".$id]['work_table']=$table;
			$con[$match."_".$id]['is_real_table']=$is_real_table;
			if($init_required&&!$required) {
				$con[$match."_".$id]['row_required']=true;
			}
			if ($is_search&&!isset($con[$match."_".$id]["search_data"])) {
				return null;
			}
			
			if($is_search&&isset($con[$match."_".$id]["search_data"])) {
				$con[$match."_".$id]["search_data"]['search_table']=$table;
				if($is_real_table) {
					$fn=getdb()->getone("select ".CAttributes::getAttributeTableFieldName()." from attributes where id='{$attribute_id}'");
					if(!in_array($con[$match."_".$id]["control"]['tagName'],array("ManagedImage","ManagedFile"))) {
						$con[$match."_".$id]["search_data"]["search_name"]="{$table}.`$fn`";
					}
					else {
						$con[$match."_".$id]['search_data']["search_phrase"]="IF('_#VAL#_'=1,{$table}.`{$fn}`!='',{$table}.`{$fn}`='')";
					}
					$con[$match."_".$id]["search_data"]["extra_where"]="";
					//
				}
			}
			
			$con[$match."_".$id]['control']['show_in_list']=(int)$parameters['BEListVisible'];
			$result=array($match."_".$id=>$con[$match."_".$id]);
			
			
			if($is_search&&isset($con["_to_".$match."_".$id])) {
				$result["_to_".$match."_".$id]=$con["_to_".$match."_".$id];				
			}
			if($is_search&&isset($con["ch_exclude_".$match."_".$id])) {
				$result["_to_".$match."_".$id]=$con["ch_exclude_".$match."_".$id];				
			}
			if($is_search&&isset($con["ch_scrict_".$match."_".$id])) {
				$result["_to_".$match."_".$id]=$con["ch_scrict_".$match."_".$id];				
			}
			
			
			if($is_search && in_array($attribute_index,array(7,8))){
				$result[$match."_".$id]['control']["autoload"]=array("type"=>"arrayname","value"=>array("DataSource"=>array(-1=>"",0=>"No",1=>"Yes")));
				$result[$match."_".$id]['control']['tagName'] = 'Select';
				$result[$match."_".$id]['control']['is_image'] = $attribute_index==7;
				$result[$match."_".$id]['control']['is_file'] = $attribute_index==8;
			}
			
			return $result;
		}
	
		return null;
	}
	
	static function renderProductCluster($product_id,$cluster_id=0,$is_search=false) {
		$db=getdb();
		if(empty($cluster_id)) {
			$cluster_id=$db->getone("select attribute_cluster_id from products where id='{$product_id}'");
		}
		$groups=$db->getone("select attribute_group_ids from attribute_clusters where id='{$cluster_id}'");
		if(empty($groups)) {
			return;
		}
		$group_ids=explode(",",$groups);
		$template_field=$is_search?"search_template":"template";
		$groups=$db->getassoc("select id,name,attribute_ids,is_table,{$template_field} as template,php_code from attribute_groups where id in($groups)");
		
		
		$con=array();
		$templates=array();
		$tinymce=array();
		$php=array();
		$has_as_table=false;
		foreach ($group_ids as $k=>$v) {
			$v=$groups[$v];
			$has_template=empty($v['template']);
			
			if($v['is_table']) {
				$has_as_table=1;
			}
			
			$result=CAttributes::renderGroup($v['id'],$v['attribute_ids'],$v['is_table'],$product_id,$has_template,2,$is_search);
			$con+=$result['con'];
			$templates[]=array('name'=>$v['name'],'t'=>$has_template?$result['template']:str_replace(CAttributes::getProductConstantString(),$product_id,$v['template']));
			$tinymce+=$result['tinymce'];
			if(!empty($v['php_code'])) {
				$php[]=$v['php_code'];
			}
			/*$templates[]=<<<EOD
		<fieldset>
			<legend>{$v['name']}</legend>
			{$result['template']}
		</fieldset>
EOD;*/
		}
		$r= array(
			'con'=>array('controls'=>$con),
			'template'=>$templates,
			'tinymce'=>$tinymce,
			'php'=>$php,
			'has_as_table'=>$has_as_table,
		);
		return $r;
		//return Master::create($r['con'],implode("<br />",$templates),$_POST,null,false,"<body>",false);
	}
	
	static function renderGroup($group_id,$attribute_ids,$is_table,$product_id,$generate_template=false,$template_columns=2,$is_search=false,$use_real_table=false) {
		
		$con=array();
		$td=array();
		if(empty($attribute_ids)) {
			return $con;
		}
		
		$db=getdb();	
		$end=0;
		
		$row=$db->getrow("select * from attribute_groups where id='$group_id'");
		$table=(int)$row["use_real_table"]?$row["real_table_name"]:"";
		
		if($is_table&&!$is_search) {
			if((int)$row["use_real_table"]) {
				$end=$db->getone("select max(sub_id) from `{$table}` where product_id='{$product_id}'");
			}
			else {
				$q=array();			
				foreach ($GLOBALS["attribute_value_tables"] as $k=>$v) {
					$q[]="SELECT max(sub_id) as sub_id FROM {$v} WHERE group_id='{$group_id}' and product_id='{$product_id}'";
				}
				$q=implode(" UNION ",$q);
				$end=$db->getone(
					"select max(sub_id) from (
					{$q}
					) as tmp"
				);
			}
			
			//$end=$db->getone("select max(sub_id) from attribute_values where group_id=?",array($group_id));
			if(is_null($end)) {
				$end=-1;
			}
		//	else {
		//		$end=(int)$end+1;
		//	}
		}
		$attributes=$db->getassoc("select * from attributes where id in($attribute_ids)");
		$attribute_ids=explode(",",$attribute_ids);
		$tinymce_controls=array();
		$spans=array();
		$start=0;
		
		
		
		$cc=0;
		for($i=$start;$i<=$end;$i++) {
			
			//foreach ($attributes as $k=>$v) {
			foreach ($attribute_ids as $v) {
				$v=$attributes[$v];
				$suff=empty($v['be_suffix'])?"":"(".$v['be_suffix'].")";
				$c=CAttributes::getControl($v['type'],$product_id,$group_id,$v['id'],$i,$v['be_prefix'].$suff,$v['is_required'],$v,$is_search,$table);
				if(!empty($c)) {
					if($v['type']==9) {	//tinymce
						$pid=CAttributes::createAttributeValueId($product_id,$group_id,$v['id'],$i);
						$tinymce_controls[$pid]="tinymce_".$pid;					
					}
					//$spans[]=$pid;
					//$con+=CAttributes::getColspanControl($pid);
					$con+=$c;					
					
				}
			}
			if($is_table&&!$is_search) {
				$max_order=$end+1;
				if($max_order>0) {
					$max_order="[1-{$max_order}]";
				}
				$result=CAttributes::getOrderControl($product_id,$group_id,$v['id'],$i,"Order{$max_order}",array("control_name"=>"order","attributes"=>array("size"=>"3")),$table);
				if(is_array($result)) {
					$con+=$result;					
				}
				else {
					if(DEBUG_MODE) {
						var_dump($result);
					}
				}
				$result=CAttributes::getControl(-1,$product_id,$group_id,$v['id'],$i,"Del",false,array("control_name"=>"del","attributes"=>array("onclick"=>"return window.confirm(&quot;Are you sure?&quot;)")),$is_search);
				if(is_array($result)) {
					$con+=$result;
				}
				else {
					if(DEBUG_MODE) {
						var_dump($result);
					}
				}
				
				
				$cc=2;
			} 
		}
		
		if($is_table&&!$is_search) {
			// sazdavame 1 prazen red
			reset($attribute_ids);
			foreach ($attribute_ids as $v) {
				$v=$attributes[$v];
			
				$c=CAttributes::getControl($v['type'],$product_id,$group_id,$v['id'],"_".$i,$v['be_prefix'].$suff,$v['is_required'],$v,$is_search,$table);
				if(!empty($c)) {
					if($v['type']==9) {	//tinymce
						$pid=CAttributes::createAttributeValueId($product_id,$group_id,$v['id'],"_".$i);
						$tinymce_controls[$pid]="tinymce_".$pid;					
					}
					//$spans[]=$pid;
					//$con+=CAttributes::getColspanControl($pid);
					$con+=$c;					
					$cc++;
				}
			}
		}
		
		CAttributes::fixDependedFields($con);
		
		
		
		if(!empty($con)&&$generate_template) {
			if($is_table&&!$is_search) {
				$template=CAttributes::generateTemplateTable($con,$cc);
			}
			else {
				$template=CAttributes::generateTemplate($con,$template_columns);
			}
		}
		//foreach ($spans as $spid) {
		//	unset($con["colspan_".$spid]);
		//}
		
		return array('con'=>$con,'template'=>$template,"tinymce"=>$tinymce_controls);
	}
	
	static function loadValue($par,$sub_id=0) {
		
		$id=$par[0];
		$index=$par[1];
		$row=$par[2];
		$dg=$par[3];
		
		//$id["fld"];
		
		$val=$row[$id["fld"]];
				
		if(isset($id['arrayname'])&&is_array($id['arrayname'])) {
			return $id['arrayname'][$val];
		}
		if(isset($id['sql'])&&!empty($id['sql'])) {
			if("$val"!="") {
				return getdb()->getOne(str_replace("_#VAL#_",$val,$id['sql']));
			}
		}
		if(!empty($id['format'])) {
			return FormatUtils::translateFormat($id['format'],$val);
		}
		
		$field_name=$id['fld'];
		$id=CAttributes::getIdsFromString($field_name);
		
		if(!$id) {
			return $val;
		}
		
		return CAttributes::getFieldValue($field_name,$val);
	}
	
	static function MultiArrayToString($val,$array) {
		if(!is_array($array)) {
			return "";
		}
		$val=explode(',',$val);
		$a=array();
		foreach ($val as $v) {
			if(isset($array[$v])) {
				$a[]=$array[$v];
			}
		}
		if(!empty($a)) {
			return implode(", ",$a);
		}
	}
	
	static function getFieldValue($field_name,$val,$val_id=0) {
		$con=$GLOBALS['_list_controls']['con']['controls'];
		
		
		
		if(isset($con[$field_name])) {
/*			if($con[$field_name]['control']["Label"]=='fSet2'){
				var_dump($con[$field_name]);
			}*/

			$type=$con[$field_name]['control']["tagName"];
			if($con[$field_name]['control']["is_image"]) {
				$type="ManagedImage";
			}
			if($con[$field_name]['control']["is_file"]) {
				$type="ManagedFile";
			}
			
			
			switch ($type) {
				case "CheckBox": {
					$val= $GLOBALS["YES_NO"][$val];
					break;
				}
				case "SingleSelectButton": {
					if(!empty($con[$field_name]['control']['parameters']['display_sql'])) {
						if(!empty($val)) {
							$val= getdb()->getOne($con[$field_name]['control']['parameters']['display_sql'],array($val));
						}
					}
					break;
				}
				case "ManagedImage": {
					
					if(!empty($val)) {
						$s=array_keys($con[$field_name]["control"]["parameters"]["sizes"]);
						$s=$s[0];
						return <<<EOD
						<img src="/files/mf/{$con[$field_name]["control"]["parameters"]["table"]}/{$val_id}_{$con[$field_name]["control"]["parameters"]["field"]}_{$s}$val" width="50" height="50" />
EOD;
					}
					return "";
				}
				case "ManagedFile": {
					if(!empty($val)) {
						$val=FE_Utils::getFileExt($val);
						return <<<EOD
	<a href="/files/mf/{$con[$field_name]["control"]["parameters"]["table"]}/{$val_id}_{$con[$field_name]["control"]["parameters"]["field"]}$val" target="_blank">view</a>
EOD;
					}
				}
				case "MultiSelectButton": {
					if(!empty($val)) {
						if(!empty($con[$field_name]['control']['parameters']['display_sql'])) {
							$sql=str_replace("_#VAL#_",$val,$con[$field_name]['control']['parameters']['display_sql']);
							$r=getdb()->getassoc($sql);
							$val=CAttributes::MultiArrayToString($val,$r);
							//$val=implode(', ',$r);
						}
						else {
							$val="";
						}
						
					}
					else {
						$val="";
					}
					break;
				}
				
				case "GroupCheckBox":
				case "MultiSelect": {
					if(isset($con[$field_name]['control']['autoload'])&&is_array($con[$field_name]['control']['autoload']['value']['DataSource'])) {
						$f=explode(",",$val);
						$a=array();
						foreach ($f as $fv) {
							$a[]=$con[$field_name]['control']['autoload']['value']['DataSource'][$fv];
						}
						$val= implode(", ",$a );						
					}
					else {
						if(isset($con[$field_name]['control']['parameters']['display_sql'])&&!empty($con[$field_name]['control']['parameters']['display_sql'])) {
							$sql=$con[$field_name]['control']['parameters']['display_sql'];
							if("$val"!="") {
								$sql=str_replace("_#VAL#_",$val,$sql);
								$r=getdb()->getAssoc($sql);
								return CAttributes::MultiArrayToString($val,$r);
								//return implode(', ',$val);
							}							
						}
						$val="";
					}
					break;
				}
				default: {
					if(!empty($con[$field_name]['control']['FormatString'])) {
						$val=FormatUtils::translateFormat($con[$field_name]['control']['FormatString'],$val);
					}
					if(isset($con[$field_name]['control']['autoload'])) {
						if(is_array($con[$field_name]['control']['autoload']['value']['DataSource'])) {
							$val=$con[$field_name]['control']['autoload']['value']['DataSource'][$val];
						}
						else {
							if($con[$field_name]['control']['autoload']['type']=="sql") {
								
								if("$val"!="") {
									if(isset($con[$field_name]['control']['parameters']['display_sql'])&&!empty($con[$field_name]['control']['parameters']['display_sql'])) {
										$sql=$con[$field_name]['control']['parameters']['display_sql'];
										
									}
									else {
										$sql=CAttributes::parseSQL($con[$field_name]['control']['autoload']['value']['DataSource']);
									}
									$val=mysql_real_escape_string($val);
									$sql=str_replace("_#VAL#_",$val,$sql);
									$val=getdb()->getone($sql);
								}
								else {
									$val="";
								}
							}
						}
					}
					break;
				}
			}

		}
		return $val;
	}
	
	static function loadAttributeValueForList($par) {
		$id=$par[0];
		$index=$par[1];
		$row=$par[2]->DataSource->Rows[$index];
		$td=$par[3];
		$dom=$par[4];
		
	
		static $tds=null;
		static $cells=null;
		
		static $trs = array();
		
		if(is_null($tds)) {
			$tds=array();
		}
		if(is_null($cells)) {
			$cells=array();
		}
		
		
		$field_name=$par[5];
		$id=CAttributes::getIdsFromString($field_name);
		
		if(!$id) {
			return $par[0];
		}
		
		
		$con=$GLOBALS['_list_controls']['con']['controls'];
//		echo "<pre>";
//		print_r($con[$field_name]);
//		echo "</pre>";

		if(!empty($con[$field_name]["work_table"])&&(int)$con[$field_name]["is_real_table"]) {
			$table=$con[$field_name]["work_table"];
			$fn=getdb()->getone("select ".CAttributes::getAttributeTableFieldName()." from attributes where id='{$id[2]}'");
			$val_col=getdb()->getcol("select `{$fn}` from {$table} where product_id=? order by sub_id",array($row['id']));
			
		}
		else {		
			$table=CAttributes::getTableByType($id['type']);
			$val_col=getdb()->getcol("select value from {$table} where product_id=? and group_id=? and attribute_id=? order by sub_id",array($row['id'],$id[1],$id[2]));
		}
		

		if(count($val_col)>1) {
			if(empty($trs[$row['id']][0])){
				$tr=$td->parentNode;

				//$myRes = $td->setAttribute('myId',"{$row['id']}_{$id[1]}_{$id[2]}");

				while ($tr&&strtolower((string)$tr->tagName)!="tr") {
					$tr=$tr->parentNode;
				}
				
				$trs[$row['id']][0]=$tr;
				$next=$tr->nextSibling;
				for ($i=1; $i<count($val_col); $i++){
					$trs[$row['id']][$i] = $tr->cloneNode(true);
					foreach ($trs[$row['id']][$i]->childNodes as $chk) {
						if($chk->tagName&&$chk->tagName=="td") {
							$chk->nodeValue="&nbsp;";
						}
					}
					if(!$next) {						
						$tr1=$tr->parentNode->appendChild($trs[$row['id']][$i]);
					}
					else {
						$tr1=$tr->parentNode->insertBefore($trs[$row['id']][$i],$next);
					}
				}				
			}
			

			
			
		}
		$cells[$index][]=count($val_col);
		
		
		$result=array();
		if(isset($con[$field_name])) {
			foreach ($val_col as $val_index=>$val) {
				switch ($con[$field_name]['control']["tagName"]) {
					case "CheckBox": {
						$val= $GLOBALS["YES_NO"][$val];
						break;
					}
					case "SingleSelectButton": {
						$val= getdb()->getOne($con[$field_name]['control']['parameters']['display_sql'],array($val));
						break;
					}
					case "MultiSelectButton": {
						if(!empty($val)) {
							$sql=str_replace("_#VAL#_",$val,$con[$field_name]['control']['parameters']['display_sql']);
							$r=getdb()->getcol($sql);
							$val=implode(', ',$r);
							
						}
						else {
							$val="";
						}
						break;
					}
					case "MultiSelect": {
						if(isset($con[$field_name]['control']['autoload'])&&is_array($con[$field_name]['control']['autoload']['value']['DataSource'])) {
							$f=explode(",",$val);
							$a=array();
							foreach ($f as $fv) {
								$a[]=$con[$field_name]['control']['autoload']['value']['DataSource'][$fv];
							}
							$val= implode(", ",$a );						
						}
						else {
							$val="";
						}
						break;
					}
					default: {
						if(!empty($con[$field_name]['control']['FormatString'])) {
							$val=FormatUtils::translateFormat($con[$field_name]['control']['FormatString'],$val);
						}
						if(isset($con[$field_name]['control']['autoload'])&&is_array($con[$field_name]['control']['autoload']['value']['DataSource'])) {
							$val=$con[$field_name]['control']['autoload']['value']['DataSource'][$val];
						}
						break;
					}
				}
				//$result[]=$val;
				if($val_index==0) {
					$result=$val;
					
				}
				else {
					$new_row=$trs[$row['id']][$val_index];
					foreach ($new_row->childNodes as $chk) {
							if($chk->tagName&&$chk->tagName=="td"&&$chk->getAttribute('myid')=="{$id[1]}_{$id[2]}") {
								$chk->nodeValue=$val;
							}
						}
					/*
					if(!isset($tds[$index][$val_index])) {
						$next=$tr->nextSibling;
						$new_node = $main_tr->cloneNode(true);
						foreach ($new_node->childNodes as $chk) {
							if($chk->tagName&&$chk->tagName=="td") {
								$chk->nodeValue="1";
							}
						}
						if(!$next) {						
							$tr1=$tr->parentNode->appendChild($new_node);
						}
						else {
							$tr1=$tr->parentNode->insertBefore($new_node,$next);
						}
						$tds[$index][$val_index]=$new_node;
											
					}
					else {
						//return "0";
					}*/
				}
			}
		}
		
	/*	echo "<pre>";
		print_r($cells);
		echo "</pre>";*/
		//return implode("<hr />",$result);
		if(empty($result)) {
			$result="";
		}
		return $result;
		
	}
	
	
	
	static function parseSQL($sql) {
		$sql=str_replace('"',"",$sql);
		$sql=str_ireplace("select id,","select ",$sql);
		$words=array(" group by "," having "," order by "," limit ");
		$where=" where id='_#VAL#_' ";
		if(stripos($sql," where ")>0) {
			$sql=str_replace(" where "," where id='_#VAL#_' and ",$sql);
			$where="";
		}
		
		foreach ($words as $k=>$v) {
			$group_pos=strripos($sql,$v);
			if($group_pos!==false) {
				$str=substr($sql,0,$group_pos);
				$str.=$where.substr($sql,$group_pos);
				return $str;
			}
		}
		return $sql.$where;
	}
	
		static function prepareListTable($con,$dg,$use_bound_field=false,$use_standart=false) {
		$headers=array();
		$body=array();
		
		if(is_array($con['controls'])) {
			foreach ($con['controls'] as $k=>$v) {
				
				$v=$v['control'];
				if(!isset($v['show_in_list'])||!$v['show_in_list']) {
					continue;
				}
				$bf=$use_bound_field?$v['bound_field']:$k;
				$o=DataGridNew::getFieldOrderString($bf,$v["Label"],$dg->currentorder,$dg->control_id);
				$headers[]=<<<EOD
		<td class="header_nor">{$o}</td>
EOD;
				$arrayname=$v['autoload']['type']=="arrayname"?"arrayname=\"".str_replace(array("\$GLOBALS[","'",'"',"]"),array("","","",""),$v['autoload']['value']['DataSource']).'"':"";
				$sql=$v['autoload']['type']=="sql"?CAttributes::parseSQL($v['autoload']['value']["DataSource"]):"";
				
				if(!empty($v['FormatString'])) {
					//$format='format="'.$v['FormatString'].'"';
					$format=$v['FormatString'];
				}
				else {
					$format="";
				}

				$ids=CAttributes::getIdsFromString($k);
			 	if(!$use_standart) {
			 		$body[]=array(
			 			'fld'=>$bf,
			 		);
					/*$body[]=<<<EOD
		<td myid="{$ids[1]}_{$ids[2]}"><ITTI field_name="{$bf}" class="CAttributes" userfunc="loadAttributeValueForList"></ITTI></td>
EOD;*/
				}
				else {
					$body[]=array(
						'fld'=>$bf,
						'sql'=>$sql,
						'arrayname'=>$arrayname,
						'format'=>$format
					);
					/*$body[]=<<<EOD
				<td myid="{$ids[1]}_{$ids[2]}"><ITTI field_name="{$bf}" {$arrayname} {$sql} {$format}></ITTI></td>
EOD;*/
				}
			}
		}
		
		return array('h'=>$headers,'b'=>$body);
	}
	
	
	static function prepareListTable_old($con,$use_bound_field=false,$use_standart=false) {
		$headers=array();
		$body=array();
		
		if(is_array($con['controls'])) {
			foreach ($con['controls'] as $k=>$v) {
				
				$v=$v['control'];
				if(!isset($v['show_in_list'])||!$v['show_in_list']) {
					continue;
				}
				$bf=$use_bound_field?$v['bound_field']:$k;
				$headers[]=<<<EOD
		<td class="header_nor"><a order="{$bf}">{$v['Label']}</a></td>
EOD;
				$arrayname=$v['autoload']['type']=="arrayname"?"arrayname=\"".str_replace(array("\$GLOBALS[","'",'"',"]"),array("","","",""),$v['autoload']['value']['DataSource']).'"':"";
				$sql=$v['autoload']['type']=="sql"?'sql="'.CAttributes::parseSQL($v['autoload']['value']["DataSource"]).'"':"";
				
				if(!empty($v['FormatString'])) {
					$format='format="'.$v['FormatString'].'"';
				}
				else {
					$format="";
				}

				$ids=CAttributes::getIdsFromString($k);
			 	if(!$use_standart) {
					$body[]=<<<EOD
		<td myid="{$ids[1]}_{$ids[2]}"><ITTI field_name="{$bf}" class="CAttributes" userfunc="loadAttributeValueForList"></ITTI></td>
EOD;
				}
				else {
					$body[]=<<<EOD
				<td myid="{$ids[1]}_{$ids[2]}"><ITTI field_name="{$bf}" {$arrayname} {$sql} {$format}></ITTI></td>
EOD;
				}
			}
		}
		
		return array('h'=>$headers,'b'=>$body);
	}
	
	static function generateTemplate($array,$colcount=0) {
		if(empty($array)) {
			return '';
		}
		
		if(empty($colcount)) {
			$colcount=1;
		}
		$controls=$array;
		$c_span=$colcount*2;
		
		$cols=array();
		$cw=ceil(100/$c_span);
		if(1||!$GLOBALS['skip_meta_tag']) {
			$meta=<<<EOD
			<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
EOD;
		}
		
		for($i=0;$i<$colcount;$i++) {
			if($i==$colcount-1) {
				$cols[]="<col width='{$cw}%'>";
				$cols[]="<col width='{$cw}%'>";
			}
			else {
				$cols[]="<col width='{$cw}%'>";
				$cols[]="<col width='{$cw}%'>";
			}
		}
		$cols=implode("\n",$cols);
		
		$str=<<<EOD
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table cellpadding="5" cellspacing="0" class="table_attributes" align="center" border='0'>
<colgroup span="{$c_span}" width="0*">
{$cols}
</colgroup>
<tbody>
	
EOD;
	
		$arr=array();
		foreach ($controls as $k=>$v) {
			
			$v=$v['control'];
			
			
			if(empty($v)) {
				$arr[]="";
				$arr[]="";
				continue;
			}
			if($v['tagName']=='Select') {
				$focus="onclick=\"document.getElementById('{$k}').focus();\"";
			}
			else {
				$focus="for=\"{$k}\"";
			}
			$label="<label {$focus}>{$v['Label']}</label>";
			if(!empty($v['extra_style'])) {
				$style=" ".$v['extra_style'];
				
			}
			else {
				$style="";
			}
			$c="<ITTI field_name='{$k}'{$style}></ITTI>";
			
			
			$arr[]=$label;
			$arr[]=$c;	
		}
		$ttds=array();
		for($i=0;$i<$colcount;$i++) {
			$ttds[]="\n<td class='_tdl'>";
			$ttds[]="\n<td class='_tdr'>";
		}
		$str.=FE_Utils::createTableCells($arr,$colcount*2,$ttds);
		$str.=<<<EOD
		
</tbody></table>
EOD;
		return $str;
	}
	
	static function generateTemplateTable($array,$colcount) {
		if(empty($array)) {
			return '';
		}
		
		if(empty($colcount)) {
			$colcount=1;
		}
		$controls=$array;

		/*$cols=array();
		$cw=ceil(100/$c_span);
		for($i=0;$i<$colcount;$i++) {
			$cols[]="<col width='{$cw}%'>";
			$cols[]="<col width='{$cw}%'>";			
		}
		$cols=implode("\n",$cols);*/
		
		$str=<<<EOD
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table cellpadding="5" cellspacing="0" class="table_attributes" align="center" border='0'>
<tbody>
	
EOD;
	
		$arr=array();
		$header=array();
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
			if(empty($v)) {
				$arr[]="";
				$arr[]="";
				continue;
			}
			if($v['tagName']=='Select') {
				$focus="onclick=\"document.getElementById('{$k}').focus();\"";
			}
			else {
				$focus="for=\"{$k}\"";
			}
			$label=$v['Label'];
			if(!empty($v['extra_style'])) {
				$style=" ".$v['extra_style'];
				
			}
			else {
				$style="";
			}
			$c="<ITTI field_name='{$k}'{$style}></ITTI>";
			
			if(count($header)<$colcount) {
				$header[]=$label;
			}
			$arr[]=$c;	
		}
		
		$str.="<tr>";
		$str.="<th>".implode("</th><th>",$header)."</th>";
		$str.="</tr>";
		
		
		$str.=FE_Utils::createTableCells($arr,$colcount);
		$str.=<<<EOD
		
</tbody></table>
EOD;
		return $str;
	}
	
	static function getIdsFromString($str) {
		$s=explode("_",$str);
		//if(!in_array(count($s),array(4,5))) {
		//	return "";
		//}
		
		if(!in_array(count($s),array(5,6))) {
			return "";
		}
		
		return array(0=>(int)$s[1],1=>(int)$s[2],2=>(int)$s[3],3=>(string)$s[4],4=>(int)$s[5],"type"=>$s[0],);
	}
	
	//static function deleteSubIdRealTable($product_id,$group_id,$sub_id,$table) {
	static function deleteSubIdRealTable($product_id,$sub_id,$table,$con) {
		$db=getdb();
		$id=$db->getone("select id from `{$table}` where product_id=? and sub_id=?",array($product_id,$sub_id));
		ControlValues::deleteManagedFiles($id,$con['controls'],false);
		ControlValues::deleteManagedImages($id,$con['controls'],false);
		
		$db->execute("DELETE FROM {$table}
			WHERE
			product_id='{$product_id}' AND
			sub_id='{$sub_id}'
		");
		
		$db->execute("UPDATE {$table}
			SET sub_id=sub_id-1 
			WHERE 
			product_id='{$product_id}' AND
			sub_id>'{$sub_id}'
			ORDER BY sub_id
		");		
	}
	
	static function deleteSubId($product_id,$group_id,$attribute_id,$sub_id) {
		$db=getdb();
		
		$table=CAttributes::getTableByType("image");
		$ids=$db->query("SELECT type,{$table}.id,php_data 
			FROM {$table} 
			INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
			WHERE 
			product_id='{$product_id}' AND
			group_id='{$group_id}' AND
			sub_id='{$sub_id}' AND
			type IN (7,8)
			"
		);
		CAttributes::deleteAttributeFiles($ids);
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->execute("DELETE FROM {$table}
				WHERE
				product_id='{$product_id}' AND
				group_id='{$group_id}' AND
				sub_id='{$sub_id}'
			");
			
			$db->execute("UPDATE {$table}
				SET sub_id=sub_id-1 
				WHERE 
				product_id='{$product_id}' AND
				group_id='{$group_id}' AND
				sub_id>'{$sub_id}'
				ORDER BY sub_id
			");
		}
	}
	
	static function getImageFileControls($con,$value_id) {
		$result=array();
		foreach ($con as $k=>$v) {
			if($v['control']["tagName"]=="ManagedImage") {
				$id=CAttributes::getIdsFromString($k);
				$result[$k]=$v;
				CAttributes::alterImageAttribute($result,$id[0],$id[1],$id[2],$id[3],$value_id,$v['control']["parameters"]['field']);
				
			}
			if($v['control']["tagName"]=="ManagedFile") {
				$id=CAttributes::getIdsFromString($k);
				$result[$k]=$v;
				CAttributes::alterFileAttribute($result,$id[0],$id[1],$id[2],$id[3],$value_id,$v['control']["parameters"]['field']);
			}
			
		}
		return $result;
	}
	
	static function deleteProductAttributes($product_id) {
		
		
		$db=getdb();
		
		//$type_row=$db->getrow("select type,options from attributes where id='{$del_id}'");	
		$cl=$db->getOne("select attribute_group_ids from products inner join attribute_clusters on attribute_cluster_id=attribute_clusters.id where products.id='{$product_id}'");
		if(empty($cl)) {
			return ;
		}
		$real_tables=$db->query("select * from attribute_groups where use_real_table=1 and id in($cl)");
		$normal_attributes=$db->query("select * from attribute_groups where use_real_table=0 and id in($cl)");
		
	
		
		$table=CAttributes::getTableByType("image");
		
		foreach ($normal_attributes as $k=>$v) {
			if(!empty($v['attribute_ids'])) {
				$ids=$db->query("SELECT type,{$table}.id,".CAttributes::getAttributeTableFieldName().",php_data 
					FROM {$table} 
					INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
					WHERE attributes.id in({$v['attribute_ids']}) AND
					type IN (7,8)
					"
				);				
				CAttributes::deleteAttributeFiles($ids);
			}
		}
		
		$n=CAttributes::getAttributeTableFieldName();
		foreach ($real_tables as $k=>$v) {
			if(empty($v['attribute_ids'])||empty($v['real_table_name'])) {
				continue;
			}
			$product_ids=$db->getcol("select id from `{$v['real_table_name']}` where product_id='{$product_id}'");
			$ids=$db->query("SELECT type,id,$n,php_data 
					FROM attributes
					WHERE attributes.id in({$v['attribute_ids']}) AND
					type IN (7,8)
					"
				);
			CAttributes::deleteRealTableFiles($v['real_table_name'],$ids,$product_ids);
			$db->execute("delete from `{$v['real_table_name']}` where product_id='$product_id'");
		}
		
		
		
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->Execute("delete from {$table} where product_id='{$product_id}'");
		}
		
		/*$db=getdb();
		$table=CAttributes::getTableByType("image");
		$ids=$db->query("SELECT type,{$table}.id,options,use_real_table,real_table_name 
			FROM {$table} 
			INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
			WHERE product_id='{$product_id}' AND
			type IN (7,8)
			"
		);
		
		
		CAttributes::deleteAttributeFiles($ids);
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->execute("delete from {$table} where product_id='{$product_id}'");
		}*/
	}
	
	static function isEmptyValue($field_name,$v) {
		$ids=CAttributes::getIdsFromString($field_name);
		
		
		if(empty($ids)) {
			return true;
		}
		
		if("{$ids[3]}"=="") {	//sub_id e prazno =>
							// proverqwame dali imame danni za noviq red, ako e prazen, ne go zapisvame
			switch ($ids['type']) {
				case "enum":
				case "enumlist":
				case "bool":
					 {
					if("$v"!=""&&(int)$v!=0) {
						return false;	//ima stojnost
					}
					else {
						return true;
					}
					break;
				}
				default: {
					if("$v"!="") {
						return false;	//ima stojnost
					}
					else {
						return true;
					}
					break;
				}
			}
			
		}
		return false;
		
	}
	
	static function getEmptyRows($data,$con=array()) {
		$result=array();
		foreach ($data['data'] as $k=>$v) {
			$ids=CAttributes::getIdsFromString($k);
			
			
			if(empty($ids)) {
				continue;
			}
			if("{$ids[3]}"=="") {	//sub_id e prazno =>
								// proverqwame dali imame danni za noviq red, ako e prazen, ne go zapisvame
				$v=trim($v);
				switch ($ids['type']) {
					case "enumlist":
					case "enum":
					case "bool":
						 {
						if("$v"!=""&&(int)$v!=0) {
							$result["{$ids[0]}_{$ids[1]}"]=1;	//otbelqzwame grupata 4e ima stoinost
						}
						break;
					}
					default: {
						
						if("$v"!="") {
							$result["{$ids[0]}_{$ids[1]}"]=1;	//otbelqzwame grupata 4e ima stoinost
						}
						break;
					}
				}
				
			}
		}
		return $result;
	}
	
	static function splitRealData($con,&$data,$unset=true,$match=false) {
		$result=array();
		$db=getdb();
		$groups=array();
		
		foreach ($con['controls'] as $k=>$v) {
			if($v['is_order']) {
				continue;
			}
			if(!(int)$v['is_real_table']) {
				continue;
			}
			$ids=CAttributes::getIdsFromString($k);
			if(!isset($groups[$ids[1]])) {
				$aid=$db->getone("select attribute_ids from attribute_groups where id='{$ids[1]}'");
				$groups[$ids[1]]=$db->getassoc("select id,name from attributes where id in($aid)");				
			}
			//$name=$db->getone("select name from attributes where id='{$ids[2]}'");
			$name=$groups[$ids[1]][$ids[2]];
			if(!isset($result[$v["work_table"]])) {
				$result[$v["work_table"]]=array();
			}
			if("{$ids[3]}"=="") {
				$ids[3]=$ids[4];
			}
			$result[$v['work_table']][(int)$ids[3]][$name]=$data[$k];
			if(isset($v['control']["parameters"]["field"])) {
				$v['control']["parameters"]["field"]=$name;
			}
			$result[$v['work_table']]['con']['controls'][$k]=$v;
			if($match) {
				$result[$v['work_table']]['match'][$ids[3]][$name]=$k;
			}
			
			if($unset) {
				unset($data[$k]);
			}
			
		}
		return $result;
	}
	
	static function checkRequiredRow($con,$data) {
		$t=CAttributes::splitRealData($con,$data,false,true);
		$errors=array();
		if(!empty($t)) {
			foreach ($t as $tbl=>$v) {
				if(is_array($v['con'])) {
					$c=$v['con'];
					unset($v['con']);
				}
				$match=$v['match'];
				
				unset($v['match']);
				
				$last=array_values($v);
				$last_index=count($last)-1;
				$last=$last[$last_index];
				
				$has_val=false;
				foreach ($match[$last_index] as $k=>$v) {
					if(ControlValues::hasFileToProcess($c['controls'][$v]['control']['name'],$_FILES)) {
						$has_val=true;
						break;
					}
					if(!CAttributes::isEmptyValue($v,$last[$k])) {
						$has_val=true;
						break;
					}					
				}
				
				if($has_val) {
					foreach ($match[$last_index] as $k=>$v) {
						if(CAttributes::isEmptyValue($v,$last[$k])&&$c['controls'][$v]['row_required']) {
							$errors[$v]="Required field <b>{$c['controls'][$v]['control']["Label"]}</b> left empty";
						}
					}
				}	 		
			}
		}
		return $errors;
	}
	
	static function canReorderRealTable($table,$product_id,$new_order) {
		$db=getdb();
		$r=(int)$db->getone("select max(sub_id) from `{$table}` where product_id='{$product_id}'");
		if($new_order>$r) {
			return false;
		}
		return true;
	}
	
	static function canReorderTables($product_id,$group_id,$new_order) {
		$db=getdb();
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$r=$db->getone("select max(sub_id) from `{$table}` where product_id='{$product_id}' and group_id='{$group_id}'");
			if(is_null($r)) {	//w tazi tablica nqma zapis
				continue;
			}
			if($new_order>$r) {
				return false;
			}
		}
		return true;
	}
	
	static function checkRealTableOrderIntegrity($table,$product_id,$try_to_recover=true) {
		$db=getdb();
		
		$ok=false;
		while (!$ok) {
			$ok=true;
			$subs=$db->getcol("select sub_id from `{$table}` where product_id='{$product_id}' order by sub_id");
			if(empty($subs)) {
				return array();
			}
			$counter=0;
			foreach ($subs as $sub_id) {
				if($sub_id!=$counter) {
					if($try_to_recover) {
						$diff=$sub_id-$counter;
						$db->execute("update `{$table}` set sub_id=sub_id-($diff) where product_id='{$product_id}' and sub_id>$counter");
						$ok=false;
						break;
					}
					else {
						return array("SUB ID <b>{$counter}</b> is missing");
					}
				}
			}
		}
		return array();
	}
	static function checkTableOrderIntegrity($table,$product_id,$group_id,$try_to_recover=true) {
		$db=getdb();
		
		$ok=false;
		while (!$ok) {
			$ok=true;
			$subs=$db->getcol("select distinct sub_id from `{$table}` where product_id='{$product_id}' and group_id='{$group_id}' order by sub_id");
			if(empty($subs)) {
				return array();
			}
			$counter=0;
			foreach ($subs as $sub_id) {
				if($sub_id!=$counter) {
					if($try_to_recover) {
						$diff=$sub_id-$counter;
						$db->execute("update `{$table}` set sub_id=sub_id-($diff) where product_id='{$product_id}' and group_id='{$group_id}' and sub_id>$counter");
						$ok=false;
						break;
					}
					else {
						return array("SUB ID <b>{$counter}</b> is missing");
					}
				}
			}
		}
		return array();
	}
	
	
	static function reorderRealTable($table,$product_id,$sub_id,$new_order,$check_integrity=false) {
		$db=getdb();
		if($sub_id==$new_order) {
			return array();	//old_order=new_order
		}
		if($sub_id<$new_order) {
			$db->execute("update `{$table}` set sub_id=sub_id+1 where product_id='{$product_id}' and sub_id>'{$new_order}' order by sub_id desc");
			$db->execute("update `{$table}` set sub_id=? where product_id='{$product_id}' and sub_id='{$sub_id}'",array($new_order+1));
			$db->execute("update `{$table}` set sub_id=sub_id-1 where product_id='{$product_id}' and sub_id>'{$sub_id}' order by sub_id");
		}
		else {
			$db->execute("update `{$table}` set sub_id=sub_id+1 where product_id='{$product_id}' and sub_id>='{$new_order}' order by sub_id desc");
			$db->execute("update `{$table}` set sub_id='{$new_order}' where product_id='{$product_id}' and sub_id=?",array($sub_id+1));
			$db->execute("update `{$table}` set sub_id=sub_id-1 where product_id='{$product_id}' and sub_id>'{$sub_id}' order by sub_id");
		}
		if($check_ntegrity) {
			return CAttributes::checkRealTableOrderIntegrity($table,$product_id);
		}
		return array();
	}
	
	static function reorderTable($product_id,$group_id,$sub_id,$new_order,$check_integrity=false) {
		$db=getdb();
		if($sub_id==$new_order) {
			return array();	//old_order=new_order
		}
		
		
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			if($sub_id<$new_order) {
				$db->execute("update `{$table}` set sub_id=sub_id+1 where product_id='{$product_id}' and group_id='{$group_id}' and sub_id>'{$new_order}' order by sub_id desc");
				$db->execute("update `{$table}` set sub_id=? where product_id='{$product_id}' and group_id='{$group_id}' and sub_id='{$sub_id}'",array($new_order+1));
				$db->execute("update `{$table}` set sub_id=sub_id-1 where product_id='{$product_id}' and group_id='{$group_id}' and sub_id>'{$sub_id}' order by sub_id");
			}
			else {
				$db->execute("update `{$table}` set sub_id=sub_id+1 where product_id='{$product_id}' and group_id='{$group_id}' and sub_id>='{$new_order}' order by sub_id desc");
				$db->execute("update `{$table}` set sub_id='{$new_order}' where product_id='{$product_id}' and group_id='{$group_id}' and sub_id=?",array($sub_id+1));
				$db->execute("update `{$table}` set sub_id=sub_id-1 where product_id='{$product_id}' and group_id='{$group_id}' and sub_id>'{$sub_id}' order by sub_id");
			}
			if($check_ntegrity) {
				$errors= CAttributes::checkTableOrderIntegrity($table,$product_id,$group_id);
			}
		}
		return array();
	}
	
	static function reorderSubId($con,$postData=null) {
		if(is_null($postData)) {
			$postData=$_POST["order"];
		}
		if(!is_array($postData)||empty($postData)) {
			return array();
		}
		
		$errors=array();
		foreach ($postData as $k=>$v) {
			$v=trim($v);
			if("$v"=="") {
				continue;
			}
			$order=(int)$v;
			
			$ids=explode("_",$k);
			if(count($ids)!=4) {
				continue;
			}
			if(!isset($con['controls']["order_".$k])) {
				continue;
			}
			
			if("$v"!="{$order}"||(int)$order<1) {
				
				$errors[]="Invalid order in row <b>{$ids[3]}</b>";
			}
			$order--;
			$c=$con['controls']["order_".$k];
			if($c["is_real_table"]) {
				if(!CAttributes::canReorderRealTable($c["table"],$ids[0],$order)) {
					$errors[]="Your order  exceeds the max order in row <b>{$ids[3]}</b>";
				}
			}
			else {
				if(!CAttributes::canReorderTables($ids[0],$ids[1],$order)) {
					$errors[]="Your order exceeds the max order in row <b>{$ids[3]}</b>";
				}
			}
		}
		
		if(!empty($errors)) {
			return array(-1=>"Your record was <b>successfully saved</b> but these errors reised with your reordering:")+$errors;
		}
		
		
		
		foreach ($postData as $k=>$v) {
			$v=trim($v);
			if("$v"=="") {
				continue;
			}
			$order=(int)$v;
			$order--;
			$ids=explode("_",$k);
			if(count($ids)!=4) {
				continue;
			}
			if(!isset($con['controls']["order_".$k])) {
				continue;
			}
			$c=$con['controls']["order_".$k];
			if($c["is_real_table"]) {
				$errors=CAttributes::reorderRealTable($c["table"],$ids[0],$ids[3],$order,true);
				if(!empty($errors)) {
					return $errors;
				}
			}
			else {
				$errors=CAttributes::reorderTable($ids[0],$ids[1],$ids[3],$order,true);
				if(!empty($errors)) {
					return $errors;
				}
			}
		}
		return array();
		
	}
	
	static function saveAttributes($data,$product_id,$con) {
		$db=getdb();
		$errors=array();
		$has_image=false;
		
		$empty_rows=CAttributes::getEmptyRows($data);
		
		$img_ids=array();
		$file_ids=array();
		
		$real_tables=CAttributes::splitRealData($con,$data['data'],true,true);
		
		foreach ($data['data'] as $k=>$v) {
			$ids=CAttributes::getIdsFromString($k);
			
			if(empty($ids)) {
				continue;
			}
			if($ids[0]!=$product_id) {	//error
				continue;
			}
			
			$table=CAttributes::getTableByType($ids['type']);
			
			switch ($ids["type"]) {
				case "image": {
					$pref="";
					if("{$ids[3]}"=="") {	//sub_id e prazno =>
											// proverqwame dali imame danni za noviq red, ako e prazen, ne go zapisvame
											
						$ids[3]=$ids[4];
						$pref="_";
						if(!isset($empty_rows["{$ids[0]}_{$ids[1]}"])) {
							$t_id=CAttributes::createAttributeValueId($ids[0],$ids[1],$ids[2],$pref.$ids[3]);
							if(!ControlValues::hasFileToProcess($con['controls']["image_".$t_id]['control']['name'],$_FILES)) {
								continue;
							}
						}						
					}
					$id=(int)$db->getone("select id from {$table} where product_id=? and group_id=? and attribute_id=? and sub_id=?",array($ids[0],$ids[1],$ids[2],$ids[3]));
					if(!$id) {
						$db->execute("insert into {$table} (product_id,group_id,attribute_id,sub_id,value) VALUES (?,?,?,?,'')",array($ids[0],$ids[1],$ids[2],$ids[3]));
						$id=$db->get_id();
					}
					$ic=CAttributes::alterImageAttribute($con['controls'],$ids[0],$ids[1],$ids[2],$pref.$ids[3],$id);
					if($ic!==false) {
						$img_ids[$id]=$ic;
					}
					//$errors+=ControlValues::processManagedImages($id,$_FILES,$con['controls']);
					//die;
					break;
				}
				case "file": {
					$pref="";
					if("{$ids[3]}"=="") {	//sub_id e prazno =>
											// proverqwame dali imame danni za noviq red, ako e prazen, ne go zapisvame
											
						$ids[3]=$ids[4];
						$pref="_";
						if(!isset($empty_rows["{$ids[0]}_{$ids[1]}"])) {
							$t_id=CAttributes::createAttributeValueId($ids[0],$ids[1],$ids[2],$pref.$ids[3]);
							if(!ControlValues::hasFileToProcess($con['controls']["image_".$t_id]['control']['name'],$_FILES)) {
								continue;
							}
						}						
					}
					$id=(int)$db->getone("select id from {$table} where product_id=? and group_id=? and attribute_id=? and sub_id=?",array($ids[0],$ids[1],$ids[2],$ids[3]));
					if(!$id) {
						$db->execute("insert into {$table} (product_id,group_id,attribute_id,sub_id,value) VALUES (?,?,?,?,'')",array($ids[0],$ids[1],$ids[2],$ids[3]));
						$id=$db->get_id();
					}
					//$con['controls']["image_"]
					$ic=CAttributes::alterFileAttribute($con['controls'],$ids[0],$ids[1],$ids[2],$pref.$ids[3],$id);
					if($ic!==false) {
						$file_ids[$id]=$ic;
					}
					//$errors+=ControlValues::processManagedFiles($id,$_FILES,$con['controls']);
					break;
				}
								
				default: {
					if("{$ids[3]}"=="") {	//sub_id e prazno =>
											// proverqwame dali imame danni za noviq red, ako e prazen, ne go zapisvame
						if(!isset($empty_rows["{$ids[0]}_{$ids[1]}"])) {
							continue;
						}
						$ids[3]=$ids[4];
					}	
						
					$db->execute(
						"INSERT INTO {$table} (product_id,group_id,attribute_id,sub_id,value) VALUES (?,?,?,?,?)
		  					ON DUPLICATE KEY UPDATE value=?",
						array($ids[0],$ids[1],$ids[2],$ids[3],$v,$v)
					);
				}
			}
		}
		if(!empty($img_ids)) {
			foreach ($img_ids as $k=>$v) {
				
				$errors+=ControlValues::processManagedImages($k,$_FILES,array("image_".$k=>$v));
			}
		}
		if(!empty($file_ids)) {
			foreach ($file_ids as $k=>$v) {
				$errors+=ControlValues::processManagedFiles($k,$_FILES,array("file_".$k=>$v));
			}
		}
		
		
		if(!empty($real_tables)) {
			
			
			foreach ($real_tables as $table=>$v) {
				$c=$v["con"];
				unset($v["con"]);
				$match=$v['match'];
				unset($v['match']);
				$last_sub_id=count($v)-1;
				
				foreach ($v as $sub_id=>$d) {
					
					if($sub_id==$last_sub_id) {	//prowerqwame dali reda ima stojnost
						$has_val=false;
						foreach ($match[$last_sub_id] as $mk=>$mv) {
							if(!CAttributes::isEmptyValue($mv,$d[$mk])) {
								$has_val=true;
								break;
							}
							if(ControlValues::hasFileToProcess($c[$mv]['control']['name'],$_FILES)) {
								$has_val=true;
								break;
							}
						}
						
						if(!$has_val) {
							continue;
						}
					}
					
					$v2="`".implode("`=?,`", array_keys($d))."`=?";
					
					$d1=$d;
					$d["product_id"]=$product_id;
					$d["sub_id"]=$sub_id;
					$k1="`".implode("`,`",array_keys($d))."`";
					$v1 = array_fill(0, count($d), "?");
					$v1=implode(",",$v1);
					
					$d1=array_merge(array_values($d),$d1);
					
					
					
					$db->execute(
						"INSERT INTO {$table} ({$k1}) VALUES ({$v1})
		  					ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id),{$v2}",
						$d1
					);
					$n_id=$db->get_id();
					
					$tmpc=array();
					foreach ($match[$sub_id] as $mak=>$mav) {
						if(in_array($c['controls'][$mav]['control']["tagName"],array("ManagedImage","ManagedFile"))) {
							$tmpc[$mav]=$c['controls'][$mav];
							//$errors+=ControlValues::processManagedImages($k,$_FILES,array($mk=>$mv));
						}
					}					
					$errors+=ControlValues::processManagedImages($n_id,$_FILES,$tmpc);
					$errors+=ControlValues::processManagedFiles($n_id,$_FILES,$tmpc);
				}
			}
		}
		
		return $errors+CAttributes::reorderSubId($con);
				
	}
	
	static function deleteAttributeFiles($attribute_value_ids) {
		if(!empty($attribute_value_ids)) {
			foreach ($attribute_value_ids as $type_row) {
				$value_id=(int)$type_row['id'];
				switch($type_row['type']) {
					case 7: {
						@$sizes=unserialize($type_row['php_data']);
						$con=CAttributes::getFakeImageControl($value_id,$sizes['sizes']);
						ControlValues::deleteManagedImages($value_id,$con,false);
						break;
					}
					case 8: {
						$con=CAttributes::getFakeFileControl($value_id);
						ControlValues::deleteManagedFiles($value_id,$con,false);
						break;
					}
				}
			}
		}
	}
	
	static function deleteRealTableFiles($table,$attribute_value_ids,$table_ids) {
		if(!empty($attribute_value_ids)) {
			$n=CAttributes::getAttributeTableFieldName();
			foreach ($attribute_value_ids as $type_row) {
				
				foreach ($table_ids as $k=>$v) {
					switch($type_row['type']) {
						case 7: {
							@$sizes=unserialize($type_row['php_data']);
							$con=CAttributes::getFakeImageControl($v,$sizes,$table,$type_row[$n]);
							ControlValues::deleteManagedImages($v,$con,true);
							break;
						}
						case 8: {
							$con=CAttributes::getFakeFileControl($v,$table,$type_row[$n]);
							ControlValues::deleteManagedFiles($v,$con,true);
							break;
						}
					}
				}
			}
		}
	}
	
	static function deleteAttribute($del_id) {
		$db=getdb();
		
		//$type_row=$db->getrow("select type,options from attributes where id='{$del_id}'");	
		
		$cl=$db->Query("select id,attribute_ids,use_real_table,real_table_name from attribute_groups where find_in_set('{$del_id}',attribute_ids)");
		
		$rt=array();
		
		foreach ($cl as $k=>$v) {
			if((int)$v["use_real_table"]) {
				$rt[$v['id']]=$v["real_table_name"];
			}
			$f=explode(',',$v['attribute_ids']);
			$p=array_search($del_id,$f);
			if($p!==false) {
				unset($f[$p]);
				if(empty($f)) {
					CAttributes::deleteAttributeGroup($v['id']);					
				}
				else {
					$f=implode(',',$f);
					$db->Execute("update attribute_groups set attribute_ids=? where id='{$v['id']}'",array($f));
				}
			}
		}
		
		$table=CAttributes::getTableByType("image");
		$ids=$db->query("SELECT type,{$table}.id,".CAttributes::getAttributeTableFieldName().",php_data 
			FROM {$table} 
			INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
			WHERE attributes.id='{$del_id}' AND
			type IN (7,8)
			"
		);
		
		CAttributes::deleteAttributeFiles($ids);
		
		if(!empty($rt)) {
			foreach ($rt as $k=>$v) {
				$product_ids=$db->getcol("select distinct products.id from products inner join attribute_clusters on attribute_clusters.id=attribute_cluster_id where find_in_set('$k',attribute_group_ids)");
				if(!empty($product_ids)) {
					$product_ids=implode(",",$product_ids);
					$product_ids=$db->getcol("select id from `{$v}` where product_id in($product_ids)");
				}
				$ids=$db->query("SELECT type,id,".CAttributes::getAttributeTableFieldName().",php_data 
					FROM attributes
					WHERE attributes.id='{$del_id}' AND
					type IN (7,8)
					"
				);
				CAttributes::deleteRealTableFiles($v,$ids,$product_ids);
			}
		}
		
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->Execute("delete from {$table} where attribute_id='{$del_id}'");
		}
		
		
		$db->execute("delete from `attributes` where id='{$del_id}'");

	
	}
	
	static function deleteAttributeGroup($del_id) {
		$db=getdb();
		
		$real_table=(string)$db->getone("select real_table_name from attribute_groups where use_real_table=1 and id='$del_id'");
		//if(!empty($real_table)) {
		//	$real_table=",'".mysql_real_escape_string($real_table)."' as real_table";
		//}
		
		
		$db->execute("delete from `attribute_groups` where id='{$del_id}'");
		$table=CAttributes::getTableByType("image");
		$ids=$db->query("SELECT type,{$table}.id,".CAttributes::getAttributeTableFieldName().",php_data
			FROM {$table} 
			INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
			WHERE group_id='{$del_id}' AND
			type IN (7,8)
			"
		);
		CAttributes::deleteAttributeFiles($ids);
		
		if(!empty($real_table)) {
			$product_ids=$db->getcol("select distinct products.id from products inner join attribute_clusters on attribute_clusters.id=attribute_cluster_id where find_in_set('$del_id',attribute_group_ids)");
			if(!empty($product_ids)) {
				$product_ids=implode(",",$product_ids);
				$product_ids=$db->getcol("select id from `{$real_table}` where product_id in($product_ids)");
			}
			CAttributes::deleteRealTableFiles($real_table,$ids,$product_ids);
			if(!empty($product_ids)) {
				$db->execute("delete from `{$real_table}` where product_id in(".implode(',',$product_ids).")");
			}
		}
		
		
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->Execute("delete from {$table} where group_id='{$del_id}'");
		}
		$cl=$db->Query("select id,attribute_group_ids from attribute_clusters where find_in_set('{$del_id}',attribute_group_ids)");
		foreach ($cl as $k=>$v) {
			$f=explode(',',$v['attribute_group_ids']);
			$p=array_search($del_id,$f);
			if($p!==false) {
				unset($f[$p]);
				if(empty($f)) {
					CAttributes::deleteAttributeCluster($v['id']);
				}
				else {
					$f=implode(',',$f);
					$db->Execute("update attribute_clusters set attribute_group_ids=? where id='{$v['id']}'",array($f));
				}
			}
		}
	}
	
	static function deleteAttributeCluster($del_id) {
		$db=getdb();

		$db->execute("delete from `attribute_clusters` where id='{$del_id}'");
		$table=CAttributes::getTableByType("image");
		$ids=$db->query("SELECT type,{$table}.id,php_data 
			FROM {$table} 
			INNER JOIN attributes ON {$table}.attribute_id=attributes.id 
			WHERE product_id IN(
				SELECT id FROM products where attribute_cluster_id='{$del_id}'
			) AND
			type IN (7,8)
			"
		);
		CAttributes::deleteAttributeFiles($ids);
		
		foreach ($GLOBALS['attribute_value_tables'] as $table) {
			$db->Execute("delete from {$table} where product_id in(select id from products where attribute_cluster_id='{$del_id}')");
		}
	}
}
 
?>