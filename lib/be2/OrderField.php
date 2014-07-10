<?php

class FEAttributeOrderField extends FEAttributeInt {
	
/*	function Validate($Value){
		$r=parent::Validate($value);
		if(!empty($r)) {
			return $r;
		}
		$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
		$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("{$this->AsTableName}.id={$row["id"]}");
		$w=$c->_buildSQLWhere();
	}
*/	
	
	function getDependentTypes() {
		$a=array();
		foreach ($GLOBALS['attribute_type_tables'] as $k=>$v) {
			if($v=="attribute_val_int"&&$k!=19) {
				$a[]=$k;
			}
		}
		return implode(",",$a);
	}
	
	function deleteOrder($where,$row) {
		$db=getdb();
		if(!empty($where)) {
			$where=" AND ";
		}
		$db->execute("delete from attribute_val_int where product_id='{$row['product_id']}' and group_id='{$this->Group->Id}' and sub_id='{$row['sub_id']}' and attribute_id='{$this["id"]}'");
		
		$db->execute("update attribute_val_int set value=value-1 where attribute_id='$id' $where");
	}
	
	function getParameters() {
		$d=array();
		if(!empty($this->PHPData)&&!empty($this->PHPData["params"])) {
			eval("\$d={$this->PHPData["params"]};");			
		}
		return $d;
	}
	
	function getDataValues() {
		$d=$this->getParameters();
		$old_data=array();
		$new_data=array();
		$c = $this->Group->Cluster;
		
		foreach ($c as $gid=>$group){
			/** @var $group AttributeGroup */
			if(!isset($d[$gid])) continue;
			if($group->IsTable) continue;


			foreach ($group as $aid=>$attribute){
				if(in_array($aid,$d[$gid]) /* if this field depends on this attrib*/){
				//	if("{$attribute->OldValue}"!="{$attribute->NewValue}") {
						if($group->UseRealTable){
							$old_data[]=array($gid,$aid,$c->ClusterLoadData[$gid][0][$attribute->fieldName]);
						} else {
							$old_data[]=array($gid,$aid,$c->ClusterLoadData[$gid][0][$aid]["value"]);
						}
						$new_data[]=array($gid,$aid,$c->ClusterSaveData[$gid][0][$aid]);
				//	}
				}
			}
		}
		
		return array($old_data,$new_data);
		
	}
	
	
	function DBSaveOnAfter($ClusterData){
	//	return ;
		
		
		$row = $ClusterData[$this->Group->Id][$this->Group->CurrentSubId];
		$OldValue = $this->Value;
		$NewValue = $row[$this->Id];
		
		if("{$OldValue}"=="{$NewValue}") {			
			return;
		}
		
		if($this->Group->IsTable) {	//za tablici ne raboti
			return;
		}
		
		

		$where=array();
		$db=getdb();
		
		$data=$this->getDataValues();
		$old_data=$data[0];
		$new_data=$data[1];
		//--
		
		if("$OldValue"!="") {
			$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
			foreach ($old_data as $r) {
				$c->OnlyById[$r[0]]->OnlyById[$r[1]]->addWhere("value={$r[2]}");
			}
		
			$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("value>'{$OldValue}'");
			$w = $c->_buildSQLWhere();
			
			$f = "{$this->AsTableName}.{$this->fieldName}";
			
			$w = "UPDATE {$w['TABLES']} SET $f = $f -1 WHERE {$w['WHERE']}";
			$db->execute($w);
		}
		/* ---- */
		
		$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
		foreach ($new_data as $r) {
			$c->OnlyById[$r[0]]->OnlyById[$r[1]]->addWhere("value={$r[2]}");
		}
		
		$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("value>={$NewValue}");
		$w = $c->_buildSQLWhere();
		
		
		$f = "{$this->AsTableName}.{$this->fieldName}";
		$w = "UPDATE {$w['TABLES']} SET $f = $f +1 WHERE {$w['WHERE']}";
		$db->execute($w);
		
		/*
		$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
		$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("{$this->AsTableName}.id={$row["id"]}");
		$w=$c->_buildSQLWhere();
		
		$w = "UPDATE {$w['TABLES']} SET $f = '{$this->NewValue}' WHERE {$w['WHERE']}";
		*/
		
		$w = "UPDATE {$this->tableName} SET {$this->fieldName} = '{$NewValue}' WHERE id={$this->RecordId[0]}";
		$db->execute($w);		
	}
	
	function DBDelete($product_id, $sub_id){
		$row=$this->DBGet($product_id,$sub_id);
		
		$this->Group->Cluster->MainGroup['id']->addWhere("value=$product_id");
		$p = $this->Group->Cluster->getAllProducts(1);
		foreach ($p as $p);
		
		$data=$this->getDataValues();
		$old_data=$data[0];
		$new_data=$data[1];
		
		$row = $ClusterData[$this->Group->Id][$this->Group->CurrentSubId];
		$OldValue = $this->Value;
		
		$NewValue = $row[$this->Id];
		$db=getdb();
		
		$new_data=array();
		$d=$this->getParameters();
		
		/*
		if("$OldValue"!="") {
			$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
			foreach ($old_data as $r) {
				$c->OnlyById[$r[0]]->OnlyById[$r[1]]->addWhere("value={$r[2]}");
			}
			
			$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("value>'{$OldValue}'");
			$w = $c->_buildSQLWhere();
			
			$f = "{$this->AsTableName}.{$this->fieldName}";
			
			$w = "UPDATE {$w['TABLES']} SET $f = $f -1 WHERE {$w['WHERE']}";
			$db->execute($w);
		}
		*/
		$c = AttributeCluster::byId($this->Group->Cluster->AttributeClusterId);
		foreach ($old_data as $r) {
			$c->OnlyById[$r[0]]->OnlyById[$r[1]]->addWhere("value={$r[2]}");
		}
		
		$c->OnlyById[$this->Group->Id]->OnlyById[$this->Id]->addWhere("value>{$OldValue}");
		$w = $c->_buildSQLWhere();
		
		$f = "{$this->AsTableName}.{$this->fieldName}";
		
		$w = "UPDATE {$w['TABLES']} SET $f = $f -1 WHERE {$w['WHERE']}";
		$db->execute($w);
		
		parent::DBDelete($product_id, $sub_id);
				
	} 
}

?>