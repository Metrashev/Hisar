<?php


class DataFormatsHelper {

	static function MysqlToIternal($row, $fields){

		foreach ($row as $k=>&$v){
			switch ($fields[$k]['type']){
				case 'Date' :
				case 'Time' :
				case 'DateTime' : $v = strtotime($v); break;
				case 'Set' : $v = $v!='' ? explode(',',$v) : array(); break;
			}
		}
		return $row;
	}

}


abstract class ABOBase {
	/**
	 * Enter description here...
	 *
	 * @var CDB
	 */
	protected $db = null;
	protected $lng;


	function __construct($lng=LNG_CURRENT){
		$this->db = getdb();
		$this->lng = $lng;
		
	}

	function getIDList($ids, $lng){
		//if(empty($ids)) return array();
		//$ids = implode(',',$ids);

		list($fields, $calcFields) = $this->prepareFieldsList('id,name', $lng);
		$fields = implode(',',$fields);
		$list = $this->db->getAssoc("SELECT $fields FROM services WHERE id IN ($ids)");
		return $list;
	}
/*
	
	function getFieldNamesForLng($fields, $lng){
		$resFields = array();
		foreach ($fields as $k=>$v){
			$v = trim($v);
			$as = '';
			if($this->lngFields[$v]){
				$resFields[$v] = $v."_{$lng}";
			} else {
				$resFields[] = $v;
			}
		}
		
		return $resFields;
	}
*/

	function prepareFieldsList($fields, $lng){

		$fields = explode(',',$fields);
		$toCalc = array();
		$resFields = array();
		foreach ($fields as $k=>$v){
			$v = trim($v);
			$as = '';
			if($this->fields[$v]['lng']){
				$lngSuffix = "_{$lng}";
				if(isset($this->fields[$v]['lngs'][$lng])){
					$lngSuffix = $this->fields[$v]['lngs'][$lng];
					if(isset($lngSuffix)) $lngSuffix = '_'.$lngSuffix;
				}
				
				$as = $v;
				$v .= $lngSuffix;
			}

			if($this->fields[$v]['SubSelect']){
				$resFields[$as] = $this->fields[$v]['SubSelect'];
			} else if($this->fields[$v]['calculated']) {
				$toCalc[$v] = $v;
			} else if($this->fields[$v]['type']=='ManagedImage') {
				$toCalc[$v] = $v;
				$resFields[] = $v;
			} else {

				if($as) {
					$resFields[$as] = $v;
				} else
					$resFields[] = $v;
			}
		}

		return array($resFields, $toCalc);
	}


	function prepareParams($fields, $SqlWhere='', $SqlOrder='', $lng=null){
		if(is_null($lng)) $lng = $this->lng;

		list($fields, $toCalc) = $this->prepareFieldsList($fields, $lng);

		$tableName = $this->tableName;


		$qb = new CSQLQueryBuilder();

		$qb->tables[] = $tableName;
		$qb->fields = $fields;

		if($SqlWhere) $qb->addWhere($SqlWhere);
		if($SqlOrder) $qb->order[] = $SqlOrder;

		if(!empty($toCalc)){
			$this->modifyQueryForCalc($toCalc, $qb);
		}

		$this->setFEConstrains($qb);
		return array($qb, $toCalc);
	}

	function getPagedList($href, $itemsPerPage, $fields, $SqlWhere='', $SqlOrder='', $lng=null){
		list($qb, $toCalc) = $this->prepareParams($fields, $SqlWhere, $SqlOrder, $lng);

		$fields = $qb->getFields();
		$qb->fields = array();
		$SQL = $qb->getSQL();

		$data1 = FEHelper::getPagedList($href, $itemsPerPage, $fields, $SQL);

		$data = $data1['data_list'];
		$data1['data_list'] = &$data;


		foreach ($data as &$row){
			$row = DataFormatsHelper::MysqlToIternal($row, $this->fields);
		}
		if(!empty($toCalc))
			$data = $this->calculateList($data, $toCalc);

		foreach ($data as &$row){
			$row = FEHelper::IternalToFe($row, $this->fields);
		}

		return $data1;
	}

	function getList($fields, $SqlWhere='', $SqlOrder='', $lng=null){


		list($qb, $toCalc) = $this->prepareParams($fields, $SqlWhere, $SqlOrder, $lng);
		$SQL = $qb->getSQL();

		$data = $this->db->getAll($SQL);

		foreach ($data as &$row){
			$row = DataFormatsHelper::MysqlToIternal($row, $this->fields);
		}
		if(!empty($toCalc))
			$data = $this->calculateList($data, $toCalc);

		foreach ($data as &$row){
			$row = FEHelper::IternalToFe($row, $this->fields);
		}

		return $data;
	}
	
	function getRangeList($fields, $ids){
		$d = $this->getList($fields, "id in ($ids)");
		$r = array();
		foreach ($d as $v) $r[$v['id']] = $v;
		return $r;
	}
	
	function _getList(CSQLQueryBuilder $qb){
		list($fields, $toCalc) = $this->prepareFieldsList($qb->getFields(), $this->lng);
		$qb->fields = $fields;
		if(!empty($toCalc)){
			$this->modifyQueryForCalc($toCalc, $qb);
		}
		$SQL = $qb->getSQL();
		$data = $this->db->getAll($SQL);
		foreach ($data as &$row){
			$row = DataFormatsHelper::MysqlToIternal($row, $this->fields);
		}
		if(!empty($toCalc))
			$data = $this->calculateList($data, $toCalc);

		foreach ($data as &$row){
			$row = FEHelper::IternalToFe($row, $this->fields);
		}

		return $data;
	}

	function getRowById($fields, $id){
		$id = (int)$id;
		return $this->getRow($fields, "id={$id}");
	}

	function getRow($fields, $SqlWhere='', $SqlOrder='', $lng=null){

		list($qb, $toCalc) = $this->prepareParams($fields, $SqlWhere, $SqlOrder, $lng);
		$qb->limit = 1;
		$SQL = $qb->getSQL();

		$row = $this->db->getRow($SQL);

		if(!$row) return false;

		$row = DataFormatsHelper::MysqlToIternal($row, $this->fields);

		if(!empty($toCalc))
			$row = reset($this->calculateList(array($row), $toCalc));

		$row = FEHelper::IternalToFe($row, $this->fields);

		return $row;
	}

	function getFEConstrains(){
		return '';
	}

	function setFEConstrains(CSQLQueryBuilder $SQL){
		$where = $this->getFEConstrains();
		if($where)
			$SQL->addWhere($where);
	}

	function modifyQueryForCalc($fieldsToCalc, CSQLQueryBuilder $qb){

		foreach ($fieldsToCalc as $field){
			$fieldData = $this->fields[$field];
			if($fieldData['refField']){
				$qb->fields[$field] = $fieldData['refField'];
			} else if($fieldData['type']=='ManagedImage'){
				$qb->fields['id'] = 'id';
			}
		}
	}


	function calculateList($data, $fieldsToCalc){
		if(empty($data)) return $data;


		$ids = array();
		$lists = array();
		$rangeFields = array();
		$listFields = array();
		$idFields = array();
		$ManagedFields = array();
		$boInsts = array();

		foreach ($fieldsToCalc as $field){
			$fieldData = $this->fields[$field];
			if($fieldData['ref']=='fullList'){
				if($fieldData['sql']){
					$list[$field] = $this->db->getAssoc($fieldData['sql']);
				} elseif ($fieldData['array']) {
					$list[$field] = $GLOBALS[$fieldData['array']];
				} elseif ($fieldData['fCall']) {
					$tmp = new $fieldData['fCall'][0];
					$list[$field] = call_user_func_array(array($tmp, $fieldData['fCall'][1]), $fieldData['fCall'][2]);
				}
				$listFields[] = $field;
			} elseif ($fieldData['ref']=='rangeList') {
				$rangeFields[] = $field;
				$listFields[] = $field;
			} elseif($fieldData['ref']=='id'){
				$idFields[$field] = $fieldData;
				if($fieldData['fCall']) $boInsts[$fieldData['fCall'][0]] = new $fieldData['fCall'][0];
			} else if($fieldData['type']=='ManagedImage') {
				$ManagedFields[$field] = $fieldData;
			}
		}

		if(!empty($rangeFields))
			foreach ($data as $row)
				foreach ($rangeFields as $field)
					$ids[$field][$row[$field]] = $row[$field];

		foreach ($rangeFields as $field){
			$fieldData = $this->fields[$field];
			if($fieldData['sql']){
				$fieldData['sql'] = str_replace('?', implode(',',$ids[$field]), $fieldData['sql']);
				$list[$field] = $this->db->getAssoc($fieldData['sql']);
			} elseif ($fieldData['fCall']) {
				$tmp = new $fieldData['fCall'][0];
				$fieldData['fCall'][2][] = implode(',',$ids[$field]);
				$list[$field] = call_user_func_array(array($tmp, $fieldData['fCall'][1]), $fieldData['fCall'][2]);
			}
		}

		foreach ($data as &$row){
			foreach ($listFields as $field)
				$row[$field] = $list[$field][$row[$field]];

			foreach ($idFields as $field=>$fieldData){
				//$fieldData = $this->fields[$field];
				if($fieldData['sql']){					
					$row[$field] = $fieldData['type']=='struct' ? $this->db->getRow($fieldData['sql'], array($row[$field])) : $this->db->getOne($fieldData['sql'], array($row[$field]));
				} elseif ($fieldData['fCall']) {
					//$tmp = new $fieldData['fCall'][0];
					$tmp = $boInsts[$fieldData['fCall'][0]];
					$fieldData['fCall'][2][] = $row[$field];

					$row[$field] = call_user_func_array(array($tmp, $fieldData['fCall'][1]), $fieldData['fCall'][2]);
				}
			}
			foreach ($ManagedFields as $field=>$fieldData){
				if($row[$field]=='') continue;
				$tmp = array();
				$tmp1 = $GLOBALS['MANAGED_FILE_DIR_IMG'].$this->tableName."/{$row['id']}_{$field}_";
				$tmp2 = $row[$field];
				foreach($fieldData['sizes'] as $size){
					$tmp[$size] = $tmp1.$size.$tmp2;
				}
				$row[$field] = $tmp;

			}			
		}


		return $data;
	}
	
	function getDefultHref($cid,$b,$c){
		$cidV = $this->lngData[$this->lng][$cid];
		return "?$cid=$cidV&amp;$b=$c";
	}
}


class CSQLQueryBuilder{

	public $method='SELECT';
	public $fields;
	public $tables;
	public $where;
	public $group;
	public $order;
	public $having;
	public $limit;
	public $limitFrom;
	
	public $joins;



	function addWhere($where){
		$this->where[] = $where;
	}

	static function parse($SQL){
		$qb = new self();
				$SQL = 'SELECT field, field AS field2 FROM tabl1, 
				tbl2 WHERE a>b and c<6 (SELECT a s(SELEC pol)ds) GROUP  	
				 BY field ASC, field2 DESC (SELECT dsffsd)';
				
		/* $matchSubQuery = '/\(SELECT ( (?>[^()]+) | (?R) )* \)/isx'; */
		$matchSubQuery = '/\(SELECT ( (?>[^()]+) | (?R) )* \)/isx';
		

		//preg_match('/(?:SELECT(.+))(?:FROM(.+))(?:WHERE(.+))(?:GROUP\s+BY(.+))(ORDER BY (.+))?(HAVING (.*))?( LIMIT ())?/is',$SQL, $res);
		preg_match_all($matchSubQuery,$SQL, $res);
		print_r($res);
	}
	
	function getFields(){
		if(empty($this->fields)) return '';
		$res = array();
		foreach ($this->fields as $k=>$v){
			if(is_numeric($k) || $k==$v){
				$res[] = $v;
			} else {
				$res[] = "$v AS $k";
			}
		}
		return implode(',',$res);
	}


	function getSQL(){

		$SQL = '';

		if(!empty($this->fields))
			$SQL .= 'SELECT '.$this->getFields();

		if(!empty($this->tables))
			$SQL .= ' FROM '.implode(',', $this->tables);

		if(!empty($this->where))
			$SQL .= ' WHERE ('.implode(') AND (', $this->where).')';

		if(!empty($this->group))
			$SQL .= ' GROUP BY '.implode(',', $this->group);

		if(!empty($this->order))
			$SQL .= ' ORDER BY '.implode(',', $this->order);

		if(!empty($this->having))
			$SQL .= ' HAVING ('.implode(') AND (', $this->having).')';

		if(!empty($this->limit)){
			$SQL .= ' LIMIT ';
			if(!empty($this->limitFrom)) $SQL .= $this->limitFrom.', ';
			$SQL .= $this->limit;

		}

		return $SQL;
	}

}


?>