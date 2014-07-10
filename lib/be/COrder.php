<?php

class COrder {
	
	public $objects=array();
	public $old_data=array();
	public $new_data=array();
	
	public $table="";
	
	
	function __construct($table) {
		$this->table=$table;
	}
	
	/**
	 * dobave pole koeto trqbwa da se podrejda
	 * format: array("dependent_field1","dependent_field2",...)
	 * 
	 */
	function add($field,$data) {
		$this->objects[$field]=$data;
	}
	
	/**
	 * zarejda nachalnite danni, t.e. danni koito idvat ot bazata
	 *
	 * @param unknown_type $data
	 */
	function setOldData($data) {
		$this->old_data=$data;
	}
	
	/**
	 * zarejda novi danni, t.e. danni koito idwat ot edit formata
	 * trqbwa da se izvika predi zapis na dannite v bazata.
	 * ako $keep_data==false tazi funkciq unsetva poletata za da ne vleznat v bazata
	 * @param unknown_type $data
	 */
	function setNewData(&$data,$keep_data=false) {
		
		
		$this->new_data=$data;
		if($keep_data) {
			return ;
		}
		foreach ($this->objects as $field=>$rows) {
			unset($data[$field]);
		}
	}
	
	function processOrders($new_id) {
		if(empty($this->objects)||!is_array($this->objects)) {
			return;
		}
		foreach ($this->objects as $field=>$data) {
			if(is_null($this->old_data[$field])) {	//now zapis
				echo "set new order";
				echo "<br />";
				$this->setNewOrder($field,$new_id);
				continue;
			}
			if((int)$this->old_data[$field]!=(int)$this->new_data[$field]) {
				echo "old<>new";
				echo "<br />";
				$this->updateOrder($field,$new_id);
				continue;
			}
			foreach ($data as $dep_field) {
				if("{$this->old_data[$dep_field]}"!="{$this->new_data[$dep_field]}") {
					echo "data differs";
					echo "<br />";
					$this->new_data[$field]=0;	//slagame go nai-otzad
					$this->updateOrder($field,$new_id);
				}
			}
		}		
	}
	
	function is_different_data($field) {
		foreach ($this->objects[$field] as $dep) {
			if("{$this->old_data[$dep]}"!="{$this->new_data[$dep]}") {
				return true;
			}
		}
		return false;
	}
	
	function prepareWhere($field,$data) {
		$where=array();
		$db=getdb();
		foreach ($this->objects[$field] as $dep_field) {
			$where[]="`{$dep_field}`='".mysql_real_escape_string($data[$dep_field])."'";
		}
		return implode(" AND ",$where);
	}
	
	function getMaxOrder($field,$where) {
		$db=getdb();
		return (int)$db->getone("select max($field) from {$this->table}{$where}");
	}
	
	function setNewOrder($field,$new_id) {
		$where=$this->prepareWhere($field,$this->new_data);
		if(!empty($where)) {
			$where1=" WHERE ".$where;
			$where=" AND ".$where;
		}
		$order=$this->fixOrder($field,$where1);
		$db=getdb();
		$db->execute("UPDATE {$this->table} set `{$field}`=`$field`+1 where {$field}>='{$order}'{$where}");
		$db->execute("UPDATE {$this->table} set `{$field}`='$order' where id='{$new_id}'");		
	}
	
	function fixOrder($field,$where,$dir=1) {
		$order=(int)$this->new_data[$field];		
		$max=$this->getMaxOrder($field,$where);
		
		if($order>$max) {
			$order=$max+$dir;
		}
		if(!$order) {
			$order=$max+1;
		}
		return $order;
	}
	
	function updateOrder($field,$new_id) {
		$old_where=$this->prepareWhere($field,$this->old_data);
		if(!empty($old_where)) {
			$old_where1=" WHERE ".$old_where;
			$old_where=" AND ".$old_where;
		}
		$new_where=$this->prepareWhere($field,$this->new_data);
		if(!empty($old_where)) {
			$new_where1=" WHERE ".$new_where;
			$new_where=" AND ".$new_where;
		}
		$diff=$this->is_different_data($field);

		var_dump($diff);
		$db=getdb();
		
		if($diff) {	//old_where<>new_where
			$old_order=(int)$this->old_data[$field];
			$db->execute("UPDATE {$this->table} set `{$field}`=`{$field}`-1 WHERE `$field`>'{$old_order}'{$old_where}");	//iztrivame ordera v stariq where
			
			$where=empty($new_where1)?" WHERE id!='{$new_id}'":$new_where1." AND id!='{$new_id}'";
			
			$order=$this->getMaxOrder($field,$where)+1;
			
			$db->execute("UPDATE {$this->table} set `{$field}`=`{$field}`+1 WHERE `{$field}`>='$order'{$new_where}");
			$db->execute("UPDATE {$this->table} set `{$field}`='$order' WHERE id='$new_id'");
			return;
		}
		$order=$this->fixOrder($field,$new_where1,0);
		if((int)$this->old_data[$field]<$order) {
			$db->execute("UPDATE `$this->table` SET $field=$field+1 WHERE $field>'$order'{$new_where} ORDER BY $field DESC");			
			$db->execute("UPDATE `$this->table` SET $field=$field-1 WHERE $field>'{$this->old_data[$field]}'{$new_where} ORDER BY $field ASC");
			$db->execute("UPDATE `$this->table` SET $field='$order' WHERE id='$new_id'");

		}
		else {
			$db->execute("UPDATE `{$this->table}` SET $field=$field+1 WHERE $field>=?{$new_where} ORDER BY {$field} DESC", array($order));			
			$db->execute("UPDATE `{$this->table}` SET $field=$field-1 WHERE $field>?{$new_where} ORDER BY {$field} ASC", array((int)$this->old_data[$field]+1));
			$db->execute("UPDATE `{$this->table}` SET $field='$order' WHERE id='$new_id'");		
		}
	}
}

?>