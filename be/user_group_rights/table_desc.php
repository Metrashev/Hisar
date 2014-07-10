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
	'table'=>'user_group_rights',
	'fields'=>'user_group_rights.*',
	'order_fields'=>"user_group_rights.id desc",
	'where'=>"",
	),
);

$xls=isset($_POST['dg_user_group_rights_xls'])||$_POST['dg_user_group_rightsbt_print']==1?"_xls":"";

$ta_xml['template']=(dirname(__FILE__). '/table.tpl');
if(isset($__custom_where)) {
	$ta_xml['DataTable']['where']=$__custom_where;
}
$unique_id="dg_user_group_rights";
$pagebar_id="pb_user_group_rights";
?>