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

$val=mysql_real_escape_string($_REQUEST["q"]);

$params=array();
if(!empty($php_data["params"])) {
	eval("\$params={$php_data["params"]};");
}

$ac_id=(int)$_REQUEST["ac_id"];
$c =  AttributeCluster::byId($ac_id);

$is_search=(int)$_REQUEST['search'];

foreach ($params["depend"] as $k=>$v) {
	if(isset($_REQUEST["e{$v[0]}_{$v[1]}"])) {
		if($is_search) {
			$vl=$_REQUEST["e{$v[0]}_{$v[1]}"];
			if("$vl"==="") {
				continue;
			}
			if(is_numeric($vl)&&$vl<0) {
				continue;
			}
		}
		$c->OnlyById[$v[0]]->OnlyById[$v[1]]->AddWhere("value='".mysql_real_escape_string($_REQUEST["e{$v[0]}_{$v[1]}"])."'");		
	}	
}
$c->OnlyById[(int)$_REQUEST['group_id']]->OnlyById[$attr_id]->AddWhere("value like '{$val}%'");
$fn=$c->OnlyById[(int)$_REQUEST['group_id']]->OnlyById[$attr_id]->fieldName;
$table=$c->OnlyById[(int)$_REQUEST['group_id']]->OnlyById[$attr_id]->AsTableName;

$w=$c->_buildSQLWhere();

$sql="select distinct {$table}.{$fn} from {$w["TABLES"]} where {$w["WHERE"]}";
$col=$db->getCol($sql);
if(!empty($col)) {
	echo implode("\n",$col);
}