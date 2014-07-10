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
'page_size'=>500,
'DataTable'=>array(
	'table'=>'categories inner join news_pages on categories.id=news_pages.cid',
	'fields'=>"categories.id,'news' as table_name,categories.level,0 as def,news_pages.title as st,news_pages.id as spid, categories.value,categories.id as cid,subtitle,due_date,is_visible",
	'order_fields'=>"categories.l,due_date desc, ifnull(news_pages.id,0)",
	'where'=>"categories.id>1 and categories.level>0",
	),
);
$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
?>