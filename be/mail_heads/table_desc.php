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
	'table'=>'mq_mail_heads',
	'fields'=>'mq_mail_heads.*',
	'order_fields'=>"mq_mail_heads.id desc",
	'where'=>"",
	),
);

$xls=isset($_POST['dg_mail_heads_xls'])||$_POST['dg_mail_headsbt_print']==1?"_xls":"";

$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
if(isset($__custom_where)) {
	$ta_xml['DataTable']['where']=$__custom_where;
}
$unique_id="dg_mail_heads";
$pagebar_id="pb_mail_heads";
?>