<?php

/**
 * Return singleton instance of current db
 *
 * @return CDB
 */
function getdb() {
	static $db;

	if (is_null($db)) {
		$db = new CDB();
		$db->setDSN($GLOBALS['CONFIG']['DSN']);
		$db->open();
		if(!empty($GLOBALS['CONFIG']['NAMES_CHARACTERS_SET'])) {
			$db->Execute("set names {$GLOBALS['CONFIG']['NAMES_CHARACTERS_SET']}");
		}
	}

	return $db;
}

class CDB{
	private $host='';
	private $username='';
	private $password='';
	private $database='';
	private $connection=null;
	public $usePersistentConnection=false;
	public $LastRecordSetHandle=null;

	const AUTOQUERY_INSERT = 1;
	const AUTOQUERY_UPDATE = 2;
	const AUTOQUERY_INSERT_ON_DUPLICATE = 3;
	
	
	 /* za rabota s tranzakcii */
  
  private $is_in_transaction=0;
  
  function isTransactionStarted() {
  	return $this->is_in_transaction;
  }
  
  function beginTransaction() {
  	if(!$this->isTransactionStarted()) {
  		$this->Execute("start transaction");
  		$this->is_in_transaction=1;
  	}
  }
  
  function commit() {
  	if($this->isTransactionStarted()) {
  		$this->Execute("commit");
  		$this->is_in_transaction=0;
  	}
  }
  
  function rollback() {
  	if($this->isTransactionStarted()) {
  		$this->Execute("rollback");
  		$this->is_in_transaction=0;
  	}
  }
  
  /* END za rabota s tranzakcii */

	public function getConnection() {
		return $this->connection;
	}

	function setDataSourceName($database,$host,$user_name,$password) {
		$this->username=$user_name;
		$this->password=$password;
		$this->host=$host;
		$this->database=$database;
	}

	function setDSN($dsn){
		$dsn = parse_url($dsn);

		$this->username=$dsn['user'];
		$this->password=$dsn['pass'];
		$this->host=$dsn['host'];
		if($dsn['port']){
			$this->host .= ":".$dsn['port'];
		}
		$this->database=substr($dsn['path'], 1);
	}

	private function quoteColumnName($col) {
		$col = str_replace('`', '', $col);

		if (!ereg("\.", $col)) return "`$col`";

		return '`' . str_replace('.', '`.`', $col) . '`';
	}

	function open($new_link=false) {
		if(is_null($this->connection)) {
			if($this->usePersistentConnection) {
				$this->connection=mysql_pconnect($this->host,$this->username,$this->password);
			}
			else {
				$this->connection=mysql_connect($this->host,$this->username,$this->password, $new_link);
			}
			if(!$this->connection)
			throw new Exception(mysql_error());
			return $this->selectDB($this->database);
		}
		return true;
	}

	function selectDB($dbName){
		$this->database = $dbName;
		if($this->connection)
		return mysql_select_db($this->database, $this->connection);
	}

	function close() {
		if($this->connection) {
			$res = mysql_close($this->connection);
			$this->connection = null;
			return $res;
		}
	}

	function getDBName() {
		return $this->database;
	}


	function prepareExecute($SQL,$array) {


		$s=explode("?",$SQL);
		$str='';
		$counter=0;

		foreach ($array as $v) {
			if(is_null($v)) {
				$str.=$s[$counter++].'NULL';
			}
			else {
				$str.=$s[$counter++]."'". mysql_real_escape_string($v, $this->connection) ."'";
			}
		}

		for($i=$counter;$i<count($s);$i++) {
			$str.=$s[$i];
		}

		return $str;
	}

	public function __query($SQL, $params=null){
		if(is_array($params))
		$SQL = $this->prepareExecute($SQL, $params);

		@ $rs = mysql_query($SQL, $this->connection);

		if ($rs===false) {
			throw new CDB_QueryException(
				mysql_errno($this->connection),
				mysql_error($this->connection),
				$SQL);
		}

		$this->LastRecordSetHandle = $rs;
		return $rs;
	}

	function getAffectedRows(){
		return mysql_affected_rows($this->connection);
	}

	function getLastInsertId(){
		//return mysql_insert_id($this->connection);
		return $this->getOne('SELECT LAST_INSERT_ID()');
	}

	function Insert_ID() {
		return $this->getLastInsertId();
	}

	function get_id() {
		return $this->getLastInsertId();
	}

	function Execute($SQL, $params=null) {
		return $this->Query($SQL, $params);
	}

	function Query($SQL, $params=null) {
		$rs = $this->__query($SQL, $params);
		return is_bool($rs) ? $rs : new DBRecordSet($rs);
	}

	function getOne($SQL, $params=null) {
		$rs = $this->__query($SQL, $params);
		$row = mysql_fetch_row($rs);
		//mysql_free_result($rs);
		return is_bool($row) ? $row : $row[0];
	}

	function getCol($SQL, $params=null){
		$rs = $this->__query($SQL, $params);
		$col = array();
		while ($row = mysql_fetch_row($rs)){
			$col[] = $row[0];
		}
		//mysql_free_result($rs);
		return $col;
	}

	function getRow($SQL, $params=null){
		$rs = $this->__query($SQL, $params);
		$row = mysql_fetch_assoc($rs);
		//mysql_free_result($rs);
		return  $row;
	}

	function getAll($SQL, $params=null){

		$rs = $this->__query($SQL, $params);
		$col = array();
		while ($row = mysql_fetch_assoc($rs)){
			$col[] = $row;
		}
		//mysql_free_result($rs);
		return $col;
	}

	function getArray($SQL,$param=null) {
		return $this->getAll($SQL,$param);
	}

	function getAssoc($SQL, $params=null, $group=false,$fullnames=false) {
		$result=array();
		$rs = $this->__query($SQL, $params);
		if(!$rs)
		return $result;

		$colsNum = mysql_num_fields($rs);

		if($colsNum<3){
			if($group){
				while ($row = mysql_fetch_row($rs)) {
					if(!$fullnames) {
						$result[$row[0]][] = $row[1];
					}
					else {
						foreach ($row as $k=>$v) {

							$colName = mysql_field_name($rs, $k);
							$tableName=mysql_field_table($rs, $k);
							$result[$row[0]][$tableName.'.'.$colName][] = $row[$k];
						}
					}
				}
			} else {
				while ($row = mysql_fetch_row($rs)) {
					if(!$fullnames) {
						$result[$row[0]] = $row[1];
					}
					else {
						foreach ($row as $k=>$v) {

							$colName = mysql_field_name($rs, $k);
							$tableName=mysql_field_table($rs, $k);
							$result[$row[0]][$tableName.'.'.$colName] = $row[$k];
						}
					}
				}
			}
		} else {
			if(!$fullnames) {
				$colName = mysql_field_name($rs, 0);

				if($group){
					while ($row = mysql_fetch_assoc($rs))
					$result[$row[$colName]][] = $row;
				} else {
					while ($row = mysql_fetch_assoc($rs)) {
						$result[$row[$colName]] = $row;
					}
				}
			}
			else {
				$first_field=mysql_field_name($rs, 0);
				while ($row = mysql_fetch_row($rs)) {
					//{

					//  	$row = mysql_fetch_row($rs);
					if($group){
						//  while ($row = mysql_fetch_assoc($rs))
						foreach ($row as $k=>$v) {

							$colName = mysql_field_name($rs, $k);
							$tableName=mysql_field_table($rs, $k);
							$result[$row[0]][$tableName.'.'.$colName][] = $row[$k];
						}
					} else {
						//while ($row = mysql_fetch_assoc($rs)) {
						foreach ($row as $k=>$v) {

							$colName = mysql_field_name($rs, $k);
							$tableName=mysql_field_table($rs, $k);
							$result[$row[0]][$tableName.'.'.$colName] = $row[$k];
						}
						//	 $result[$row[$colName]] = $row;
					}
				}
			}
		}
		//mysql_free_result($rs);
		return $result;
	}

	function my_fetch_multitable($result) {
		$row = mysql_fetch_row($result);
		if (!is_array($row)) return null;

		foreach ($row as $k=>$v) {
			$result_row[mysql_field_table($result, $k)][mysql_field_name($result, $k)] = $v;
		}

		return $result_row;
	}


	function autoExecute($table_name, $data, $execute_type=1, $where='') {

		foreach ($data as $k=>$v) {
			$v = mysql_real_escape_string($v);
			$data[$k] = self::quoteColumnName($k)."='$v'";
		}

		switch ($execute_type) {

			case self::AUTOQUERY_UPDATE:
				$SQL = "UPDATE `$table_name` SET " . implode(', ', $data);
				if ($where) $SQL .= ' WHERE ' . $where;
				break;

			case self::AUTOQUERY_INSERT :
				$SQL = "INSERT INTO `$table_name` SET " . implode(', ', $data);
				break;
				
			case self::AUTOQUERY_INSERT_ON_DUPLICATE :
				$SQL = implode(', ', $data);
				$SQL = "INSERT INTO `$table_name` SET $SQL ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), $SQL";
				break;

		}
		//var_dump($SQL);
		$this->Query($SQL);

	}


	function getPivot($SQL, $params=null, $fields=array(), $unsetKeys=false){
		$result=array();
		$rs = $this->__query($SQL, $params);
		if(!$rs)
		return $result;

		if(count($fields)==0){
			$colsNum = mysql_num_fields($rs);
			while ($row = mysql_fetch_row($rs)){
				$tmp =& $result;
				for($i=0; $i<$colsNum; $i++){
					$tmp =& $tmp[$row[$i]];
				}
				$tmp++;
			}
		} else {
			while ($row = mysql_fetch_assoc($rs)){
				$tmp =& $result;
				foreach ($fields as $fn){
					$tmp =& $tmp[$row[$fn]];
					if($unsetKeys) unset($row[$fn]);
				}
				$tmp[] = $row;
			}
		}
		//mysql_free_result($rs);
		return $result;
	}


	static function escapeSimple($str) {
		return mysql_real_escape_string($str);
	}

}

class DBRecordSet implements iterator {
	public $rs;
	protected $key;
	protected $row = null;
	protected $affected_rows;

	function __construct($rs){
		$this->rs = $rs;
		$this->affected_rows = mysql_num_rows($rs);
		//    $this->key = $this->affected_rows;
	}

	function next(){
		$this->row = mysql_fetch_assoc($this->rs);
		$this->key++;
		return $this->current();
	}

	function current () {
		return $this->row;
	}

	function key ()  {
		return $this->key;
	}

	function rewind ()  {
		$this->key = 0;
		if($this->affected_rows>0){
			mysql_data_seek ($this->rs, 0 );
			$this->row = mysql_fetch_assoc($this->rs);
		}
	}

	function valid () {
		return  $this->row;
	}


	function fetchRow() {
		return $this->next();
	}

	function __destruct(){
		mysql_free_result($this->rs);
	}

}


class CDB_QueryException extends Exception {

	const ERROR_CODE_SERVER_DIED = 2006;
	const ERROR_CODE_PARSE_ERROR = 1064;
	const ERROR_CODE_DUPLICATE_ENTRY = 1062;
	const ERROR_CODE_AMBIGUOUS_COLUMN = 1052;

	public $sql_error_num;

	public $sql_error_msg;

	public function __construct($error_number, $error_message, $SQL) {
		$this->sql_error_num = $error_number;
		$this->sql_error_msg = $error_message;

		$message = <<<EOD
<p style="word-wrap:break-word;width:100%;left:0">
<b>{$error_number}</b>:<br>
{$error_message}<br>
{$SQL}
</p>
EOD;

		parent::__construct($message);
	}

}
