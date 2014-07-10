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
	'table'=>'gallery',
	'fields'=>'gallery.*',
	'order_fields'=>"gallery.order_field",
	'where'=>"cid='{$_GET['cid']}'",
	),
);
$ta_xml['template']=(dirname(__FILE__). '/table.tpl');

if(isset($_GET['page_id'])&&!isset($_GET['all'])) {
	$ta_xml['DataTable']['where'].=" and page_id='".((int)$_GET['page_id'])."'";
}

if(isset($__custom_where)) {
	$ta_xml['DataTable']['where']=$__custom_where;
}
$unique_id="dg_gallery";
$pagebar_id="pb_gallery";
?>