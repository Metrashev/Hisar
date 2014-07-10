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
	'table'=>'mq_mail_items',
	'fields'=>'mq_mail_items.*',
	'order_fields'=>"mq_mail_items.id desc",
	'where'=>"mail_head_id='{$_GET['mail_head_id']}'",
	),
);

$xls=isset($_POST['dg_mail_items_xls'])||$_POST['dg_mail_itemsbt_print']==1?"_xls":"";

$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
if(isset($__custom_where)) {
	$ta_xml['DataTable']['where']=$__custom_where;
}
$unique_id="dg_mail_items";
$pagebar_id="pb_mail_items";
?>