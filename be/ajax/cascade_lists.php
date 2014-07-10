<?php

require_once(dirname(__FILE__)."/../../config/config.php");
require_once(dirname(__FILE__)."/../../lib/db.php");
require_once(dirname(__FILE__)."/../../lib/fe/AttributeCluster.php");

$attr_id=(int)$_REQUEST["id"];
if(!$attr_id) {
	return;
}

$db=getdb();
$row=$db->getRow("select * from attributes where id='{$attr_id}'");
@$php_data=unserialize($row["php_data"]);

$val=$_REQUEST["value"];



$params=array();
if(!empty($php_data["params"])) {
	eval("\$params={$php_data["params"]};");
}

$dep=array();
foreach ($params["depend"] as $k=>$v) {
	if(isset($_REQUEST["e{$v[0]}_{$v[1]}"])) {
		$dep["_#{$v[0]}_{$v[1]}#_"]=mysql_real_escape_string($_REQUEST["e{$v[0]}_{$v[1]}"]);
	}
}

$sql=str_replace(array_keys($dep),$dep,$php_data["sql"]);
if(isset($_REQUEST["debug"])) {
	echo "<pre>";
	print_r($_REQUEST);
	echo "</pre>";
	echo $sql;
	echo "<br />";
}
$q=$db->getAssoc($sql);
if(isset($_REQUEST["debug"])) {
	echo "<pre>";
	print_r($q);
	echo "</pre>";
}
if(isset($_REQUEST["search"])) {
	echo "<option value=''></option>";
	if("$val"=="0") {
		echo "<option value='0' selected>Empty</option>";
	}
	else {
		echo "<option value='0'>Empty</option>";
	}
}
else {
	echo "<option value='0'></option>";
}

foreach ($q as $k=>$v) {
	$selected="$val"=="$k"?" selected":"";
	$k=htmlspecialchars($k);
	$v=htmlspecialchars($v);
	echo <<<EOD
	<option value='{$k}'{$selected}>$v</option>
EOD;
}
?>