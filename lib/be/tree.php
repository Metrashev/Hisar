<?php
//require_once('db.php');

class CURLTree {
  
  private $TableName='';
  
  public $create_node_handlers = array();
  public $delete_node_handlers = array();
  
  public $pids=array();
  
  protected $db;	/* @var $db CDB*/
  
  function __construct($tableName) {
  	$this->db=getdb();
  	$this->TableName=$tableName;
  }
  
  public function getTableName() {
    if ($this->TableName=='') throw new Exception('TableName is not set!');
    return $this->TableName;
  }
  
  public function setTableName($value) {
    $value = (string)$value;
    if ($value=='') throw new Exception('Invalid TableName supplied!');
    if ($this->TableName === $value) return;
    
    $this->TableName = $value;
    $this->get_tree_root();
  }
  
  private function make_tree_root() {
   	$SQL = "INSERT INTO {$this->TableName} (`pid`, `l`, `weight`, `level`) VALUES (0, 1, 0, 0)";
    
    $this->db->execute($SQL);
    $id = (int)$this->db->get_id();
    
//    $this->raiseCreateEvent(array($id));
    
    return $this->get_node_by_id($id);
  }
  
  function get_tree_root() {
    $SQL = "SELECT * FROM {$this->TableName} WHERE pid = 0";
    $row = $this->db->getRow($SQL);
    
    if (empty($row)) {
      $row = $this->make_tree_root();
    }
    
    return $row;
  }
  
  function find_language($cid) {
  	$row=$this->get_tree_root();
  	$r_id=(int)$row['id'];
  	$db=getdb();
  	$node=$db->getRow("select id,pid,language_id from {$this->TableName} where id={$cid}");
  	while((int)$node['pid']!=$r_id&&!empty($node['id'])) {
  		$node=$db->getRow("select id,pid,language_id from {$this->TableName} where id={$node['pid']}");
  	}
  	return $node['language_id'];
  }
  
  function add_node($parent_id=0,$value='') {
    if ($parent_id==0) {
      $row = $this->get_tree_root();
      if ($row['id']>0) $parent_id = $row['id'];
      else return false;
    }
    
    $SQL = "SELECT id, l, level FROM {$this->TableName} WHERE id='$parent_id'";
    $row = $this->db->getRow($SQL);
    
    if (!empty($row)) {
      $id = $row['id'];
      $l = $row['l'];
      $level = $row['level'];
      
      $SQL = "UPDATE {$this->TableName} SET weight=weight+1 WHERE l<=$l AND l+weight>=$l";
      $this->db->query($SQL);
      
      $SQL = "UPDATE {$this->TableName} SET l=l+1 WHERE l>{$l}";
      $this->db->query($SQL);
      
      $SQL = "INSERT INTO {$this->TableName} (`l`, `weight`, `pid`, `level`,`value`,`visible`) VALUES (?, 0, ?, ?,?,1)";
      $this->db->query($SQL,array(($l+1),$id,($level+1),$value));
      $ins_id = (int)$this->db->get_id();
      $this->db->Query("update {$this->TableName} set language_id='".$this->find_language($ins_id)."' where id={$ins_id}");
   //   $this->raiseCreateEvent(array($ins_id));
      return $ins_id;
    }
  }
  
  function insertAfter($node_id,$value='') {
    if ($node_id<1) {
      $this->add_node(0,$value);
    }
    
    $SQL = "SELECT * FROM {$this->TableName} WHERE id='$node_id'";
    $row = $this->db->getRow($SQL);
    
    if (!is_null($row)) {
      $id = $row['id'];
      $pid = $row['pid'];
      $l = $row['l'];
      $weight = $row['weight'];
      $level = $row['level'];
      
      if ($pid==0) return false;
      
      $SQL = "UPDATE {$this->TableName} SET weight=weight+1 WHERE l<$l AND l+weight>=$l";
	  $this->db->query($SQL);
      
      $SQL = "UPDATE {$this->TableName} SET l=l+1 WHERE l>".($l+$weight);
      $this->db->query($SQL);
      
      $SQL = "INSERT INTO {$this->TableName} (`l`, `weight`, `pid`, `level`,`value`,`visible`) VALUES (?, 0, ?, ?,?,1)";
      $this->db->query($SQL,array($l + $weight + 1,$pid,$level,$value));
      $ins_id = (int)$this->db->get_id();
      $this->db->Query("update {$this->TableName} set language_id='".$this->find_language($ins_id)."' where id={$ins_id}");
  //    $this->raiseCreateEvent(array($ins_id));
      return $ins_id;
    }
  }
  
  function delete_node($id) {
    $SQL = "SELECT l, weight FROM {$this->TableName} WHERE id='$id'";
    $row = $this->db->getRow($SQL);
    
    if (!is_null($row)) {
      if ($row["l"]==1) {
        return array();
      }
      
      $l = (int)$row["l"];
      $weight = (int)$row["weight"];
      $right = $l + $weight;
      $total_offset = $weight + 1;
      
      $SQL = "SELECT id FROM {$this->TableName} WHERE (l BETWEEN {$l} AND {$right})";
      $delete_ids = $this->db->getCol($SQL);
      
      $SQL = "DELETE FROM {$this->TableName} WHERE (l BETWEEN {$l} AND {$right})";
      $this->db->query($SQL);
      
      $SQL = "UPDATE {$this->TableName} SET weight=weight-{$total_offset} WHERE ($l BETWEEN l AND l+weight)";
      $this->db->query($SQL);
      
      $SQL = "UPDATE {$this->TableName} SET l=l-{$total_offset} WHERE l>{$l}";
      $this->db->query($SQL);
      
   //   $this->raiseDeleteEvent($delete_ids);
      return $delete_ids;
    } else {
      return array();
    }
  }
  
  function moveAsFirstChildOf($node_id, $destination) {
    $node_1 = $this->get_node_by_id($node_id, 'l, weight, level');
    $node_2 = $this->get_node_by_id($destination, 'l, weight, level');
    
    if ($node_1['l'] <= $node_2['l'] && $node_2['l'] <= $node_1['l']+$node_1['weight']) {
      return false;
    }
    
    $min_level = $this->getIntersectionLevel($node_id, $destination);
    $max_key = $this->db->getOne("SELECT l+weight FROM {$this->TableName} WHERE pid=0") + 1;
    
    $SQL = "UPDATE {$this->TableName} SET
      weight = weight - {$node_1['weight']} - 1
      WHERE (level>{$min_level})
      AND (l<{$node_1['l']})
      AND (l+weight>={$node_1['l']})";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET
      weight = weight + {$node_1['weight']} + 1
      WHERE (level>{$min_level})
      AND (l<={$node_2['l']})
      AND (l+weight>={$node_2['l']})";
    $this->db->query($SQL);
    
    /*
    offset_1 -> с колко места се мести node_1, за да стане 1ви child на node_2
    offset_2 -> с колко места да се преместят позициите м/у node_1 и node_2, за да се възстанови дървото
    
    WHERE_1 -> този интервал винаги обхваща node_1
    WHERE_2 -> това е интервалът от nodes м/у node_1 и node_2, които тр. да се преместят на мястото на node_1
    */
    if ($node_1['l']<$node_2['l']) {
      $offset_1 = $node_2['l'] - $node_1['l'] - $node_1['weight'];
      $offset_2 = 0 - ($node_1['weight'] + 1);
      
      $WHERE_1 = "WHERE (l BETWEEN ".$node_1['l']." AND ".($node_1['l']+$node_1['weight']).")";
      $WHERE_2 = "WHERE (l BETWEEN ".($node_1['l']+$node_1['weight']+1)." AND ".($node_2['l']).")";
    } else {
      $offset_1 = $node_2['l'] - $node_1['l'] + 1;
      $offset_2 = $node_1['weight'] + 1;
      
      $WHERE_1 = "WHERE (l BETWEEN ".$node_1['l']." AND ".($node_1['l']+$node_1['weight']).")";
      $WHERE_2 = "WHERE (l BETWEEN ".($node_2['l']+1)." AND ".($node_1['l']-1).")";
    }
    
    $SQL = "UPDATE {$this->TableName} SET l = l + {$offset_1} + {$max_key}, level = level + ".($node_2['level']+1-$node_1['level'])." {$WHERE_1}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET l = l + {$offset_2} {$WHERE_2}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET l = l - {$max_key} WHERE l >= {$max_key}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET pid={$destination} WHERE id={$node_id}";
    $this->db->query($SQL);
    $this->db->Query("update {$this->TableName} set language_id='".$this->find_language($node_id)."' where id={$node_id}");
  }
  
  function moveAfter($node_id, $destination) {
  	if(!$destination) {
  		$destination=$this->get_tree_root();
  		$destination=(int)$destination['id'];
  	}
    $node_1 = $this->get_node_by_id($node_id, 'l, weight, level');
    $node_2 = $this->get_node_by_id($destination, 'l, weight, level, pid');
    
    if ($node_1['l'] <= $node_2['l'] && $node_2['l'] <= $node_1['l']+$node_1['weight']) {
      return false;
    }
    
    if ($node_1['level']==0 || $node_2['level']==0) {
      return false;
    }
    
    $min_level = $this->getIntersectionLevel($node_id, $destination);
    $max_key = $this->db->getOne("SELECT l+weight FROM {$this->TableName} WHERE pid=0") + 1;
    
    if ($node_2['level']==$min_level) $min_level--;
    
    $SQL = "UPDATE {$this->TableName} SET
      weight = weight - {$node_1['weight']} - 1
      WHERE (level>{$min_level})
      AND (l<{$node_1['l']})
      AND (l+weight>={$node_1['l']})";
    $this->db->query($SQL);// echo "$SQL<br>";
    
    $SQL = "UPDATE {$this->TableName} SET
      weight = weight + {$node_1['weight']} + 1
      WHERE (level>{$min_level})
      AND (l<{$node_2['l']})
      AND (l+weight>={$node_2['l']})";
    $this->db->query($SQL);// echo "$SQL<br>";
    
    /*
    offset_1 -> с колко места се мести node_1, за да стане съсед на node_2
    offset_2 -> с колко места да се преместят позициите м/у node_1 и node_2, за да се възстанови дървото
    
    WHERE_1 -> този интервал винаги обхваща node_1
    WHERE_2 -> това е интервалът от nodes м/у node_1 и node_2, които тр. да се преместят на мястото на node_1
    */
    
    if ($node_1['l']<=$node_2['l']+$node_2['weight']) {
      
      $offset_1 = $node_2['l'] - $node_1['l'] - $node_1['weight'] + $node_2['weight'];
      $offset_2 = 0 - ($node_1['weight'] + 1);
      
      $WHERE_1 = "WHERE (l BETWEEN ".$node_1['l']." AND ".($node_1['l']+$node_1['weight']).")";
      $WHERE_2 = "WHERE (l BETWEEN ".($node_1['l']+$node_1['weight']+1)." AND ".($node_2['l']+$node_2['weight']).")";
      
    } else {
      
      $offset_1 = 0 - ($node_1['l'] - $node_2['l'] - $node_2['weight'] - 1);
      $offset_2 = $node_1['weight'] + 1;
      
      $WHERE_1 = "WHERE (l BETWEEN ".$node_1['l']." AND ".($node_1['l']+$node_1['weight']).")";
      $WHERE_2 = "WHERE (l BETWEEN ".($node_2['l']+$node_2['weight']+1)." AND ".($node_1['l']-1).")";
      
    }
    
    $SQL = "UPDATE {$this->TableName} SET l = l + {$offset_1} + {$max_key}, level = level + ".($node_2['level']-$node_1['level'])." {$WHERE_1}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET l = l + {$offset_2} {$WHERE_2}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET l = l - {$max_key} WHERE l >= {$max_key}";
    $this->db->query($SQL);
    
    $SQL = "UPDATE {$this->TableName} SET pid={$node_2['pid']} WHERE id={$node_id}";
    $this->db->query($SQL);
    $this->db->Query("update {$this->TableName} set language_id='".$this->find_language($node_id)."' where id={$node_id}");
  }
  
  function getIntersectionLevel($node_1, $node_2) {
    $node_1 = $this->get_node_by_id($node_1, 'l, level');
    $node_2 = $this->get_node_by_id($node_2, 'l, level');
    
    $SQL = "SELECT MAX(level)
FROM {$this->TableName}
WHERE (level<={$node_1['level']})
AND (level<={$node_2['level']})
AND (l<={$node_1['l']})
AND (l<={$node_2['l']})
AND (l+weight>={$node_1['l']})
AND (l+weight>={$node_2['l']})";
    
    return (int)$this->db->getOne($SQL);
  }
  
  function get_node_by_id($id, $fields='*') {
    $SQL = "SELECT {$fields} FROM {$this->TableName} WHERE id = '$id'";
    return $this->db->getRow($SQL);
  }
  
  function get_tree_items() {
    $SQL = "SELECT * FROM {$this->TableName} ORDER BY l";
    $struct = $this->db->getAssoc($SQL);
    
    //if (is_null($struct)) $struct = array();
    
    return $struct;
  }
  
  function raiseCreateEvent($id_list) {
    foreach ($this->create_node_handlers as $handler) {
      if (is_callable($handler))
        call_user_func($handler, $id_list);
    }
  }
  
  function raiseDeleteEvent($id_list) {
    foreach ($this->delete_node_handlers as $handler) {
      if (is_callable($handler))
        call_user_func($handler, $id_list);
    }
  }
  
  public function get_parent_field($id,$field='title') {
		
		$SQL="SELECT id, l, $field FROM {$this->TableName} WHERE id=$id";
		$row=$this->db->getRow($SQL);
		if(is_array($row)&&count($row)>0)
		{
			$l = $row["l"];
			$id = $row["id"];
			$end = $row[$field];
			$SQL = "SELECT id, $field FROM {$this->TableName} WHERE l<$l AND (l+weight)>$l AND level>0 ORDER BY l desc";
			$res=$this->db->getAll($SQL);
			
			if(is_array($res)&&count($res)>0)
			foreach ($res as $row)
			{
				if(!empty($row[$field]))
					return $row[$field];
			}
		}

		return '';
	}
	
	function move_node_up($id) {
		$row=$this->db->getRow("select l,level from {$this->TableName} where id={$id}");
		$prev=(int)$this->db->getOne("select id from {$this->TableName} where l<{$row['l']} and level={$row['level']} order by l desc limit 1");
		if($prev) {
			$this->moveAfter($prev,$id);
			return true;
		}
		return false;
	}
	
	function move_node_down($id) {
		$row=$this->db->getRow("select l,level from {$this->TableName} where id={$id}");
		$next=(int)$this->db->getOne("select id from {$this->TableName} where l>{$row['l']} and level={$row['level']} order by l");
		if($next) {
			$this->moveAfter($id,$next);
			return true;
		}
		return false;
	}
	
	function move_node_left($id) {
		$r=$this->db->getRow("select l,level,pid from {$this->TableName} where id={$id}");
		if($r['level']<2)
			return false;
		$prev=(int)$this->db->getOne("select id from {$this->TableName} where l<{$r['l']} and level=".($r['level']-1)." order by l desc limit 1");
		if($prev) {
			$this->moveAfter($id,$prev);
			return true;
		}
		return false;
	}
	
	function move_node_right($id) {
		$r=$this->db->getRow("select l,level,pid from {$this->TableName} where id={$id}");
		$prev=(int)$this->db->getOne("select id from {$this->TableName} where l<{$r['l']} and level={$r['level']} and pid={$r['pid']} order by l desc limit 1");
		if($prev) {
			$this->moveAsFirstChildOf($id,$prev);
			return true;
		}
		return false;
	}
	
	function get_node_path_static($id, $field = 'value',$table='categories'){
		$path = "";
		$db=getdb();
		$SQL="SELECT id, l, $field FROM {$table} WHERE id=$id";
		$row=$db->getRow($SQL);
		if(is_array($row)&&count($row)>0)
		{
			$l = $row["l"];
			$id = $row["id"];
			$end = $row[$field];
			$SQL = "SELECT id, $field FROM {$table} WHERE l<$l and (l+weight)>={$l} AND level>0 ORDER BY l";
			$res=$db->getAll($SQL);
			if(is_array($res)&&count($res)>0)
			foreach ($res as $row)
			{
				$id = $row["id"];
				$value = $row[$field];
				if(!empty($value)) {
					$path .= "$value/";
				}
			}
			$path .= $end;
		}

		return $path;
	}
  
  function get_node_path($id, $field = 'value'){
		$path = "";

		$SQL="SELECT id, l, $field FROM {$this->TableName} WHERE id=$id";
		$row=$this->db->getRow($SQL);
		if(is_array($row)&&count($row)>0)
		{
			$l = $row["l"];
			$id = $row["id"];
			$end = $row[$field];
			$SQL = "SELECT id, $field FROM {$this->TableName} WHERE l<$l and (l+weight)>={$l} AND level>0 ORDER BY l";
			$res=$this->db->getAll($SQL);
			if(is_array($res)&&count($res)>0)
			foreach ($res as $row)
			{
				$id = $row["id"];
				$value = $row[$field];
				if(!empty($value)) {
					$path .= "$value/";
				}
			}
			$path .= $end;
		}

		return $path;
	}
  
  function get_tree_options($WHERE='',$level_offset=0,$extra_fields=""){
	  if($WHERE)
	   $WHERE = "WHERE $WHERE";
		$result = Array();
		if(!empty($extra_fields)) {
			$extra_fields=",".$extra_fields;
		}
		$SQL="SELECT id, value, level{$extra_fields} FROM {$this->TableName} $WHERE ORDER BY l";
		$row=$this->db->getAssoc($SQL);
		if(is_array($row)&&count($row)>0)
		{
			foreach ($row as $key=>$value)
			{
				$val=str_repeat(" &nbsp; ", $value['level']+$level_offset?$value['level']-1+$level_offset:0).$value['value'];
				if(!empty($extra_fields)) {
					$value['value']=$val;
					unset($value['level']);
					unset($value['id']);
					$result[$key]=$value;
				}
				else {
					$result[$key] = $val;
				}
			}
		}
		unset($result[1]);
		return $result;
	}
	
	function draw_categories_tree($cid, &$pids,$useLi=false){

		$pids = Array();

		$SQL="SELECT id,l, level FROM {$this->TableName} WHERE id='$cid'";
		$selected=$this->db->getRow($SQL);
		if(is_array($selected)&&count($selected)>0)
		{
			$sl = $selected['l'];
			$slevel = $selected['level'];

			$pids[$selected['id']] = $selected['id'];

			$SQL = "SELECT id FROM {$this->TableName} WHERE l<$sl AND (l+weight)>=$sl ";
			$res=$this->db->getAll($SQL);
			if(is_array($res)&&count($res)>0) {
				foreach ($res as $value) {
					$pids[$value['id']] = $value['id'];
				}
			}
		} else {
			return;
			$pids[] = '1';
		}

		 $SQL = "SELECT * FROM {$this->TableName} WHERE visible=1 AND level>1 AND pid IN (".implode(', ', $pids).") ORDER BY l";
		$cats = $this->db->getAll($SQL);
		$level = $cats[0]['level'];
		$path = Array();
 
 		if($useLi) {
 			$result .= "<ul class='Nav1'>\n";
 		}
 		else {
			$result .= "<table cellspacing=0 class='Nav1'>\n";
 		}
		foreach($cats as $cat){

			if($cat['level']>$level){
				//$result .= "<ul class=submenu>\n";
				$level = $cat['level'];
			}

			if($cat['level']<$level){
				//$result .= str_repeat("</ul>\n", $level-$cat['level']);
				$level = $cat['level'];
				array_pop($path);
			}

			$path[$level] = $cat['path'];
			$target='';
			if(!empty($cat['path'])) {
				$href = $cat['path'];
			} else 
			if (!empty($cat['custom_url'])) {
			  $href = $cat['custom_url'];
			  $target="target='{$cat['target']}'";
			} else {
			  $href = "/?cid={$cat['id']}";
			}
			if($useLi) {
				$result .= "<li class=\"nav{$level} selected\"><a href=\"$href\" {$target}>{$cat['value']}</a></li>\n";
			}
			else {
		  		$result .= "<tr><td BtnCssName=\"nav{$level}\" BtnSelected=\"".($pids[$cat['id']]?"true":"false")."\" class=\"nav{$level}\"><a href=\"$href\" {$target}>{$cat['value']}</a></td></tr>\n";
			}
		}
		$result .= $useLi?"</ul>\n": "</table>\n";
	
	return $result;

	}
	
	function draw_categories_tree2($cid, &$pids){

		$pids = Array();

		$SQL="SELECT id,l, level FROM {$this->TableName} WHERE id='$cid'";
		$selected=$this->db->getRow($SQL);
		if(is_array($selected)&&count($selected)>0)
		{
			$sl = $selected['l'];
			$slevel = $selected['level'];

			$pids[$selected['id']] = $selected['id'];

			$SQL = "SELECT id FROM {$this->TableName} WHERE l<$sl AND (l+weight)>=$sl ";
			$res=$this->db->getAll($SQL);
			if(is_array($res)&&count($res)>0) {
				foreach ($res as $value) {
					$pids[$value['id']] = $value['id'];
				}
			}
		} else {
			return;
			$pids[] = '1';
		}

		 $SQL = "SELECT * FROM {$this->TableName} WHERE visible=1 AND level>1 AND pid IN (".implode(', ', $pids).") ORDER BY l";
		$cats = $this->db->getAll($SQL);
		$level = $cats[0]['level'];
		$path = Array();
 
 		if($useLi) {
 			$result .= "<ul class='Nav1'>\n";
 		}
 		else {
			$result .= "<table cellspacing=0 class='Nav1'>\n";
 		}
		foreach($cats as &$cat){

			if($cat['level']>$level){
				//$result .= "<ul class=submenu>\n";
				$level = $cat['level'];
			}

			if($cat['level']<$level){
				//$result .= str_repeat("</ul>\n", $level-$cat['level']);
				$level = $cat['level'];
				array_pop($path);
			}

			$path[$level] = $cat['path'];
			$target='';
			if(!empty($cat['path'])) {
				$href = $cat['path'];
			} else 
			if (!empty($cat['custom_url'])) {
			  $href = $cat['custom_url'];
			  $target="target='{$cat['target']}'";
			} else {
			  $href = "/?cid={$cat['id']}";
			}
			if($useLi) {
				$result .= "<li class=\"nav{$level} selected\"><a href=\"$href\" {$target}>{$cat['value']}</a></li>\n";
			}
			else {
		  		$result .= "<tr><td BtnCssName=\"nav{$level}\" BtnSelected=\"".($pids[$cat['id']]?"true":"false")."\" class=\"nav{$level}\"><a href=\"$href\" {$target}>{$cat['value']}</a></td></tr>\n";
			}
			$cat['selected'] = $pids[$cat['id']]?true:false;
			$cat['href'] = $href;
			$cat['target'] = $target;
			
		}
		$result .= $useLi?"</ul>\n": "</table>\n";
	
	return $cats;

	}	
  
}


?>