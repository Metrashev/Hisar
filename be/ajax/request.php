<?php
require_once('../libCommon.php');

//$table=CAttributes::getTableByType("autocomplete");
$table="attribute_val_str";

$db=getdb();
$q=mysql_real_escape_string($_REQUEST['q']);

//$p=$_REQUEST;
$attribute_id=mysql_real_escape_string($_REQUEST["__attribute_id"]);

//eval("\$a={$p};");
/*foreach ($a as $k=>$v) {
	echo "{$k}={$v}\n";
}*/
/*
echo "<pre>";
print_r($a);
echo "</pre>";
*/

/*echo $_SERVER['REQUEST_METHOD']."\n";
foreach ($_REQUEST as $k=>$v) {
	echo "{$k}={$v}\n";
}
echo "---\n";*/
$SQL="select distinct value from {$table} where attribute_id='{$attribute_id}' and value like '{$q}%'";
$all=$db->Query($SQL);
if(!empty($all)) {
	foreach ($all as $v) {
		echo $v['value']."\n";
	}
}
?>