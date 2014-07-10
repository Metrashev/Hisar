<?php

define("MAX_ORDER",2147483647);

class COrder {
	private $table;
	private $order_field;
	private $where_fields;
	private $primary_key;
	private $db;
	
	
	
	function __construct($table,$order_field,$where_fields='',$primary_key='id') {
		$this->table=$table;
		$this->order_field=$order_field;
		$this->where_fields=$where_fields;
		$this->primary_key=$primary_key;
		$this->db=getDB();
	}
	
	/* static version */
	
	
	
	public static function removeOrder($table,$item_id,$where,$order_field="order_field",$id_field="id") {
		if(!empty($where)) {
			$where=" and ".$where;
		}
		
		$db=getdb();
		$o=(int)$db->getone("select `$order_field` from `$table` where $id_field='{$item_id}'");
		
		if($o>0&&$o<MAX_ORDER) {	//samo stoinosti nad 0 sa validni
			$db->execute("update `{$table}` set `$order_field`=`$order_field`-1 where `$order_field`>'$o' {$where}");
			$db->execute("update `{$table}` set `$order_field`=".MAX_ORDER." where $id_field='{$item_id}'");
		}
	}
	
	public static function setOrder($table,$item_id,$order,$where,$order_field="order_field",$id_field="id") {
		$where1=" where `$order_field`<'".MAX_ORDER."'";
		if(!empty($where)) {
			$where1.=" and ".$where;
			$where=" where ".$where;
			
		}
		else {
			
		}
		$db=getdb();
		$max=1+(int)$db->getone("select max(`{$order_field}`) from `{$table}` $where1");
		if($order>$max||$order<1) {
			$order=$max;
		}
		$db->execute("UPDATE `$table` set `$order_field`=`$order_field`+1 {$where1} and `$order_field`>='{$order}'");
		$db->execute("UPDATE `$table` set `$order_field`='$order' where `$id_field`='{$item_id}'");
	}
	
	/* static version */
	
	function get_item_order($item_id) {
		return intval($this->db->getOne("select {$this->order_field} from {$this->table} where {$this->primary_key}={$item_id}"));
	}
	
	function get_last_order_index($skip_id=0,$str_where='') {
		if($str_where!='') {
			$str_where=' and '.$str_where;
		}
		
		return intval($this->db->getOne("select max({$this->order_field}) from {$this->table} where id!={$skip_id} {$str_where}"));
	}
	
	private function getWhere(&$str_new_where='',$str_old_where='',$prefix='') {
		if($str_old_where=='')
			$str_where=$this->where_fields;
		if($str_new_where=='')
			$str_new_where=$this->where_fields;
		if($str_where!='')
			$str_where=$prefix.strtolower($str_where);
		if($str_new_where!='')
			$str_new_where=$prefix.strtolower($str_new_where);
		return $str_where;
	}
	
	function delete_order($item_id,$str_old_where='') {
		$s='';
		$str_where=$this->getWhere($s,$str_old_where,' and ');
		$c_order=$this->db->getone("select {$this->order_field} from {$this->table} where {$this->primary_key}='{$item_id}'");
		$this->db->Execute("update {$this->table} set {$this->order_field}={$this->order_field}-1 where {$this->order_field}>{$c_order} {$str_where}");
	}
	
/**
 * Zadawa order na element.
 * @item_id int id na elementa
 * @new_order=0 int order na elementa.Ako 0 togava go slaga posleden,-1 ->slaga go purvi
 * @str_new_where='' string where clausa za novata grupa v koqto 6te u4astwa elementa NE SADURJA 'WHERE'
 * @str_old_where='' string where clausa w koeto v momenta prisustwa elementa t.e. teku6ta grupa na elementa NE SADURJA 'WHERE'
 * $str_old_where i $str_new_where sa nujni ako elementa se mesti ot 1 grupa v druga
 * @return void
 */
	
	function set_item_order($item_id,$new_order=0,$str_new_where='',$str_old_where='') {
		if($str_old_where=='')
			$str_where=$this->where_fields;
		if($str_new_where=='')
			$str_new_where=$this->where_fields;
		$current_order=$this->get_item_order($item_id);
		$max_order=-1;
		
		if($new_order==0) {
			if($str_new_where=='') {
				$max_order=$this->get_last_order_index($item_id,$str_where);
				
			}
			else {
				$max_order=$this->get_last_order_index($item_id,$str_new_where);
			}
			$new_order=$max_order+1;
		}
		
		if($str_where!='')
			$str_where=' and '.strtolower($str_where);
		if($str_new_where!='')
			$str_new_where=' and '.strtolower($str_new_where);
			
		
		
		if($new_order==-1) {
			$new_order=1;
		}
		
		if($current_order==$new_order&&$str_where==$str_new_where)
			return;
		if($current_order<$new_order) {
			$sign=-1;
			$l=$current_order;
			$r=$new_order;
		}
		else {
			$sign=1;
			$l=$new_order;
			$r=$current_order;
		}
		if($str_where==$str_new_where) {
			if($current_order==0) {
//				echo "update {$this->table} set {$this->order_field}={$this->order_field}+1 where {$this->order_field}>={$new_order} {$str_where}";
//				echo "<br>";
				
				$this->db->Execute("update {$this->table} set {$this->order_field}={$this->order_field}+1 where {$this->order_field}>={$new_order} {$str_where}");
			}
			else {
//				echo "update {$this->table} set {$this->order_field}={$this->order_field}+({$sign}) where {$this->order_field} between {$l} and {$r} {$str_where}";
//				echo "<br>";
				
				$this->db->Execute("update {$this->table} set {$this->order_field}={$this->order_field}+({$sign}) where {$this->order_field} between {$l} and {$r} {$str_where}");
			}
		}
		else {
//			echo "case 2";
//			echo "<br>";
//			
//			echo "update {$this->table} set {$this->order_field}={$this->order_field}+1 where {$this->order_field} >={$new_order} {$str_new_where}";
//			echo "<br>";
			
			$this->db->Execute("update {$this->table} set {$this->order_field}={$this->order_field}-1 where {$this->order_field} between 2 and {$new_order} {$str_new_where}");
			$this->db->Execute("update {$this->table} set {$this->order_field}={$this->order_field}+1 where {$this->order_field} >{$new_order} {$str_new_where}");
		}
		/*if($max_order>-1) {
			if(abs($new_order-$current_order)>1) {
				$new_order=$max_order;
			}
		}*/
		
		$this->db->Execute("update {$this->table} set {$this->order_field}={$new_order} where {$this->primary_key}={$item_id}");
	}
	
	/**
 * Zadawa order na element=999.
 * @item_id int id na elementa
 * @str_new_where='' string where clausa za novata grupa v koqto 6te u4astwa elementa NE SADURJA 'WHERE'
 * @str_old_where='' string where clausa w koeto v momenta prisustwa elementa t.e. teku6ta grupa na elementa NE SADURJA 'WHERE'
 * $str_old_where i $str_new_where sa nujni ako elementa se mesti ot 1 grupa v druga
 * @return void
 */

	//function removeOrder($item_id,$str_new_where,$str_old_where) {
	//	$this->set_item_order($item_id,999,$str_new_where,$str_old_where);
	//}
	
	function move_order_up($item_id,$str_new_where='',$str_old_where='') {
		$c_order=$this->get_item_order($item_id);
		$str_old_where=$this->getWhere($str_new_where,$str_old_where,' and ');
		$prev_order=intval($this->db->getOne("select {$this->primary_key} from {$this->table} where {$this->order_field}<{$c_order} {$str_old_where} order by {$this->order_field} desc"));
		if($prev_order>0) {
			$order=intval($this->db->getOne("select {$this->order_field} from {$this->table} where {$this->primary_key}={$prev_order}"));
			$this->db->Execute("Update {$this->table} set {$this->order_field}={$order} where {$this->primary_key}={$item_id}");
			$this->db->Execute("Update {$this->table} set {$this->order_field}={$c_order} where {$this->primary_key}={$prev_order}");
		}
	}
	
	function move_order_down($item_id,$str_new_where='',$str_old_where='') {
		$c_order=$this->get_item_order($item_id);
		$str_old_where=$this->getWhere($str_new_where,$str_old_where,' and ');
		$next_order=intval($this->db->getOne("select {$this->primary_key} from {$this->table} where {$this->order_field}>{$c_order} {$str_old_where} order by {$this->order_field}"));
		if($next_order>0) {
			$order=intval($this->db->getOne("select {$this->order_field} from {$this->table} where {$this->primary_key}={$next_order}"));
			$this->db->Execute("Update {$this->table} set {$this->order_field}={$order} where {$this->primary_key}={$item_id}");
			$this->db->Execute("Update {$this->table} set {$this->order_field}={$c_order} where {$this->primary_key}={$next_order}");
		}
	}
}

?>