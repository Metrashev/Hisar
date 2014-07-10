<?php

$ta_xml=array(
'columns'=>array(
	'header'=>'',
	
),
/*'excel_options'=>array(
	'skip_columns'=>array("id","_h_id"),
	'add_index'=>true,
),
'hasExcelExport'=>true,*/
//'OnItemDataBound'=>'chCap',
//'OnOrderChange'=>'fn_order_change',
//'OnBeforeItemDataBound'=>"loadRowData",
'page_size'=>25,
'DataTable'=>array(
	'table'=>'translation',
	'fields'=>'translation.*',
	'order_fields'=>"translation.id desc",
	'where'=>"",
	),
);

$xls=isset($_POST['dg_translation_xls'])||$_POST['dg_translationbt_print']==1?"_xls":"";

$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
if(isset($__custom_where)) {
	$ta_xml['DataTable']['where']=$__custom_where;
}
$unique_id="dg_translation";
$pagebar_id="pb_translation";



class CTransTabList {
	function getValue($params) {
		$txt=$params[0];
		$row=$params[2]->DataSource->Rows[$params[1]];
		
		$txt = strip_tags($txt);
		return (mb_strlen($txt)<100) ? $txt : mb_substr(strip_tags($txt), 0, 100).'.....';
		if(!$row['is_html']){
			return $txt;
		} else {
			//return $txt=='' ? 'NO' : 'YES';
			$txt = strip_tags($txt);
			return (mb_strlen($txt)<100) ? $txt : mb_substr(strip_tags($txt), 0, 100).'.....';
		}

	}
}
?>