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
	'table'=>'static_pages',
	'fields'=>'static_pages.*',
	'order_fields'=>"static_pages.id desc",
	'where'=>"cid='{$_GET['n_cid']}'",
	),
);
$ta_xml['template']=(dirname(__FILE__). '/table.tpl');

?>