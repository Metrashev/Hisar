<?php



class AttributeCluster extends ArrayObject {
	
	public $DumpStat=false;
	
	public $order=array();
	
	public $where=array();
	public $GroupBy=array();
	public $OnlyById = array();
	/**
	 * Enter description here...
	 *
	 * @var AttributeGroup
	 */
	public $MainGroup = null;
	public $MainGroupId = 1;
	public $MainGroupTableName = '';
	
	public $HavMultiLineGroupWhere = false;
	
	public $AttributeClusterId=0;
	
	public $ClusterSaveData = array();
	public $ClusterLoadData = array();
	
	public $ClusterRow = array();
	
	public function __construct($row){
		
		parent::__construct(array());

		if($row['id']){
			$this->ClusterRow = $row;
			$a = explode(',', $row['attribute_group_ids']);
			$this->MainGroupId = (int)$a[0];
			$this->AttributeClusterId = $row['id'];
		} else {
			$a = array($this->MainGroupId);
		}

		foreach ($a as $v){
			$grp = AttributeGroup::byId($v, $this);
			$this[$grp->Name] = $grp;
			$this->OnlyById[$grp->Id] = $grp;
		}
		
		$this->MainGroup = $this->OnlyById[$this->MainGroupId];
		

		$this->MainGroupTableName =$this->MainGroup->RealTableName;
		if($row['id']){ // Ako id = 0 To imame izkustveno sazdaden Product. Za tarsene po obshta grupa m/u produkti
			//$this->OnlyById[1]->where[] ="attribute_cluster_id = {$row['id']}";
			if($this->MainGroupId==1){
				$this->OnlyById[$this->MainGroupId]['attribute_cluster_id']->addWhere("value = {$row['id']}");
			}

		} else {
			$this->OnlyById[$this->MainGroupId]->where[] = '1';
		}
	}
	
/**
 * Enter description here...
 *
 * @param int $id
 * @return AttributeCluster
 */
	public static function byId($id){
		$row = getdb()->getRow("SELECT * FROM attribute_clusters WHERE id=?",array($id));
		return new self($row);
	}
	
	function getAllProducts($limit=null,$from=0){
		$where = $this->buildSQLWhere();
		
		if($this->HavMultiLineGroupWhere){
			$SQL = "SELECT DISTINCT {$this->MainGroupTableName}.id ".$where.$this->buildSQLGroupBy().$this->buildSQLOrder();	
		} else {
			$SQL = "SELECT {$this->MainGroupTableName}.* ".$where.$this->buildSQLGroupBy().$this->buildSQLOrder();
		}

		if(!is_null($limit)){
			$SQL .= " LIMIT $from, $limit";
		}
		$starTime = microtime(true);
		$pids = getdb()->getAll($SQL);
		
		if($this->DumpStat) echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";
		
		$pids = $this->getProductsRowsByIds($pids);

		return new ProductsArray($pids, $this);
	}
	
	function getPagedProducts($itemsPerPage = 10){

		$count = $this->getProductsCnt();

		$pb = new CFEPageBar($itemsPerPage, $count);
		$i = ($pb->CurrentPage - 1)*$itemsPerPage;

		$data = array();
		$data['PageBar'] = $pb->getData($hreh);
		$data['items'] = $count ? $this->getAllProducts($itemsPerPage,$i) : array();
		
		return $data;
	}
	
	function getProductsCnt(){
		
		$where = $this->buildSQLWhere();
		
		if($this->HavMultiLineGroupWhere){
			$SQL = "SELECT count(DISTINCT {$this->MainGroupTableName}.id) ".$this->buildSQLWhere();	
		} else {
			$SQL = "SELECT count(*) ".$this->buildSQLWhere();
		}
		
		$starTime = microtime(true);
		$cnt = getdb()->getOne($SQL);
		
		if($this->DumpStat)  echo "Count SQL:<hr>$SQL<hr> {$cnt} за ".(microtime(true)-$starTime)." sec.<br/>";
		
		return $cnt;
	}

	
	function buildSQLOrder(){

		$order=array();
		foreach ($this->order as $field){
			$order[]=$field->getOrderSQL();
		}
		
		if(!empty($order)) return " ORDER BY ".implode(',',$order);;

		return '';
	}
	

	function _buildSQLWhere(){
		

		$tables = array();
		$where = $this->where;
		
		foreach ($this as $grp){
			$tmp = $grp->getSQL();
			if(!empty($tmp['table'])) {
				$tables[] = $tmp['table'];
				$where[] = $tmp['where'];
				if($grp->IsTable) {
					$this->HavMultiLineGroupWhere = true;
				}
			}
		}
		/*
			array_unique ni triabva za situacia pri koiato imame 2 AttrGroups varhu edna i sashta Real Table no razlichni koloni.
		*/
		$tables = array_unique($tables);
		
		$tables = implode(", \n", $tables);
		if(empty($tables)) $tables = $this->MainGroupTableName;
		
		$where = empty($where) ? '' :implode(" AND \n", $where);
		
		return array(
			'TABLES'=>$tables,
			'WHERE'=>$where
		);
		

	}
	
	function buildSQLWhere(){
		$arr = $this->_buildSQLWhere();
		
		$where = empty($arr['WHERE']) ? '' : "WHERE\n{$arr['WHERE']}";
		$from = empty($arr['TABLES']) ? '' : "FROM\n{$arr['TABLES']}";
		return "$from $where";
	}
	
	function buildSQLGroupBy(){
		if(empty($this->GroupBy)) return "";
		return " GROUP BY ".implode(', ',$this->GroupBy);
	}
	
	function addOrder(FEAttribute $field, $ord='ASC'){
		$field->order = $ord;
		$this->order[] = $field;
	}
	
	
	function loadProductByRow($ClusterLoadData){
		$this->ClusterLoadData = $ClusterLoadData;
		foreach ($this->OnlyById as $gid=>$group) {
			$group->loadData($ClusterLoadData[$gid]);
		}
	}
	
	function getProductById($id){
		$row = array('id'=>$id);
		$tmp = $this->HavMultiLineGroupWhere;
		$this->HavMultiLineGroupWhere = true;
		$row = $this->getProductRowById($row);
		$this->HavMultiLineGroupWhere = $tmp;
		$this->loadProductByRow($row);
		return $this;
	}

	function getProductRowById($row){
		
		if(empty($row)) return array();
		
		$finalRes = array();
		$pids = array();

		$finalRes[$this->MainGroupId][0]=$row;  // Prehvarliame danni ot Main (products) tablicata

		$pid = $row['id'];
		

		$types=array();
		$realTables = array();
		foreach ($this as $grp){
			if(!$this->HavMultiLineGroupWhere && $grp->Id==$this->MainGroupId) continue;
			if(!$grp->Visible && !($this->HavMultiLineGroupWhere && $grp->Id==$this->MainGroupId)) continue;
			
			if($grp->UseRealTable){
				$realTables[$grp->Id]=$grp->RealTableName;
				continue;
			}
			foreach ($grp as $atr){
				if($atr->Visible)
					$types[$atr->tableName] = $atr->tableName;
			}
		}
		
		if(!empty($types)){
			$SQL = array();
			foreach ($types as $v){
				$SQL[] = "SELECT * FROM `{$v}` WHERE product_id = {$pid}";
			}
			
			$SQL = implode(' UNION ', $SQL);
			
			$starTime = microtime(true);
			$res = getdb()->Query($SQL);
			
			foreach ($res as $row){
				$finalRes[$row['group_id']][$row['sub_id']][$row['attribute_id']]=$row;
			}
			
			if($this->DumpStat)  echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";
			
		}
		
		if(!empty($realTables)){
			foreach ($realTables as $k=>$v){
				$idField  = ($k==$this->MainGroupId?'id':'product_id');
				
				$SQL="SELECT * FROM `$v` WHERE {$idField} = {$pid}";

				$starTime = microtime(true);
				$res = getdb()->Query($SQL);
				
				if($this->DumpStat)  echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";
				
				foreach ($res as $row){
					$finalRes[$k][(int)$row['sub_id']]=$row;
				}
			}
		}

		return $finalRes;
	}
	
	function getProductsRowsByIds($ids){
		
		if(empty($ids)) return array();
		
		$finalRes = array();
		$pids = array();
		foreach ($ids as $row){
			$finalRes[$row['id']][$this->MainGroupId][0]=$row;  // Prehvarliame danni ot Main (products) tablicata
			$pids[$row['id']]=$row['id']; //Otdeliame product.id za sledvashtite selecti
		}
		
		
		$pids = implode(',',$pids);
		$types=array();
		$realTables = array();
		foreach ($this as $grp){
			if(!$this->HavMultiLineGroupWhere && $grp->Id==$this->MainGroupId) continue;
			if(!$grp->Visible && !($this->HavMultiLineGroupWhere && $grp->Id==$this->MainGroupId)) continue;
			
			if($grp->UseRealTable){
				$realTables[$grp->Id]=$grp->RealTableName;
				continue;
			}
			foreach ($grp as $atr){
				if($atr->Visible)
					$types[$atr->tableName] = $atr->tableName;
			}
		}
		
		if(!empty($types)){
			$SQL = array();
			foreach ($types as $v){
				$SQL[] = "SELECT * FROM `{$v}` WHERE product_id IN ({$pids})";
			}
			
			$SQL = implode(' UNION ', $SQL);
			
			$starTime = microtime(true);
			$res = getdb()->Query($SQL);
			
			foreach ($res as $row){
				$finalRes[$row['product_id']][$row['group_id']][$row['sub_id']][$row['attribute_id']]=$row;
			}
			
			if($this->DumpStat)  echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";
			
		}
		
		if(!empty($realTables)){
			foreach ($realTables as $k=>$v){
				$idField  = ($k==$this->MainGroupId?'id':'product_id');
				
				$SQL="SELECT * FROM `$v` WHERE {$idField} IN ($pids)";

				$starTime = microtime(true);
				$res = getdb()->Query($SQL);
				
				if($this->DumpStat)  echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";
				
				foreach ($res as $row){
					$finalRes[$row[$idField]][$k][(int)$row['sub_id']]=$row;
				}
			}
		}

		return $finalRes;
	}
	
	function getIterator(){
		return new ArrayIterator($this->OnlyById);
	}
	
	function getSearchForm(){
		$controls = array();
		foreach ($this->OnlyById as $gid=>$group){
			foreach ($group->OnlyById as $aid=>$attribute) {
				if($attribute['php_data']['BEListSearchable']){
					$control = $attribute->getSearchControl();
					$controls[$gid][$aid] = $control;
				}
			}
		}
		return $controls;
	}
	
	function DBSave($ClusterData, $id){

		if(!$this->AttributeClusterId) throw new Exception('No Cluster ID');
		
		$id = (int)$id;
		
		foreach ($this->OnlyById as $gid=>$group) { // PRavime vsichki da sa sas Sub_id
			if(!$group->IsTable){
				$ClusterData[$gid] = array($ClusterData[$gid]);
			}
		}
		
		foreach ($this->OnlyById as $gid=>$group) {
			$group->DBSaveOnBefore($ClusterData);
		}
		
		$this->ClusterSaveData = $ClusterData;
		
		$id=$this->MainGroup->DBSaveRealTable($ClusterData[$this->MainGroupId][0], $id, 0);

		foreach ($this->OnlyById as $gid=>$group) {
			if($gid==$this->MainGroupId) continue;
			$group->DBSave($ClusterData, $id);
		}
		
		foreach ($this->OnlyById as $gid=>$group) {
			$group->DBSaveOnAfter($ClusterData);
		}
		
					
		foreach ($this->OnlyById as $gid=>$group) {
			$group->ReorderSubIds($id);
		}

		return $id;
	}
	
	function DBDelete($product_id){
		$row = explode(',',$this->ClusterRow['product_group_ids']);
		foreach ($row as $acId){
			$this->DBDeleteSlaveProducts($product_id, $acId);
		}

		foreach ($this->OnlyById as $gid=>$group) {
			$group->DBDelete($product_id);
		}
	}
	
	function DBDeleteSlaveProducts($product_id, $acId){
		$c = AttributeCluster::byId($acId);
		$SQL = "SELECT id FROM {$c->MainGroupTableName} WHERE master_id={$product_id}";
		$res = getdb()->Query($SQL);
		foreach ($res as $row){
			$c->DBDelete($row['id']);
		}
	}
	
	function getAllProducts2(){
		$where = $this->buildSQLWhere();
		
		if($this->HavMultiLineGroupWhere){
			$SQL = "SELECT DISTINCT {$this->MainGroupTableName}.id ".$where.$this->buildSQLGroupBy().$this->buildSQLOrder();	
		} else {
			$SQL = "SELECT {$this->MainGroupTableName}.* ".$where.$this->buildSQLGroupBy().$this->buildSQLOrder();
		}

		$starTime = microtime(true);
		$rs = getdb()->__query($SQL);
		
		if($this->DumpStat) echo "SELECT SQL:<hr>$SQL<hr> за ".(microtime(true)-$starTime)." sec.<br/>";

		return new ProductsArray2($rs, $this);
	}
	/**
	 * Exports Selected Data In Required Format
	 *
	 * @param unknown_type $Format
	 */
	function Export($Format){

		$data = $this->getAllProducts2();
		if($Format==6){
			$this->ExportCSV($data);
		} else if($Format==5){
			$this->ExportCSV($data, "\t", ' ', 'txt');
		}
		$this->ExportHTML($data);
	}
	
	function ExportHTML($data){

		echo "<table border='1'>";
		
		
		$cols = array();
		echo "<tr>";
		foreach ($this->OnlyById as $gid=>$group){
			foreach ($group->OnlyById as $aid=>$attribute){
				echo "<th>{$attribute->LabelPrefix}</th>";
				$cols["{$gid}_{$aid}"] = "{$gid}_{$aid}";
			}
			
		}
		echo "</tr>";
		

		foreach ($data as $ac){
			
			$Rows = array();
			$Rows[0] = $cols;

			foreach ($ac as $gid=>$group){
				if(!$group->IsTable) {
					$Table = array(0=>$group);
				} else {
					$Table = $group->Table;
				}
				
				foreach ($Table as $sub_id=>$SubGroup){
					foreach ($SubGroup as $aid=>$attribute){
						$Rows[$sub_id]["{$gid}_{$aid}"] = $attribute->getBEListValue();
					}
				}
			}
			
			foreach ($Rows as $row){
				echo "<tr>";
				foreach ($cols as $col){
					$val = $row[$col];
					$val = $val ? $val : '&nbsp;';
					echo "<td>{$val}</td>";
				}
				echo "</tr>";
			}
			
			
		}
		echo "</table>";
		die();
	}
	
	function ExportCSV($data, $delimiter=',', $enclosure='"', $ext="csv"){

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: application/$ext;\n"); //or yours?
//header("Content-Transfer-Encoding: binary");
$len = filesize($filename);
//header("Content-Length: $len;\n");
$outname="export.$ext";
header("Content-Disposition: attachment; filename=\"$outname\";\n\n");


		
		
		$cols = array();
		$row = array();

		foreach ($this->OnlyById as $gid=>$group){
			foreach ($group->OnlyById as $aid=>$attribute){
				$row[] = "{$attribute->LabelPrefix}";
				$cols["{$gid}_{$aid}"] = "{$gid}_{$aid}";
				if($attribute instanceof FEAttributeSet){
					$attribute->Separator="\n";
				}
			}
		}
		
		
		$fp = fopen("php://output","w+");
		fputcsv($fp, $row, $delimiter, $enclosure);

		

		foreach ($data as $ac){
			
			$Rows = array();
			$Rows[0] = $cols;

			foreach ($ac as $gid=>$group){
				if(!$group->IsTable) {
					$Table = array(0=>$group);
				} else {
					$Table = $group->Table;
				}
				
				foreach ($Table as $sub_id=>$SubGroup){
					foreach ($SubGroup as $aid=>$attribute){
						$Rows[$sub_id]["{$gid}_{$aid}"] = $attribute->getBEListValue();
					}
				}
			}
			
			foreach ($Rows as $row){
				foreach ($cols as $col){
					$val = $row[$col];
					$val = $val ? $val : '';
					$row[$col] = $val;
				}
				fputcsv($fp, $row, $delimiter,$enclosure);
			}
			
			
		}
		fclose($fp);
		die();
	}
}


class ProductsArray2 extends DBRecordSet {
	
	/**
	 * Enter description here...
	 *
	 * @var AttributeCluster
	 */
	public  $cluster;

	
	function __construct($rs, $cluster){
		$this->cluster = $cluster;
		parent::__construct($rs);
	}

	function current () {
		$data = $this->cluster->getProductRowById($this->row);
		$this->cluster->loadProductByRow($data);
		return $this->cluster;
	}
}

class ProductsArray extends ArrayIterator  {
	
	/**
	 * Enter description here...
	 *
	 * @var AttributeCluster
	 */
	public  $cluster;
	function __construct($ids, $cluster){
		$this->cluster = $cluster;
		parent::__construct($ids);
	}
	
	function current(){
		$val = parent::current();
		$this->cluster->loadProductByRow($val);
		return $this->cluster;
	}

}

class AttributeGroup extends ArrayObject {
	
	public $Id;
	public $IsTable=false;
	public $UseRealTable=false;
	public $RealTableName='';
	public $Name='';
	
	public $GroupRow;
	
	public $where=array();
	
	public $OnlyById = array();
	
	/* Za Grupi koito sa is_table */
	/*
	public $TableIndex = 0;
	public $TableRows = 0;
	public $TableData = array();
	*/
	public $Table = array();
	
	public $Visible=true;
	
	/**
	 * Reference to parent AttributeCluster
	 *
	 * @var AttributeCluster
	 */
	public $Cluster;
	
	protected $RecordId=0; // Tuk se darzi row id na zapisa ot tablicata s dannite za Istinskite tablici
	
	public $CurrentSubId=0;
	
	public $ReOrderArray; // Tuk darzime koi sub_id-ta triabva da se prepodrediat. Trupat se v DBSave i se izpalniava ot Clustera, sled DBSaveOnAfter
	

	public function __construct($row, AttributeCluster $cluster){
		
		$this->GroupRow = $row;
		$this->Cluster = $cluster;
		$this->Id = (int)$row['id'];
		$this->Name = $row['name'];
		$this->IsTable = (boolean)$row['is_table'];
		$this->UseRealTable = (boolean)$row['use_real_table'];
		$this->RealTableName = $row['real_table_name'];
		
		//parent::__construct($row,ArrayObject::ARRAY_AS_PROPS);
		//parent::__construct($row,ArrayObject::STD_PROP_LIST);
		parent::__construct(array());
		
		if(!FEAttribute::$SELECT_FIELDS){
			if(defined(LNG_CURRENT)){
				FEAttribute::$SELECT_FIELDS ='id,name,prefix_'.LNG_CURRENT.' as prefix, suffix_'.LNG_CURRENT.' as suffix, type, php_data';
			} else {
				FEAttribute::$SELECT_FIELDS ='id,name,be_prefix as prefix, be_suffix as suffix, type, php_data';
			}
		}
		
		$tmp = getdb()->getAll("SELECT ".FEAttribute::$SELECT_FIELDS." FROM attributes WHERE id IN ({$row['attribute_ids']}) AND (language_id='' OR language_id=?)", array(LNG_CURRENT));
		$res = explode(',',$row['attribute_ids']);
		$res = array_flip($res);
		foreach ($tmp as $row) $res[$row['id']] = $row;
		
/*		if($this->Id==1){
			$row = array();
			$row['id'] = '0id';
			$row['name'] = 'id';
			$row['type'] = 1;
			$res[] = $row;
			
			$row['id'] = '0acid';
			$row['name'] = 'attribute_cluster_id';
			$res[] = $row;
		}*/
		
		foreach ($res as $row){
			$row['group_id'] = $this->Id;
			$attr = FEAttribute::factory($row, $this);
			$this[$row['name']] = $attr;
			$this->OnlyById[$row['id']] = $attr;
		}
		

	}
	
	
	function getIterator(){
		return new ArrayIterator($this->OnlyById);
	}
/*	
	function cleanValue(){
		foreach ($this->OnlyById as $attr) $attr->cleanValue();
	}
*/	
	
	function loadData($GroupData){
		if(!$this->Visible) return ;
		if($this->IsTable){
			if(!is_array($GroupData)) $GroupData=array();

			$this->Table = new AttrGrpTable(array_keys($GroupData), $this);
		} else {
			$this->_loadData(0);
		}
	}

	function _loadData($sub_id){
		$this->CurrentSubId = $sub_id;
		$data = $this->Cluster->ClusterLoadData[$this->Id][$this->CurrentSubId];
		if($this->UseRealTable){
			foreach ($this->OnlyById as $id=>$attribute) {
				if(!$attribute->Visible) continue;
				$attribute->Value = $data[$attribute->fieldName];
			}
		} else {
			foreach ($this->OnlyById as $id=>$attribute) {
				if(!$attribute->Visible) continue;
				$attribute->Value = $data[$id][$attribute->fieldName];
			}
		}
	}


	public static function byId($id, AttributeCluster $cluster){
		
		//$row = getdb()->getRow("SELECT id,name,attribute_ids, is_table,use_real_table,real_table_name FROM attribute_groups WHERE id=?",array($id));
		$row = getdb()->getRow("SELECT * FROM attribute_groups WHERE id=?",array($id));
		
/*		if($id==1 && empty($row)){
			$row = array(
				'id'=>1,
				'name'=>'Main Products',
				'is_table'=>0,
				'use_real_table'=>1,
				'real_table_name'=>'products',
				'attribute_ids'=>'0',
			);
		}*/
		return new self($row, $cluster);
	}
	

	function getSQL(){
		$SQL = array();
		
		if($this->UseRealTable){
			$visible = false;
			$SQL['where'] = $this->where;

			foreach ($this->OnlyById as $atr){
				$visible |= $atr->Visible;
				if(empty($atr->where)) continue;
				$SQL['where'][]=implode(" AND \n",$atr->where);
			}
			$this->Visible = $visible;
			
			if(empty($SQL['where'])) return ;


			if($this->Id<>$this->Cluster->MainGroupId){
				$SQL['where'][]="products.id={$this->RealTableName}.product_id";
			}
			
			$SQL['where']=implode(" AND \n", $SQL['where']);
			$SQL['table'] = $this->RealTableName;
			
			return $SQL;
		}
		
		foreach ($this->OnlyById as $atr){
			$tmp = $atr->getSQL();
			if(empty($tmp['where'])) continue;
			$SQL['table'][] = $tmp['table'];
			$SQL['where'][] = $tmp['where'];
		}
		
		if(empty($SQL['where'])) return ;
		$SQL['table']=implode(", \n", $SQL['table']);
		$SQL['where']=implode(" AND \n", $SQL['where']);

		return $SQL;
	}
	
	/**
	 * Convert keys from attribute id to attribute name. It is use only for real tables.
	 * It preserves all keys that do not have corespondant attributes.
	 * If the key is last_update it stays last_update.
	 * The purpose of key preserving is for extendet fields of ManagedFile & ManagedImage _size, _type etc.
	 *
	 * @param Array $data
	 * @return Array
	 */
	function changeDataKeysId2Name($data){
		/*
		$res = array();
		foreach ($this->OnlyById as $aid=>$attribute) {
			if(!isset($data[$aid])) continue;
			$res[$attribute['name']] = $data[$aid];
		}
		return $res;
		*/
		
		$res = array();
		foreach ($data as $aid=>$val){
			if(isset($this->OnlyById[$aid])){
				$res[$this->OnlyById[$aid]->fieldName] = $val;
			} else {
				$res[$aid] = $val;
			}
		}
		return $res;
	}
	
	function DBSaveOnBefore(&$ClusterData){
		foreach ($ClusterData[$this->Id] as $this->CurrentSubId=>$null){
			foreach ($this->OnlyById as $aid=>$attribute) {
				$attribute->DBSaveOnBefore($ClusterData);
			}
		}
	}
	
	function DBSaveRealTable(&$row, $id, $sub_id){

		$row2 = $this->changeDataKeysId2Name($row);
		
		if($this->Id==1){
			$row2['attribute_cluster_id'] = $this->Cluster->AttributeClusterId;
		} 
		
		if($this->Id==$this->Cluster->MainGroupId){
			$row2['id'] = $id;
		} else {
			$row2['product_id'] = $id;		
			$row2['sub_id'] = $sub_id;
		}
		
		unset($row2['ord']);


		$db = getdb();
		$db->autoExecute($this->RealTableName, $row2, CDB::AUTOQUERY_INSERT_ON_DUPLICATE);

		$RecordId = $db->getLastInsertId();
		foreach ($this->OnlyById as $aid=>$attribute) {
			$attribute->RecordId[$this->CurrentSubId] = $RecordId;
		}
		return $RecordId;
	}
	
	function DBSaveAttribTables($row, $id, $sub_id){
		$db = getdb();
		$data = array();
		$data['product_id'] = $id;
		$data['group_id'] = $this->Id;
		$data['sub_id'] = $sub_id;
		
		foreach ($this->OnlyById as $aid=>$attribute) {
			
			if(!array_key_exists($aid, $row)) continue; //Niama kakvo da zapisvame

			$data['attribute_id'] = $aid;
			$data['value'] = $row[$aid];
			
			$db->autoExecute($attribute->tableName, $data, CDB::AUTOQUERY_INSERT_ON_DUPLICATE);
			
			$attribute->RecordId[$this->CurrentSubId] = $db->getLastInsertId();
		}
	}

	
	function DBSaveOnAfter($ClusterData){
		foreach ($ClusterData[$this->Id] as $this->CurrentSubId=>$null){
			foreach ($this->OnlyById as $aid=>$attribute) {
				$attribute->DBSaveOnAfter($ClusterData);
			}
		}
	}
	
	function DBSave($ClusterData, $id){
		
		$ReOrder = array();

		$max = -1;
		foreach ($ClusterData[$this->Id] as $this->CurrentSubId=>$row){
			if($this->CurrentSubId==='z'){
				throw new Exception("Doide Z");
			}

			if($this->UseRealTable){
				$this->DBSaveRealTable($row, $id, $this->CurrentSubId);
			} else {
				$this->DBSaveAttribTables($row, $id, $this->CurrentSubId);
			}
			
			if(((int)$row['ord'])>0 ){
				$ReOrder[$this->CurrentSubId]=(int)$row['ord'] - 1;
			}
			
		}
		$max = $this->CurrentSubId;

		if(!empty($ReOrder)){
			$tmp = array_unique($ReOrder);
			if(count($tmp)<>count($ReOrder)){
				die('Duplicated positions');
			}
			foreach ($ReOrder as $sub_id=>$new_sub_id){
				if($new_sub_id>$max){
					die('More than Max');
				}
				//if($sub_id==$new_sub_id) unset($ReOrder[$sub_id]);
			}
		}

		$this->ReOrderArray = $ReOrder;
	}
	

	function ReOrderSubIds($product_id){

		if(empty($this->ReOrderArray)) return ;
		asort($this->ReOrderArray);
		$ReOrder = new ArrayObject($this->ReOrderArray);
		//$newArray=array();
		
		foreach ($ReOrder as $k=>$v) {
			$newArray[]=array($k,$v,$k,$v);
		}
		$ReOrder=new ArrayObject($newArray);
		
		if($this->UseRealTable){
			foreach ($ReOrder as $index=>$array){
				$sub_id=$array[0];
				$new_sub_id=$array[1];
			//foreach ($ReOrder as $sub_id=>$new_sub_id){
				$this->ReOrderSubId1($this->RealTableName, $product_id, $sub_id, $new_sub_id);
				$ReOrder=$this->ReOrderSubIdPHP($ReOrder, $sub_id, $new_sub_id);
			}
		} else {
			$tables = $this->getAttributesTables();
			//foreach ($ReOrder as $sub_id=>$new_sub_id){
			foreach ($ReOrder as $index=>$array){
				$sub_id=$array[0];
				$new_sub_id=$array[1];
				foreach ($tables as $table=>$null){
					$this->ReOrderSubId2($table, $product_id, $sub_id, $new_sub_id, $this->Id);
				}
				$ReOrder=$this->ReOrderSubIdPHP($ReOrder, $sub_id, $new_sub_id);
				
			}
		}
	//	if(!$this->isValidOrder($ReOrder)) {
	//		$this->ReOrderSubIds($product_id);
	//	}
		//die;
	}
	
	/*function isValidOrder($ReOrder) {
		$a=array();
		foreach ($ReOrder as $k=>$v) {
			if($v[0]!=$v[3]) {
				$a[$v[0]]=$v[3];
			}
		}
		if(!empty($a)) {
			$this->ReOrderArray=new ArrayObject($a);
			return false;
		}
		return true;
	}*/
	
	function findIndex($search,$array) {
		foreach ($array as $k=>$v) {
			if($v[0]==$search) {
				return $k;
			}
		}
		return -1;
	}
	
	function ReOrderSubIdPHP($ReOrder, $sub_id, $new_sub_id){
		
		$keys=array();
		foreach ($ReOrder as $index=>$array) {
			$keys[$array[0]]=array($array[0],$index);
		}
		$keys=new ArrayObject($keys);
		if($sub_id<$new_sub_id){
			
			foreach ($keys as $k=>$array) {
				$v=$array[0];
				if($v>$new_sub_id) {
					$keys[$k][0]++;
				}
			}
			
			$i=$this->findIndex($sub_id,$keys);
			$keys[$i][0]=$new_sub_id+1;
			foreach ($keys as $k=>$array) {
				$v=$array[0];
				if($v>$sub_id) {
					$keys[$k][0]--;
				}
			}
			
	//		$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE product_id=? AND sub_id>? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
	//		$db->Query("UPDATE `$table` SET sub_id=? WHERE product_id=? AND sub_id=?", array($new_sub_id+1, $product_id, $sub_id));
	//		$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));						
		} else {
			
			foreach ($keys as $k=>$array) {
				$v=$array[0];
				if($v>=$new_sub_id) {
					$keys[$k][0]++;
				}
			}
			
			$i=$this->findIndex($sub_id+1,$keys);
			$keys[$i][0]=$new_sub_id;
			foreach ($keys as $k=>$array) {
				$v=$array[0];
				if($v>$sub_id) {
					$keys[$k][0]--;
				}
			}
			
	//		$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE product_id=? AND sub_id>=? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
	//		$db->Query("UPDATE `$table` SET sub_id=? WHERE product_id=? AND sub_id=?", array($new_sub_id, $product_id, $sub_id+1));
	//		$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));
		}
		$a=array();
		foreach ($keys as $k=>$v) {
			$ReOrder[$v[1]][0]=$v[0];
			//$a[$v[0]]=$ReOrder[$k];
		}
		
		return $ReOrder;
		//return $ReOrder;
	}
	
	function ReOrderSubId1($table, $product_id, $sub_id, $new_sub_id){
		$db = getdb();
		if($sub_id<$new_sub_id){
			$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE product_id=? AND sub_id>? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
			$db->Query("UPDATE `$table` SET sub_id=? WHERE product_id=? AND sub_id=?", array($new_sub_id+1, $product_id, $sub_id));
			$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));						
		} else {
			$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE product_id=? AND sub_id>=? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
			$db->Query("UPDATE `$table` SET sub_id=? WHERE product_id=? AND sub_id=?", array($new_sub_id, $product_id, $sub_id+1));
			$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));
		}
	}
	
	function ReOrderSubId2($table, $product_id, $sub_id, $new_sub_id, $group_id){
		$db = getdb();
		if($sub_id<$new_sub_id){
			$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE group_id={$group_id} AND product_id=? AND sub_id>? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
			$db->Query("UPDATE `$table` SET sub_id=? WHERE group_id={$group_id} AND product_id=? AND sub_id=?", array($new_sub_id+1, $product_id, $sub_id));
			$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE group_id={$group_id} AND product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));						
		} else {
			$db->Query("UPDATE `$table` SET sub_id=sub_id+1 WHERE group_id={$group_id} AND product_id=? AND sub_id>=? ORDER BY sub_id DESC", array($product_id, $new_sub_id));
			$db->Query("UPDATE `$table` SET sub_id=? WHERE group_id={$group_id} AND product_id=? AND sub_id=?", array($new_sub_id, $product_id, $sub_id+1));
			$db->Query("UPDATE `$table` SET sub_id=sub_id-1 WHERE group_id={$group_id} AND product_id=? AND sub_id>? ORDER BY sub_id ASC", array($product_id, $sub_id));
		}
	}
	
	function DBDelete($product_id){
		
		$db = getdb();

		if($this->IsTable){
			if($this->UseRealTable){
				$subIds = $db->getCol("SELECT sub_id FROM `{$this->RealTableName}` WHERE product_id=?", array($product_id));
			} else {
				$tables = $this->getAttributesTables();
				foreach ($tables as $table=>$null){
					$SQL[] = "SELECT sub_id FROM `$table` WHERE product_id={$product_id} AND group_id={$this->Id}";
				}
				$SQL = implode(' UNION ', $SQL);
				$subIds = $db->getCol($SQL);
			}
		} else {
			$subIds = array(0);
		}
		
		foreach ($subIds as $sub_id){
			foreach ($this->OnlyById as $aid=>$attribute) {
				$attribute->DBDelete($product_id, $sub_id);
			}
		}
		
		if($this->UseRealTable){
			if($this->Id == $this->Cluster->MainGroupId){
				$db->Query("DELETE FROM `{$this->RealTableName}` WHERE id=?", array($product_id));
			} else {
				$db->Query("DELETE FROM `{$this->RealTableName}` WHERE product_id=?", array($product_id));
			}
		}
	}
	
	function DBDeleteSubId($product_id, $sub_id){
		foreach ($this->OnlyById as $aid=>$attribute) {
			$attribute->DBDelete($product_id, $sub_id);
		}
		
		
		if($this->UseRealTable){
			$db = getdb();
			$db->Query("DELETE FROM `{$this->RealTableName}` WHERE product_id=? AND sub_id=?", array($product_id, $sub_id));
			$db->Query("UPDATE `{$this->RealTableName}` SET sub_id = sub_id - 1 WHERE product_id=? AND sub_id>?", array($product_id, $sub_id));
		}
	}
	
	function getAttributesTables(){
		$tables = array();
		foreach ($this->OnlyById as $attribute) $tables[$attribute->tableName] = 1;
		return $tables;
	}
}

class AttrGrpTable extends ArrayIterator  {
	public  $AttributeGroup;
	
	function __construct($data, $AttributeGroup){
		$this->AttributeGroup = $AttributeGroup;
		parent::__construct($data);
	}
	
	function current(){
		$sub_id = parent::current();
		$this->AttributeGroup->_loadData($sub_id);
		return $this->AttributeGroup;
	}
}




class FEAttribute{
	static $SELECT_FIELDS='';
	public $where=array();
	/**
	 * Enter description here...
	 *
	 * @var AttributeGroup
	 */
	public $Group=null;
	public $order='';
	public $tableName='';
	public $AsTableName='';
	public $fieldName='value';

	public $Id;
	public $Type;
	public $Name;
	public $LabelPrefix;
	public $LabelSuffix;
	public $PHPData;
	
	
	public $Visible = true;
	
	public $IsBESearchable = true;
	
	
	public $Value=null;
	public $RecordRow=null;
	
	/**
	 * Za vsiako sub_id darzi record Id 
	 * Popalva se pri DBSave. 
	 * Ako ni triabvat ID-tata pri loadData, moze da gi namerim v cluster->ClusterLoadData
	 *
	 * @var Array
	 */
	public $RecordId=array(); // Za vsiako sub id darzi record Id

	function __construct($row, AttributeGroup $grp){

		
		$this->Group = $grp;
		
		if($grp->UseRealTable){
			$this->fieldName = $row['name'];
			$this->tableName = $grp->RealTableName;
			$this->AsTableName = $grp->RealTableName;
		} else {
			$this->fieldName = 'value';
			$this->tableName = $GLOBALS['attribute_type_tables'][$row['type']];
			$this->AsTableName = 'tbl'.$grp->Id.'_'.$row['id'];
		}
		if($row['php_data']){
			$row['php_data']=unserialize($row['php_data']);
		}
		
		$this->Id = $row['id'];
		$this->Name = $row['name'];
		$this->Type = $row['type'];
		$this->LabelPrefix = $row['prefix'];
		$this->LabelSuffix = $row['suffix'];
		$this->PHPData = $row['php_data'];
		
		
		//$row['value'] = '';
		//$row['orig_value'] = '';
		/*
		IMPORTANT FEAttribute extends ArrayObject
		
Mnogo neiasen bug. Kogato dobavih v getBeValue na ManagedImages da vrashta i $this["value"]["sizes"]=$this->ImageSizes;
Vsichko se pochupi, vsichki FEAttr instancii poluciha i te taia stoinost. Tova e pri situacia che predi tova ne im e izvikano izrichno $this['value'] = neshtosi.
Ne se izvikva, ako ne sme zaredili danni prez loadData.
Izglezda niakade copy on write mehanizma se pochupva pri naslediavane na ArrayObject
		
		*/
		
//		parent::__construct($row);
	}
	
	
	static function factory($row, AttributeGroup $grp){
		if($row['type']==1){
			return new FEAttributeInt($row, $grp);
		} elseif($row['type']==2){
			return new FEAttributeDec($row, $grp);
		} elseif($row['type']==3){
			return new FEAttributeStr($row, $grp);
		} elseif($row['type']==4){
			return new FEAttributeEnum($row, $grp);
		} elseif($row['type']==5) {
			return new FEAttributeSet($row, $grp);
		} elseif($row['type']==6) {
			return new FEAttributeBoolean($row, $grp);
		} elseif ($row['type']==7) {
			return new FEAttributeImg($row, $grp);
		} elseif ($row['type']==8) {
			return new FEAttributeFile($row, $grp);
		} elseif ($row['type']==10) {
			return new FEAttributeDate($row, $grp);
		} elseif ($row['type']==11) {
			$c = new FEAttributeDate($row, $grp);
			$c->Format .= ' '.TIME_FORMAT;
			return $c;
		} elseif ($row['type']==12 || $row['type']==16) {
			return new FEAttributeEnumNom($row, $grp);
		} elseif ($row['type']==13 || $row['type']==17) {
			return new FEAttributeSetNom($row, $grp);
		} elseif ($row['type']==14) {
			return new FEAttributeStr($row, $grp);
		} elseif ($row['type']==18) {
			return new FEAttributePHPData($row, $grp);
		} elseif ($row['type']==19) {
			return new FEAttributeOrderField($row, $grp);
		} elseif ($row['type']==20) {
			return new FEAttributeCascadedList($row, $grp);
		}
		
		return new FEAttribute($row, $grp);  // 9 - TinyMCE, 15 - Text,
	}
	
	function getSearchControl(){
		
	
		//$id= $this->Group->Id.'_'.$this->Id;
		$id= $this->Id;
		$Label = $this->LabelPrefix;
		$row['type'] = $this->Type;

		if($row['type']==1 || $row['type']==2){ // INT & Decimal
			$c = new ControlRangeInput($id, $Label);
		} elseif($row['type']==4){  //Enum
			$c = new ControlSelect($id, $Label);
			$c->Options = array(-1=>'',0=>'EMPTY')+$this->Options;
		} elseif($row['type']==5) {  //SET
			$c = new ControlMultipleSelect($id, $Label);
			$c->Options = array(0=>'EMPTY')+$this->Options;
		} elseif($row['type']==6 || $row['type']==7 || $row['type']==8) {  //boolean & Image & File
			$c = new ControlSelect($id, $Label);
			$c->Options = array(-1=>'',0=>'No',1=>'Yes');
		} elseif ($row['type']==10) { // Date
			$c = new ControlDateRangeInput($id, $Label);
		} elseif ($row['type']==11) { // Date Time
			$c = new ControlDateTimeRangeInput($id, $Label);
		} elseif ($row['type']==12) {  //EnumNom
			$c = new ControlIDRef($id, $Label);
			$c->RenderEmptyBtn = true;
			$c->SelectUrl = './?search=single&attribute_cluster_id='.$this->PHPData['nomenclature_ids'];
		} elseif ($row['type']==16) {  //EnumList
			$c = new ControlSelect($id, $Label);
			$c->Options = array(-1=>'',0=>'EMPTY')+getdb()->getAssoc($this->PHPData['sql']);
		} elseif ($row['type']==13) {  //SetNom
			$c = new ControlIDRef($id, $Label);
			$c->RenderEmptyBtn = true;
			$c->SelectUrl = './?search=multiple&attribute_cluster_id='.$this->PHPData['nomenclature_ids'];
		} elseif ($row['type']==14) {//autocomplete
			$c = new ControlAutoComplete($id, $Label);
			$c->PHPData=$this->PHPData;
			$c->attribute=$this; 
			$c->isSearch=1;
			$c->ClusterId=$this->Group->Cluster->AttributeClusterId;
		}elseif ($row['type']==17) {  //SetList
			$c = new ControlMultipleSelect($id, $Label);
			$c->Options = array(0=>'EMPTY')+getdb()->getAssoc($this->PHPData['sql']);
		} elseif ($row['type']==20) { //cascade list
			$c =  new ControlCascadedList($id, $Label,$this);			
			$c->PHPData=$this->PHPData;
			//$c->Options = array(''=>'')+$this->Options;
			$c->isSearch=1;
			$c->Options = array(''=>'',0=>'Empty')+getdb()->getAssoc($this->PHPData['sql']);
			$c->attribute=$this; 
		} else {
			$c = new ControlTextInput($id, $Label);
		}

		return $c;
	}
	
	
	function getEditControl(){
		
	
		//$id= $this->Group->Id.'_'.$this->Id;
		$id= $this->Id;
		$Label = $this->LabelPrefix;
		
		$row['type'] = $this->Type;
		

		if($row['type']==1 || $row['type']==2){ // INT & Decimal
			$c = new ControlTextInput($id, $Label);
		} elseif($row['type']==3){
			$c = new ControlTextInput($id, $Label);
		} elseif($row['type']==4){  //Enum
			$c = new ControlSelect($id, $Label);
			$c->Options = array(''=>'')+$this->Options;

		} elseif($row['type']==5 || $row['type']==17) {  //SET && SetList
			
			switch ($this->PHPData['render_type']){
				case 1 : $c = new ControlMultipleSelect($id, $Label); break;
				case 2 : $c = new ControlCheckBoxGroup($id, $Label); break;
				case 3 : $c = new ControlDoubleSelect($id, $Label); break;
			}
			
			
			$c->Options = $row['type']==5 ? $this->Options : getdb()->getAssoc($this->PHPData['sql']);
		} elseif($row['type']==6) {  //boolean
			$c = new ControlCheckBox($id, $Label);
		} elseif ($row['type']==7) {  //Image
			$c = new ControlManagedImage($id, $Label, $this);
			$c->Sizes = $this->ImageSizes;
		} elseif ($row['type']==8) {  //File
			$c = new ControlManagedFile($id, $Label);
		} elseif ($row['type']==9) {  //TinyMCE HTML
			$c = new ControlTinyMCE($id, $Label);
		} elseif ($row['type']==10) { // Date
			$c = new ControlDateInput($id, $Label);
			$c->Format = DATE_FORMAT;
		} elseif ($row['type']==11) { // Date Time
			$c = new ControlDateTimeInput($id, $Label);
			$c->Format = DATE_FORMAT.' '.TIME_FORMAT;
		} elseif ($row['type']==12) {  //EnumNom
			$c = new ControlIDRef($id, $Label);
			$c->SelectUrl = './?search=single&attribute_cluster_id='.$this->PHPData['nomenclature_ids'];
		} elseif ($row['type']==15) {  //Text
			$c = new ControlTextArea($id, $Label);
		} elseif ($row['type']==16) {  //EnumList
			$c = new ControlSelect($id, $Label);
			$c->Options = array(''=>'')+getdb()->getAssoc($this->PHPData['sql']);
		} elseif ($row['type']==13) {  //SetNom
			$c = new ControlIDRef($id, $Label);
			$c->SelectUrl = './?search=multiple&attribute_cluster_id='.$this->PHPData['nomenclature_ids'];
		} elseif ($row['type']==14) { //AutoComplete
			$c = new ControlAutoComplete($id, $Label);
			$c->PHPData=$this->PHPData;
			$c->attribute=$this; 
			$c->ClusterId=$this->Group->Cluster->AttributeClusterId;
		} elseif ($row['type']==18) { //PHP Data
			$c =  new ControlPHPData($id, $Label);
			$c->EditTemplate = $this->PHPData['EditTemplate'];
			$c->EditFile = $this->PHPData['EditFile'];
		} elseif ($row['type']==20) { //cascade list
			$c =  new ControlCascadedList($id, $Label,$this);			
			$c->PHPData=$this->PHPData;
			//$c->Options = array(''=>'')+$this->Options;
			$c->Options = array(''=>'')+getdb()->getAssoc($this->PHPData['sql']);
			$c->attribute=$this; 
		} else {
			$c = new ControlTextInput($id, $Label);
		}
		
		$c->Attributes = $this->PHPData['style'];
		if(!$this->PHPData['BEEditReadOnly'] && $this->PHPData['BEEditRequired']){
			$c->Required = true;
			$c->Attributes .= ' required="true"';
		}
		return $c;
	}
	

	function Validate($Value){
		if($this->isEmpty($Value)){
			if($this->PHPData['BEEditRequired']){
				return array("Field {$this->LabelPrefix} is required!");
			} else {
				return array();
			}
		}
		
		if($this->PHPData['reg_expression']){
			$regExp = $this->PHPData['reg_expression'];
			if(substr($regExp,0,1)!='/'){
				if(defined($regExp)) $regExp = '/'.constant($regExp).'/';
			}
			if(!preg_match($regExp, $Value)){
				return array($this->PHPData['reg_msg']);
			} else {
				return array();
			}
		}
	}
	
	static function isEmpty($Value){
		return empty($Value) && $Value!=='0' && $Value!==0;
	}
	
	function __toString(){
		return (string)$this->getFEValue();
	}
	
	function addWhere($where){
		$this->where[] = str_replace('value',"`{$this->AsTableName}`.`$this->fieldName`", $where);
	}
	
	function setSearchVal($val){
		$val = trim($val);
		if($val=='') return ;
		$db = getdb();
		$val = $db->escapeSimple($val);
		$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` LIKE '%$val%'";
	}

	function getFEValue(){
		return $this->Value;
	}

	function getBEListValue(){
		return $this->getFEValue();
	}
	
	function getBEEditValue(){
		return $this->Value;
	}
	
	function getSQL(){
		
		if(empty($this->where)) return ;
		$SQL = array();

		$name = $this->AsTableName;
		$SQL['table'] = $this->tableName.' AS '.$name;
		$where = $this->where;
		if($this->Group->Id<>1){
			$where[] = "products.id={$name}.product_id AND \n{$name}.group_id={$this->Group->Id} AND {$name}.attribute_id={$this->Id}";
		}
		$SQL['where'] = implode(" AND \n", $where);

		return $SQL;
	}
	
	function getOrderSQL(){
		if($this->Group->Id==$this->Group->Cluster->MainGroupId){
			return "`{$this->tableName}`.`{$this->fieldName}` {$this->order}";
		}
		if($this->Group->UseRealTable){
			return empty($this->where) ?
				"(SELECT `{$this->fieldName}` FROM `{$this->tableName}` WHERE product_id=products.id AND sub_id=0) {$this->order}" :
				"`{$this->tableName}`.`{$this->fieldName}` {$this->order}";
		}
		
		if(!empty($this->where)){
			$name = 'tbl'.$this->Group->Id.'_'.$this->Id;
			return "{$name}.`{$this->fieldName}` {$this->order}";
		}

		return "(SELECT `{$this->fieldName}` FROM `{$this->tableName}` WHERE product_id=products.id AND group_id={$this->Group->Id} AND attribute_id={$this->Id} AND sub_id=0) {$this->order}";
	}
	
	
	function DBSaveOnBefore(&$ClusterData){ }
	
	function DBSaveOnAfter($ClusterData){	}
	
	function DBDelete($product_id, $sub_id){
		if($this->Group->UseRealTable) return;
		$db = getdb();
		$db->Query("DELETE FROM {$this->tableName} WHERE product_id=? AND group_id=? AND attribute_id=? AND sub_id=?", array($product_id, $this->Group->Id, $this->Id, $sub_id));
		$db->Query("UPDATE {$this->tableName} SET sub_id = sub_id - 1 WHERE product_id=? AND group_id=? AND attribute_id=? AND sub_id>?", array($product_id, $this->Group->Id, $this->Id, $sub_id));
	}
	
	function DBGet($product_id, $sub_id){
		if($this->Group->Id == $this->Group->Cluster->MainGroupId){
			$row = getdb()->getRow("SELECT id, $this->fieldName FROM $this->tableName WHERE id=?", array($product_id));
		} else if($this->Group->UseRealTable) {
			$row = getdb()->getRow("SELECT id, $this->fieldName FROM $this->tableName WHERE product_id=? and sub_id=?", array($product_id, $sub_id));
		} else {
			$row = getdb()->getRow("SELECT id, $this->fieldName FROM $this->tableName WHERE product_id=? and group_id=? and sub_id=?", array($product_id, $this->Group->Id, $sub_id));
		}
		return $row;
	}
}


class FEAttributeInt extends FEAttribute {
	
	function setSearchVal($val){
		if($val['from']!==''){
			$val['from'] = CDB::escapeSimple($val['from']);
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` >= '{$val['from']}'" ;
		}
		
		if($val['to']!==''){
			$val['to'] = CDB::escapeSimple($val['to']);
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` <= '{$val['to']}'";
		}
	}
	
	function Validate($Value){
		$errors = parent::Validate($Value);
		if(!empty($errors) || $this->isEmpty($Value)) return $errors;
		if(!preg_match('/^[-+]?[0-9]+$/', $Value)) $errors[] = "Field {$this->LabelPrefix} is not valid Int!";
		$this->ValidateMinMax($Value, $errors);
		return $errors;
	}
	
	function ValidateMinMax($Value, $errors){
		if(empty($errors) && $this->PHPData['min_val'] && $Value<$this->PHPData['min_val']){
			$errors[] = "Field {$this->LabelPrefix} must be >= {$this->PHPData['min_val']}!";
		}
		
		if(empty($errors) && $this->PHPData['max_val'] && $Value>$this->PHPData['max_val']){
			$errors[] = "Field {$this->LabelPrefix} must be <= {$this->PHPData['max_val']}!";
		}
		
		return $errors;
	}
}

class FEAttributeDec extends FEAttributeInt  {
	
	function Validate($Value){
		$errors = FEAttribute::Validate($Value);
		if(!empty($errors) || $this->isEmpty($Value)) return $errors;
		if(!preg_match('/^[-+]?[0-9]+(\.[0-9]*)?$/', $Value)) $errors[] = "Field {$this->LabelPrefix} is not valid Decimal!";
		$this->ValidateMinMax($Value, $errors);
		return $errors;
	}
}

class FEAttributeStr extends FEAttribute {

	function getFEValue(){
		return htmlspecialchars($this->Value);
	}
}


class FEAttributeBoolean extends FEAttribute {
	
	function getFEValue(){
		return $GLOBALS['YES_NO'][$this->Value];
	}
	
	function setSearchVal($val){
		$val = (int)$val;
		if($val<0) return ;
		$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` = $val";
	}
}

class FEAttributeDate extends FEAttribute {
	public $Format = DATE_FORMAT;
	
	function getFEValue(){
		if($this->isEmpty($this->Value)) return '';
		return strftime($this->Format,strtotime($this->Value));
	}
	
	function setSearchVal($val){
		if(!$this->isEmpty($val['from'])){
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` >= '". CDB::escapeSimple($val['from'])."'";
		}
		
		if(!$this->isEmpty($val['to'])){
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` <= '".CDB::escapeSimple($val['to'])."'";
		}
	}
	
	static function isEmpty($Value){
		return parent::isEmpty($Value) || $Value=='0000-00-00' || $Value=='0000-00-00 00:00:00';
	}
	
	function Validate($Value){
		$errors = parent::Validate($Value);
		if(!empty($errors) || $this->isEmpty($Value)) return $errors;
		if(strtotime($Value)===false) $errors[] = "Field {$this->LabelPrefix} is not valid!";
		return $errors;
	}
}


class FEAttributeEnum extends FEAttribute { // Set & Enum
	public $Options=array();
	
	function __construct($row, AttributeGroup $grp){
		static $lngId=null;
		parent::__construct($row, $grp);
		
		if(is_null($lngId)){
			$lngKeys=array_flip(array_keys($GLOBALS['attribute_languages']));
			$lngId = $lngKeys[LNG_CURRENT]+1;
		}

		$row['options'] = explode("\r\n",$this->PHPData['options']);
		$tmp =array();
		foreach ($row['options'] as $k=>$v){
			$v=explode("|",$v);
			$tmp[(int)$v[0]] = htmlspecialchars($v[$lngId]);
		}
		
		if($this->PHPData['sort_alpha']){
			asort($tmp);
		}
		$this->Options = $tmp;
	}
	
	function getFEValue(){
		return $this->Options[$this->Value];
	}
	
	function setSearchVal($val){
		if($val==='') return ;
		$val = (int)$val;
		if($val<0) return ;
		$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` = $val";
	}
}

class FEAttributeSet extends FEAttributeEnum { // Set & Enum

	public $Separator = '<br />';

	function getFEValue(){
		$val = $this->Value;
		$ids = explode(',',$val);
		foreach ($ids as $k=>$v){
			$ids[$k] = $this->Options[$v];
		}
		return implode($this->Separator, $ids);
	}
	
	function setSearchVal($val){
		if($val==='') return ;
		$val = explode(',',$val);
		
		if($val[0]=='0') {
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName`=''";
			return ;
		}
		foreach ($val as $i){
			$this->where[] = "FIND_IN_SET('$i', `{$this->AsTableName}`.`$this->fieldName`)";
		}
	}
}


class FEAttributeEnumNom extends FEAttributeEnum { // TODO - expand tables and select other columns instead NAME

	public $ValueSQL = 'SELECT name FROM products WHERE id=_#VAL#_';

	function __construct($row, AttributeGroup $grp){
		FEAttribute::__construct($row, $grp);
		
		if(!empty($this->PHPData['display_sql'])){
			$this->ValueSQL = $this->PHPData['display_sql'];
		}
	}

	function getFEValue(){
		if($id = (int)$this->Value){
			$SQL = str_replace('_#VAL#_',$id,$this->ValueSQL);
			return getdb()->getOne($SQL);
		}
		return '';
	}
	
}

class FEAttributeSetNom extends FEAttributeSet { // TODO - expand tables and select other columns instead NAME
	
	public $OptionsSQL = "SELECT id,name FROM products WHERE find_in_set(id,'_#VAL#_')";

	
	function __construct($row, AttributeGroup $grp){
		FEAttribute::__construct($row, $grp);
		
		if(!empty($this->PHPData['display_sql'])){
			$this->OptionsSQL = $this->PHPData['display_sql'];
		}
	}
	
	function getFEValue(){
		
		if($this->Value){
			$SQL = str_replace('_#VAL#_', $this->Value, $this->OptionsSQL);
			$this->Options = getdb()->getAssoc($SQL);
		} else {
			$this->Options = array();
		}
		return parent::getFEValue();
	}
}


class FEAttributePHPData extends FEAttribute {

	function getBEListValue(){

		$Value = unserialize($this->Value);

		ob_start();
		if($this->PHPData['ListFile']){
			include($this->PHPData['ListFile']);
		} elseif($this->PHPData['ListTemplate']) {
			eval('?>'.$this->PHPData['ListTemplate'].'<?');
		}
		return ob_get_clean();
	}
}




class FEAttributeFile extends FEAttribute {
	
	protected $TmpName; // Stores uploaded file between BeforeSave & AfterSave
	public  $ParsedData = array();
	
	
	function expandData(){
		$Data = $this->ParsedData[$this->Group->CurrentSubId];
		if($Data) return $Data;
		$val = $this->Value;
		

		$Data=array();
		
		if($val) {
			$row = $this->Group->Cluster->ClusterLoadData[$this->Group->Id][$this->Group->CurrentSubId];
			$id = $this->Group->UseRealTable ? $row['id'] : $row[$this->Id]['id'];
			
			$ext = getFileExt($val);
			$Data['url'] = "{$GLOBALS['MANAGED_FILE_DIR_IMG']}{$this->tableName}/{$id}_{$this->fieldName}{$ext}";
			$Data['ext'] = $ext;
			$Data['name'] = $val;
		
			if($this->Group->UseRealTable && $this->PHPData['save_fields']){
				$Data['size'] = $row[$this->fieldName.'_size'];
				$Data['type'] = $row[$this->fieldName.'_type'];
			}
		}
		$this->ParsedData[$this->Group->CurrentSubId] = $Data;
		return $Data;
	}
	
	function setSearchVal($val){
		if($val==0){
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` = ''";
		} elseif($val==1){
			$this->where[] = "`{$this->AsTableName}`.`$this->fieldName` <> ''";
		}
	}
	
	function getFEValue(){
		if(!$this->Value) return '';
		$data = $this->expandData();
		"<a href='{$data['url']}' target='_blank'>{$data['name']}</a>";
	}
	
	function getBEEditValue(){
		if(!$this->Value) return array();
		return $this->expandData();
	}
	
	function DBSaveOnBefore(&$ClusterData){
		$row = &$ClusterData[$this->Group->Id][$this->Group->CurrentSubId];
		$tmp = $row[$this->Id];

		if($tmp['delete']){
			$row[$this->Id] = '';
			if($this->Group->UseRealTable && $this->PHPData['save_fields']){
				$row[$this->fieldName.'_size'] = '';
				$row[$this->fieldName.'_type'] = '';
			}
		} else if($tmp['name']) {
			$row[$this->Id] = $tmp['name'];
			$this->TmpName[$this->Group->CurrentSubId] = $tmp['tmp_name'];

			if($this->Group->UseRealTable && $this->PHPData['save_fields']){
				$row[$this->fieldName.'_size'] = $tmp['size'];
				$row[$this->fieldName.'_type'] = $tmp['type'];
			}
		} else {
			unset($row[$this->Id]);
			return ;
		}

		$this->UnlinkFile($row['product_id'], $row['sub_id']);
	}
	
	function DBSaveOnAfter($ClusterData){
		$row = $ClusterData[$this->Group->Id][$this->Group->CurrentSubId];
		$id = $this->RecordId[$this->Group->CurrentSubId];
		$this->SaveFile($id, $row[$this->Id]);
	}

	function SaveFile($id, $name){
		if($name){
			$ext = getFileExt($name);
			if(!is_dir("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/")){
				mkdir("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/");
			}
			
			@$res = move_uploaded_file($this->TmpName[$this->Group->CurrentSubId], "{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/{$id}_{$this->fieldName}{$ext}");
			if(!$res){
				$SQL = "UPDATE {$this->tableName} SET {$this->fieldName}='' WHERE id='{$id}'";
				getdb()->Query($SQL);
			}
		}
	}
	
	function DBDelete($product_id, $sub_id){
		$this->UnlinkFile($product_id, $sub_id);
		parent::DBDelete($product_id, $sub_id);
	}
	
	function UnlinkFile($product_id, $sub_id){
		$row = $this->DBGet($product_id, $sub_id);

		if(!empty($row[$this->fieldName])){
			$ext = getFileExt($row[$this->fieldName]);
			unlink("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/{$row['id']}_{$this->fieldName}{$ext}");
		}
	}
	
	function makeUrl($id, $ext){
		return "{$this->tableName}/{$id}_{$this->fieldName}{$ext}";
	}
	
	function Validate($Value){

		if($Value['name'] && !is_uploaded_file($Value['tmp_name'])){
			return array("Error while receiving file {$Value['name']} for field {$this->LabelPrefix}!");
		}
		return array();
	}
	
}

class FEAttributeImg extends FEAttributeFile {
	public $imageSize;
	public $ImageSizes = array();
	
	protected $TmpName; // Stores uploaded file between BeforeSave & AfterSave
	
	function __construct($row, AttributeGroup $grp){
		FEAttribute::__construct($row, $grp);

		$this->ImageSizes = $this->getSizes($this->PHPData['sizes']);
		reset($this->ImageSizes);
		$this->imageSize = key($this->ImageSizes);
	}
	
	function expandData(){
		$Data = $this->ParsedData[$this->Group->CurrentSubId];
		if($Data) return $Data;

		$val = $this->Value;

		$Data = array();
		if($val) {
			$row = $this->Group->Cluster->ClusterLoadData[$this->Group->Id][$this->Group->CurrentSubId];
			$id = $this->Group->UseRealTable ? $row['id'] : $row[$this->Id]['id'];
			$val = getFileExt($val);
			$Data['url'] = "/files/mf/{$this->tableName}/{$id}_{$this->fieldName}_{$this->imageSize}{$val}";
			foreach ($this->ImageSizes as $k=>$v) {
				$Data["urls"][$k]["url"]="/files/mf/{$this->tableName}/{$id}_{$this->fieldName}_{$k}{$val}";
				$Data["urls"][$k]["label"]=$v[2];
			}
			$Data['ext'] = $val;
			$Data['name'] = $this->Value;
			
			if($this->Group->UseRealTable && $this->PHPData['save_fields']){
				
				$Data['size'] = $row[$this->fieldName.'_size'];
				$Data['type'] = $row[$this->fieldName.'_type'];
			}
		}

		
		
		$this->ParsedData[$this->Group->CurrentSubId] = $Data;
		return $Data;
	}
	
	
	function getFEValue(){
		if($this->Value){
			$data = $this->expandData();
			return $data['url'];
		}
		return '';
	}
	function getBEListValue(){

		if($this->PHPData['BERenderType']==1){
			return $this->Value ? 'YES':'NO';
		} else if($this->PHPData['BERenderType']==2 && $this->Value){
			$data = $this->expandData();
			return "<img src='{$data['url']}' />";
		}

		return '';
	}

	static function getSizes($sizes) {
		$ImageSizes=array();
		if(empty($sizes)) {
			return array();
		}
		$sizes=explode("\n",$sizes);
		foreach ($sizes as $v) {
			$v=trim($v);
			if(empty($v)) {
				continue;
			}
			$v=explode("=",$v,2);
			$v1=explode("x",$v[1],3);
			$v2=isset($v1[2])?$v1[2]:"{$v1[0]}x{$v1[1]}";
			$ImageSizes[$v[0]]=array($v1[0],$v1[1],$v2);
		}
		return $ImageSizes;
	}
	
	function Validate($Value){
		if(!$Value['name']) return array();
		$e = parent::Validate($Value);
		if(!empty($e)) return $e;

		if(CPictures::getImageExtension($Value['tmp_name'])==''){
			return array("Invalid image type {$Value['type']} for file {$Value['name']}");
		}
	}

	function SaveFile($id, $name){
		if($name){
			if(!is_dir("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/")){
				mkdir("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/");
			}
			$ext = getFileExt($name);
			foreach ($this->ImageSizes as $k=>$v) {
				$e=CPictures::resizeImage($this->TmpName[$this->Group->CurrentSubId],"{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/{$id}_{$this->fieldName}_{$k}{$ext}",$v);
				if(!empty($e)) break;
			}
			
			if(!empty($e)){
				foreach ($this->ImageSizes as $k=>$v) {
					@unlink("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/{$id}_{$this->fieldName}_{$k}{$ext}");
				}
				$SQL = "UPDATE {$this->tableName} SET {$this->fieldName}='' WHERE id='{$id}'";
				getdb()->Query($SQL);
			}
		}
	}

	function UnlinkFile($product_id, $sub_id){
		$row = $this->DBGet($product_id, $sub_id);

		if(!empty($row[$this->fieldName])){
			$ext = getFileExt($row[$this->fieldName]);
			foreach ($this->ImageSizes as $k=>$v) {
				@unlink("{$GLOBALS['MANAGED_FILE_DIR']}{$this->tableName}/{$row['id']}_{$this->fieldName}_{$k}{$ext}");
			}
		}
	}
}

class FEAttributeCascadedList extends FEAttributeEnum { }












/*


ALTER TABLE `attribute_val_int` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_dec` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_str` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_txt` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_bln` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_dat` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);
ALTER TABLE `attribute_val_tim` DROP INDEX `attribute_id` , ADD INDEX `attribute_id` ( `attribute_id` , `product_id`);

---

ALTER TABLE `attribute_val_int` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;
ALTER TABLE `attribute_val_dec` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;
ALTER TABLE `attribute_val_str` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;
ALTER TABLE `attribute_val_bln` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;
ALTER TABLE `attribute_val_dat` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;
ALTER TABLE `attribute_val_tim` DROP INDEX `value` , ADD INDEX `value` ( `value` , `attribute_id` , `group_id` , `product_id` ) ;


*/


require_once(dirname(__FILE__).'/../be2/OrderField.php');

?>