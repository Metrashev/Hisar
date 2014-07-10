<?php

$ta_xml=array(
'columns'=>array(
	'header'=>'',
	
),
'hasPrint'=>true,
'hasExcelExport'=>true,
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
	'table'=>'news_pages',
	'fields'=>'news_pages.*',
	'order_fields'=>"news_pages.id desc",
	'where'=>"cid='{$_GET['cid']}'",
	),
);
$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
$pagesize=25;
?>