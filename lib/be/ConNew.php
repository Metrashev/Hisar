<?php

define("ENC_NONE",0);
define("ENC_HTML",1);
define("ENC_URL",2);

define('DIRECTION_HORIZONTAL',0);
define('DIRECTION_VERTICAL',1);

define(DEBUG,1);

define("POST_METHOD","POST");
define("GET_METHOD","GET");

define("EVENT_PAGE_CHANGE","EVENT_PAGE_CHANGE");
define("EVENT_ORDER_CHANGED","EVENT_ORDER_CHANGED");

define('BF_USE_DEFAULT_FORMATING',true);

define("ORDER_ASC","ASC");
define("ORDER_DESC","DESC");

/* SQL TYPES */
/* -------------------------------- */
define('DATA_VARCHAR','VARCHAR');
define('DATA_TINYINT','TINYINT');
define('DATA_TEXT','TEXT');
define('DATA_DATE','DATE');
define('DATA_SMALLINT','SMALLINT');
define('DATA_MEDIUMINT','MEDIUMINT');
define('DATA_INT','INT');
define('DATA_BIGINT','BIGINT');
define('DATA_FLOAT','FLOAT');
define('DATA_DOUBLE','DOUBLE');
define('DATA_DECIMAL','DECIMAL');
define('DATA_DATETIME','DATETIME');
define('DATA_TIMESTAMP','TIMESTAMP');
define('DATA_TIME','TIME');
define('DATA_YEAR','YEAR');
define('DATA_CHAR','CHAR');
define('DATA_TINYBLOB','TINYBLOB');
define('DATA_TINYTEXT','TINYTEXT');
define('DATA_BLOB','BLOB');
define('DATA_MEDIUMBLOB','MEDIUMBLOB');
define('DATA_MEDIUMTEXT','MEDIUMTEXT');
define('DATA_LONGBLOB','LONGBLOB');
define('DATA_LONGTEXT','LONGTEXT');
define('DATA_ENUM','ENUM');
define('DATA_SET','SET');
define('DATA_BINARY','BINARY');
define('DATA_VARBINARY','VARBINARY');


class FormatUtils {
	
	function stringParser($str,&$substr,$index=0,$search_for='%') {
		$i=strpos($str,$search_for,$index);
		if($i===false) {
			if(strlen($str)>$index) {
				//$substr=iconv('utf-8','windows-1251',substr($str,$index));
				$substr=substr($str,$index);
			}
			return -1;
		}
		$substr=substr($str,$index,$i-$index);
		return (int)$i;
	}
	
	static function decodeFormat($format,$value,$getArray=false,$emptyReturnVal='') {
		if(empty($format))
			return $value;
		if (empty($value)) {
			return $emptyReturnVal;
		}
		$arr=preg_split("/[\/\.\/\ \:]/",$value);
	
	  
		//$months_full=array(1=>"РЇРЅСѓР°СЂРё",2=>"Р¤РµРІСЂСѓР°СЂРё",3=>"РњР°СЂС‚",4=>"пїЅ?РїСЂРёР»",5=>"РњР°Р№",6=>"Р®РЅРё",7=>"Р®Р»Рё",8=>"пїЅ?РІРіСѓпїЅ?С‚",9=>"РЎРµРїС‚РµР�?РІСЂРё",10=>"РћРєС‚РѕР�?РІСЂРё",11=>"пїЅ?РѕРµР�?РІСЂРё",12=>"Р”РµРєРµР�?РІСЂРё");
		//$months_simp=array(1=>"РЇРЅСѓ",2=>"Р¤РµРІ",3=>"РњР°СЂ",4=>"пїЅ?РїСЂ",5=>"РњР°Р№",6=>"Р®РЅРё",7=>"Р®Р»Рё",8=>"пїЅ?РІРі",9=>"РЎРµРї",10=>"РћРєС‚",11=>"пїЅ?РѕРµ",12=>"Р”РµРє");
		$_day=0;
		$_month=0;
		$_year=0;
		$_hour=0;
		$_min=0;
		$_sec=0;
		$index=0;
		$val_pos=0;
		$hasTime=false;
		$hasDate=false;
		while($index>-1&&$index<strlen($format)) {
			$substr='';
			if(($index=self::stringParser($format,$substr,$index))!=-1) {
				$index++;
				$res=$val_pos+$index-1;
				if(strlen($format)>$index)
				switch ($format[$index++]) {
					case 'd': {
						$_day=intval($arr[$val_pos++]);
						$hasDate=true;
						break;
					}
					case 'a':
					case 'm':
					{
						$hasDate=true;
						$_month=intval($arr[$val_pos++]);
						break;
					}
					case 'O': {
						$hasDate=true;
						$_month=array_search($arr[$val_pos++],$months_full);
						break;
					}
					case 'b': {
						$hasDate=true;
						$_month=array_search($arr[$val_pos++],$months_simp);
						break;
					}
					case 'Y': {
						$hasDate=true;
						$_year=intval($arr[$val_pos++]);
						if($_year<1000&&$_year!=0)
							$_year+=2000;
						if($_year==0) {
							$_year='0000';	
						}
						//$_year=2000+intval($arr[$val_pos++]);
						break;
					}
					case 'y': {
						$hasDate=true;
						//$_year=2000+intval($arr[$val_pos++]);
						if($_year<1000)
							$_year+=2000;
						if($_year==0) {
							$_year='00';	
						}
						//$_year=2000+intval($arr[$val_pos++]);
						break;
						break;
					}
					case 'H': {
						$_hour=intval($arr[$val_pos++]);
						$hasTime=true;
						break;
					}
					case 'M':
					case 'i': {
						
						$_min=intval($arr[$val_pos++]);
						$hasTime=true;
						break;
					}
					case 's': {
						$_sec=intval($arr[$val_pos++]);
						$hasTime=true;
						break;
					}
					case 'f': 
					case 'n':
					{
						return floatval(str_replace(",","",$value));
					}
				}
			}
		}
		if(!$hasDate&&!$hasTime) {
			return '0000-00-00';
		}
		$result=array();
		if($hasDate) {
			$result[]=$_year.'-'.($_month<10?'0'.$_month:$_month).'-'.($_day<10?'0'.$_day:$_day);
			if($getArray) {
				$r_array['year']=$_year;
				$r_array['month']=$_month;
				$r_array['day']=$_day;
			}
		}
		if($hasTime) {
			$result[]=($_hour<10?'0'.$_hour:$_hour).':'.($_min<10?'0'.$_min:$_min).':'.($_sec<10?'0'.$_sec:$_sec);
			if($getArray) {
				$r_array['sec']=$_sec;
				$r_array['hour']=$_hour;
				$r_array['min']=$_min;
			}
		}
		
		return $getArray?array('string'=>implode(" ",$result),'struct'=>$r_array): implode(" ",$result);
	}
	
	static function translateFormat($format,$value,$inFormat='',$emptyReturnVal='') {
		if(empty($format))
			return $value;
		if(empty($value)) {
			return $emptyReturnVal;	
		}

		$months_full=array(1=>"РЇРЅСѓР°СЂРё",2=>"Р¤РµРІСЂСѓР°СЂРё",3=>"РњР°СЂС‚",4=>"пїЅ?РїСЂРёР»",5=>"РњР°Р№",6=>"Р®РЅРё",7=>"Р®Р»Рё",8=>"пїЅ?РІРіСѓпїЅ?С‚",9=>"РЎРµРїС‚РµР�?РІСЂРё",10=>"РћРєС‚РѕР�?РІСЂРё",11=>"пїЅ?РѕРµР�?РІСЂРё",12=>"Р”РµРєРµР�?РІСЂРё");
		$months_simp=array(1=>"РЇРЅСѓ",2=>"Р¤РµРІ",3=>"РњР°СЂ",4=>"пїЅ?РїСЂ",5=>"РњР°Р№",6=>"Р®РЅРё",7=>"Р®Р»Рё",8=>"пїЅ?РІРі",9=>"РЎРµРї",10=>"РћРєС‚",11=>"пїЅ?РѕРµ",12=>"Р”РµРє");
		$common=array('Y'=>'0',);
		//$result='';
		$index=0;
		while($index>-1&&$index<strlen($format)) {
			$substr='';
			if(($index=self::stringParser($format,$substr,$index))!=-1) {
				$index++;
				$result.=$substr;
				if(strlen($format)>$index)
				switch ($format[$index++]) {
					case 'd': {
						$result.=substr($value,8,2);
						break;
					}
					case 'D': {	//bez vide6ta nula
						$a=(int)substr($value,8,2);
						$result.=$a;
						break;
					}
					case 'a': {
						$result.=(int)substr($value,5,2);
						break;
					}
					case 'm': {
						$result.=substr($value,5,2);
						break;
					}
					//case 'M': {
					//	$result.=$months_full[(int)substr($value,5,2)];
					//	break;
					//}
					case 'b': {
						$result.=$months_simp[(int)substr($value,5,2)];
						break;
					}
					case 'Y': {
						$result.=substr($value,0,4);
						break;
					}
					case 'y': {
						$result.=substr($value,2,2);
						break;
					}
					case 'H': {
						$result.=substr($value,11,2);
						break;
					}
					case "M":
					case 'i': {
						$result.=substr($value,14,2);
						break;
					}
					case 's': {
						$result.=substr($value,17);
						break;
					}
					case 'f': {
						$result.=number_format($value,2);
						break;
					}
					case 'n': {
						$result.=number_format($value,0);
						break;
					}
				}
			}
			else {
				if(!empty($substr))
					$result.=$substr;
			}
			
		}
		return $result;
	}
	
}

class DataTable {
	public $Rows;
	public $columns;
	public $FormatStrings;
	
	public $OrderFields=array();
	protected $WhereFields=array();
	public $SelectFields='';
	public $HavingFields=array();
	public $GroupFields=array();
	public $Table='';
	public $Limit='';
	protected $m_isLoaded=false;
	public $CountExpresion='';
	public $CustomSelect='';
	public $CountRows=false;
	public $hasRollUp=false;
	public $is_static_order=false;
	public $Union="";
	
	public $ExtraOrderFields=array();
	
	
	function __construct() {
		$this->Rows=array();
		$this->columns=array();
		$this->FormatStrings=array();
	}
	
	/* WHERE FORMAT:
		$field=field name
		$value={
			array(
				0=>left_value,
				1=>right_value,
			);
			|
			value
		}
		$condition=operator
		Ex: name like 'wer'			=> $field='name',$condition='like',$value='wer'
		Ex: id between 1 and 10		=> $field='id' ,$condition='between',$value=array(0=>'1',1=>'10')
	*/
	
	function AddWhere($field,$condition='',$values='') {
		if(empty($condition)&&empty($values)) {
			$this->WhereFields[]=$field;
			return;
		}
		if(is_array($values)) {
			$v=array_values($values);
			$this->WhereFields[$field]['val']=$v[0];
			$this->WhereFields[$field]['right']['val']=$v[1];
		}
		else {
			$this->WhereFields[$field]['val']=$values;
		}
		$this->WhereFields[$field]['cond']=$condition;
	}
	
	function isLoaded() {
		return $this->m_isLoaded;
	}
	
	function getWhere() {
		$w=array();
		foreach ($this->WhereFields as $k=>$v) {
			if(is_array($v)&&isset($v['cond'])&&!empty($v['cond'])) {
				switch ($v['cond']) {
					case 'in': {
						if(!empty($v['val']))
							$w[]=$k." in({$v['val']})";
						break;
					}
					case 'like': {
						$w[]=$k." like '%{$v['val']}%'";
						break;
					}
					case 'between': {
						$w[]=$k." between {$v['val']} and ".$where[$v['right']]['val'];
						break;
					}
					default: {
						$w[]="{$k}{$v['cond']}{$v['val']}";
						break;
					}
				}
			}
			else {
				
				if(is_numeric($k)) {
					if(!empty($v)) {
						$w[]="(".$v.")";
					}
				}
			}
		}
		return implode(" AND ",$w);
	}
	
	function getCount() {
		if(!empty($this->CountExpresion)&&$this->CountExpresion=="DEFAULT") {
			$limit=$this->Limit;
			$this->Limit="";
			$res=$this->BuildQuery(null,true);
			$this->Limit=$limit;
			return getdb()->getAffectedRows();			
		}
		$where=$this->getWhere();
		$where=empty($where)?'':" where ".$where;
		$group=empty($this->GroupFields)?'':" group by ".implode(',',$this->GroupFields);
		$having=empty($this->HavingFields)?'':" having ".implode(',',$this->HavingFields);
		if($this->hasRollUp) {
			$group.=" with rollup ";	
		}
		if(!empty($this->CountExpresion)) {
			$SQL=str_replace(
				array("_#WHERE#_","_#GROUP#_","_#HAVING#_"),
				array($where,$group,$having),
				$this->CountExpresion
			);
			$SQL=trim($SQL);
		}
		else {
			$SQL=trim("select count(*) from {$this->Table}{$where}{$group}{$having}");
		}
		
		$db=getdb();
		/* @var $db CDB*/
		if($this->CountRows) {
			
			$res=$db->Query($SQL);
			return $db->getAffectedRows();
			//$res=$db->getAll($SQL);
			//return count($res);
		}
		return intval($db->getOne($SQL));
	}
	
	function &NewColumn($name,$type=DATA_VARCHAR) {
		$this->columns[$name]=$type;
		return $this->columns[$name];
	}
	
	function &NewRow() {
		$index=count($this->Rows);
		$this->Rows[$index]=array();
		foreach ($this->columns as $k=>$v) {
			$this->Rows[$index][$k]='';
		}
		return $this->Rows[$index];
	}
	
	protected function buildColumns() {
		if(isset($this->Rows[0])) {
			$counter=0;
			foreach ($this->Rows[0] as $k=>$v)
				$this->columns[$k]=$counter++;
		}
	}
	
	function BuildQuery($params=null,$asQuery=false) {
		$where=$this->getWhere();
		$where=empty($where)?'':" where ".$where;
		$group=empty($this->GroupFields)?'':" group by ".implode(',',$this->GroupFields);
		if(!$this->hasRollUp) {
			$or=empty($this->OrderFields)?'':implode(',',$this->OrderFields);
			$order=empty($or)?'':" order by ".$or;
		}
		$having=empty($this->HavingFields)?'':" having ".implode(',',$this->HavingFields);
		if($this->hasRollUp) {
			$group.=" with rollup ";	
			$order='';
		}
		if(!empty($this->CustomSelect)) {
			$SQL=str_replace("_#WHERE#_",$where,$this->CustomSelect);
			$SQL=trim($SQL.$group.$having.$order.' '.$this->Limit);
		}
		else {
			$sf=$this->SelectFields;
			if(!empty($this->ExtraOrderFields)) {
				$sf.=",".implode(',',$this->ExtraOrderFields);
			}
			if(!empty($this->Union)) {
				$SQL=trim("(select {$sf} from {$this->Table}{$where}{$group}{$having}) {$this->Union} {$order} {$this->Limit}");
			}
			else {
				$SQL=trim("select {$sf} from {$this->Table}{$where}{$group}{$having}{$order} {$this->Limit}");
			}
		}
		$this->load($SQL,$params,$asQuery);
	}
	
	function load($SQL,$params=null,$asQuery=false) {
		$db=getdb();
		/* @var $db CDB*/
		if(empty($params))
			$params=null;
		
		if(!$asQuery) {
			$this->Rows=$db->getAll($SQL,$params);
			$this->buildColumns();
		}
		else {
			$r=$db->getRow($SQL." limit 1",$params);
			$this->Rows=$db->Query($SQL,$params);			
			//$this->Rows=mysql_query($SQL, $db->getConnection());
			$counter=0;
			if(is_array($r)) {
				foreach ($r as $k=>$v) {
					$this->columns[$k]=$counter++;
				}
			}
		}
		$this->isLoaded=true;
	}
}

class Control_Utils {
	static function getGetString($get,$glue='&',$valueQuote="'",$enc_type=ENC_URL,$equ_string='=') {
		$arr=array();
		if(is_array($get)) {
			foreach ($get as $k=>$v) {
				switch ($enc_type) {
					case ENC_HTML: {
						$arr[]=$k.$equ_string.$valueQuote.htmlspecialchars($v).$valueQuote;
						break;
					}
					case ENC_URL: {
						if(is_array($v)) {
							continue;
						}
						else {
							$arr[]=$k.$equ_string.$valueQuote.urlencode($v).$valueQuote;							
						}
						break;
					}
					case ENC_NONE: {
						$arr[]=$k.$equ_string.$valueQuote.$v.$valueQuote;
						break;
					}
				}
				
			}
		}
		return implode($glue,$arr);
	}
	
	static function parseArrayName($name) {
		$v=explode('[',$name);
		$result=array();
		foreach ($v as $val) {
			if(!empty($val)) {
				$result[]=str_replace("]","",$val);
			}
		}
		return $result;
	}
	
	static function createArrayName($name_array,$set_first_brackets=false) {
		if(!is_array($name_array)) {
			return $name_array;
		}
		$a='';
		if($set_first_brackets) {
			return '['.implode('][',$name_array).']';
		}
		$a=array_values($name_array);
		$f=$a[0];
		unset($a[0]);
		return $f.'['.implode('][',$a).']';
	}
	
	function getPostArray($name,$postData=null) {
		$v=strpos($name,"[");
		if($v!==false) {
			$v="[".substr_replace($name,"][",$v,1);
		}
		if($postData==null) {
			$postData=$_POST;
		}
		$str='$postData'.$v;
		$str=str_replace("[]","",$str);
		$str=str_replace(array("[","]"),array("['","']"),$str);
		@eval("\$h=$str;");
		return $h;
		/*$name=explode("[",$name);
		if($postData==null) {
			$result=& $_POST;
		}
		else {
			$result=$postData;
		}
		if(!is_array($result))
			return;
		foreach ($name as $v) {
			$v=str_replace("]",'',$v);
			if(empty($v))
				continue;
			if(!is_array($result))
				break;
			$result=& $result[$v];
		}
		return $result;*/
	}
	
	function setPostArray($name,$value,$postData=null) {
		$v=strpos($name,"[");
		if($v!==false) {
			$v="[".substr_replace($name,"][",$v,1);
		}
		if($postData==null) {
			$postData=&$_POST;
		}
		$str='$postData'.$v;
		$str=str_replace("[]","",$str);
		//echo "{$str}->{$value}";
		//echo "<br />";
		//eval("\$postData[__attributes]['1_2_8']='.jpg';");
		@eval("$str=\$value;");	
		
		/*$name=explode("[",$name);
		if($postData==null) {
			$result=& $_POST;
		}
		else {
			$result=$postData;
		}
		if(!is_array($result))
			return;
		foreach ($name as $v) {
			$v=str_replace("]",'',$v);
			if(empty($v))
				continue;
			if(!is_array($result))
				break;
			$result=& $result[$v];
		}
		return $result;*/
	}
	
	static function array_merge_recursive_custom($arr1, $arr2) {
		$result = array();
	
		foreach ($arr1 as $k=>$v) {
			if (isset($arr2[$k])) {
				if (is_array($v) and is_array($arr2[$k])) {
					$result[$k] = self::array_merge_recursive_custom($v, $arr2[$k]);
				} else {
					$result[$k] = $arr2[$k];
				}
			} else {
				$result[$k] = $v;
			}
		}
	
		foreach ($arr2 as $k=>$v) {
			if (!isset($result[$k])) {
				$result[$k] = $v;
			}
		}
	
		return $result;
	}
}

class CommonControl {
	public $attributes=array();
	public $class_name='';
	public $control_id='';
	public $nodeValue='';
	public $tagName='';
	public $values=array();
	public $control_array=array();
	public $controlValue='';
	
	public $postName='';
	public $repeat_index=null;
	
	public $isReadOnly=false;
	public $is_required=false;
	
	public $validation_data=array();
	
	public $selectedValues=array();
	
	
	function __construct($control_array,$control_id='') {
		$this->control_array=$control_array;
		if(empty($control_id)) {
			$control_id=$control_array['bound_field'];
		}
		if(isset($control_array['repeat_index'])&&!empty($control_array['repeat_index'])) {
			$control_id.='_'.$control_array['repeat_index'];
			$this->repeat_index=$control_array['repeat_index'];
		}
		$this->control_id=$control_id;
		$this->process_autoload();
		//$this->processUserFunc();
		$this->setCommonAttributes();		
	}
	
	function setCommonAttributes() {
		$name=$this->control_array['name'];
		if(isset($this->control_array['repeat_index'])) {
			$name=str_replace("_#INDEX#_",$this->control_array['repeat_index'],$name);	
		}
		$this->addAttribute("name",$name);
		$this->postName=$name;
		$this->addAttribute("id",$this->control_id);
		
		if(isset($this->control_array['attributes'])&&is_array($this->control_array['attributes'])) {
			foreach($this->control_array['attributes'] as $k=>$v) {
				$this->addAttribute($k,$v);				
			}
		}
		
	}
	
	
	function getAttributesString() {
		if(!isset($this->attributes['class'])) {
			$class=get_class($this);
			if(isset($GLOBALS['control_classes'][$class])) {
				if($this->is_required) {
					$this->attributes['class']=$GLOBALS['control_classes'][$class].'_required';					
				}
				else {
					$this->attributes['class']=$GLOBALS['control_classes'][$class];
				}
			}
		}
		if(!empty($this->validation_data)&&is_array($this->validation_data)) {
			if(!is_array($this->attributes)) {
				$this->attributes=$this->validation_data;
			}
			else {
				$this->attributes+=$this->validation_data;
			}
		}
		if(!is_array($this->attributes)||empty($this->attributes)) {
			return '';
		}
		$str=Control_Utils::getGetString($this->attributes,' ','"',ENC_HTML,"=");
		return " ".$str;
	}
	
	function setValue($value) {
		//if($value) {
		$value=$this->processUserFunc($value);			
		//}
		$this->controlValue=$value;
	}
	
	function process_autoload() {
		if(isset($this->control_array['autoload'])&&is_array($this->control_array['autoload'])) {
			switch($this->control_array['autoload']['type']) {
				case 'sql': {
					$db=getdb();
					$array=$db->getassoc($this->control_array['autoload']['value']['DataSource']);
					break;
				}
				case 'arrayname': {
					if(is_array($this->control_array['autoload']['value']['DataSource'])) {
						$array=$this->control_array['autoload']['value']['DataSource'];
					}
					else {
						$array=array();
					}
					break;
				}
				case 'string': {
					$array=array(0=>$this->control_array['autoload']['value']['DataSource']);
					break;
				}
				case "user_func": {
					$array=call_user_func($this->control_array['autoload']['value']['DataSource']);
					break;
				}
				
				default: {
					$array=array(0=>'');
					break;
				}
			}
			if(isset($this->control_array['autoload']['value']['DataTextField'])) {
				$textField=$this->control_array['autoload']['value']['DataTextField'];				
			}
			reset($array);
			$test=current($array);
			if(is_array($test)) {
				foreach($array as $key=>&$v) {
					if(!isset($v[$textField])) {
						$tmp=array_keys($v);
						$textField=$tmp[0];
					}
					$v=$v[$textField];
				}
			}
			if(isset($this->control_array['autoload']['value']['addzero'])) {
				$key=$this->control_array['autoload']['value']['addzero']['key'];
				$value=$this->control_array['autoload']['value']['addzero']['value'];
				$position=isset($this->control_array['autoload']['value']['addzero']['position'])?$this->control_array['autoload']['value']['addzero']['position']:'top';
				if($position=='top') {
					if(is_array($key)) {
						
						$array=$key+$array;
					}
					else {
						$array=array($key=>$value)+$array;
					}
				}				
				else {
					if(is_array($key)) {
						$array=$array+$key;
					}
					else {
						$array[$key]=$value;
					}
				}
			}
			$this->values=$array;
		}
		else {
			//$values ne se promenq
		}
	}
	
	function processUserFunc($value) {
		if(!empty($this->control_array['userFunc'])) {
			$value=call_user_func($this->control_array['userFunc'],array($this,$value));		
		}
		return $value;
	}
	
	function getSingleValue() {
		$value=$this->controlValue;
		
				
		if(isset($this->control_array['FormatString'])) {
			$value=FormatUtils::translateFormat($this->control_array['FormatString'],$value);
		}
		return $value;
	}
	
	function addAttribute($name,$value) {
		if($name=="style") {
			$value=str_replace(',','',$value);
			$n=explode(':',$value);
			
			if(isset($this->attributes['style'])) {
				$v=explode(',',$this->attributes['style']);
				$st=array();
				$name_is_set=false;
				foreach($v as $val) {
					if(empty($val)) {
						continue;
					}
					$t=explode(':',$val);
					if($t[0]==$n[0]) {
						$st[]=$n[0].':'.$n[1];
						$name_is_set=true;
					}
					else {
						$st[]=$t[0].':'.$t[1];
					}
				}
				if(!$name_is_set) {
					$st[]=$n[0].':'.$n[1];
				}
				$this->attributes['style']=implode(';',$st);
			}
			else {
				$this->attributes['style']=$value;
			}		
		}
		else {
			$this->attributes[$name]=$value;
		}
	}
		
}

class Label extends CommonControl {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
	}
	
	function getValue() {		
		$value=$this->getSingleValue();
		if($this->control_array['isHTML']!==true) {
			$value=htmlspecialchars($value);
		}
		return $this->nodeValue=$value;
	}
	
	function getHTML() {
		return "<label".$this->getAttributesString().">".$this->getValue()."</label>";
	}
}

class Input extends CommonControl {
	function __construct($control_array,$control_id='',$type='text') {
		parent::__construct($control_array,$control_id);
		$this->addAttribute("type",$type);
	}
	
	function setValue($value) {
		if(isset($this->control_array['FormatString'])&&!empty($this->control_array['FormatString'])) {
			$value=FormatUtils::translateFormat($this->control_array['FormatString'],$value);
		}
  
		$this->addAttribute("value",$value);
	}
	
	function getHTML($read_only=false) {
		if($read_only) {
			if(isset($this->control_array['read_only_control'])) {			
				if(strtolower($this->control_array['read_only_control'])=='input') {
					$this->addAttribute("readonly","readonly");
				}
				else {
					if(empty($this->control_array['read_only_control'])) {
						return $this->attributes['value'];
					}
					else {
						return "<{$this->control_array['read_only_control']}".$this->getAttributesString().">".$this->attributes['value']."</{$this->control_array['read_only_control']}>";
					}
				}
			}
			else {
				if(isset($this->control_array['isHTML'])&&$this->control_array['isHTML']===true) {
					return $this->attributes['value'];
				}
				else {
					return htmlspecialchars($this->attributes['value']);
				}
			}
		}
		
		return "<input".$this->getAttributesString()." />";
	}
}

class Autocomplete extends Input {
	
	function getHTML($read_only=false) {
		if($read_only) {
			return parent::getHTML($read_only);
		}
		$bedir=BE_DIR;
		$js="";
		if(!defined("AUTOCOMPLETE_JS")) {
			$jsdir=JS_DIR;
			define("AUTOCOMPLETE_JS",1);
			$js=<<<EOD
<script type="text/javascript" src="{$jsdir}jquery/jquery-autocomplete/lib/jquery.js"></script>
<script type='text/javascript' src='{$jsdir}jquery/jquery-autocomplete/lib/jquery.bgiframe.min.js'></script>
<script type='text/javascript' src='{$jsdir}jquery/jquery-autocomplete/lib/jquery.ajaxQueue.js'></script>
<script type='text/javascript' src='{$jsdir}jquery/jquery-autocomplete/lib/thickbox-compressed.js'></script>
<script type='text/javascript' src='{$jsdir}jquery/jquery-autocomplete/jquery.autocomplete.js'></script>

<link rel="stylesheet" type="text/css" href="{$jsdir}jquery/jquery-autocomplete/main.css" />
<link rel="stylesheet" type="text/css" href="{$jsdir}jquery/jquery-autocomplete/jquery.autocomplete.css" />


EOD;
		}
		
		if(!empty($this->control_array['parameters']['depends_on'])) {
			$depends_on=",extraParams: {			
			{$this->control_array['parameters']['depends_on']}
		   }";
		}
		return "<input".$this->getAttributesString()." />".<<<EOD
{$js}
<script>
$("#{$this->control_id}").autocomplete("{$bedir}ajax/request.php", {
		selectFirst: true,
		max:20,		
		type:"POST"
		   {$depends_on}
	});
</script>
EOD;
	}
}

class SingleSelectButton extends Input {
	function __construct($control_array,$control_id='') {
		
		if(isset($_POST["sb_".$control_id])) {
			unset($_POST["sb_".$control_id]);
			if(isset($_GET['return_point'])) {
				CSessionStack::cleanEntry($_GET['return_point']);
			}
			$key=CSessionStack::addEntry($_POST);
			CSessionStack::addCaller($key,$control_id);
			if(!empty($control_array["parameters"]["filter"])) {
				$filter="&".$control_array["parameters"]["filter"];
			}
			header("Location: ".$control_array["parameters"]["select_url"]."&return_point={$key}{$filter}&bkp=".urlencode($_SERVER['REQUEST_URI']));
			exit;
		}
		
		
		parent::__construct($control_array,$control_id,"hidden");
				
	}
	
	function setValue($value) {
		if(isset($_POST["sbc_{$this->control_id}"])) {
			$this->addAttribute("value","");
			return;
		}
		if(CSessionStack::isReturn($_GET["return_point"])&&CSessionStack::getCaller($_GET["return_point"])==$this->control_id) {
			$this->addAttribute("value",$_GET["return_key"]);
		}
		else {
			if(isset($this->control_array['FormatString'])&&!empty($this->control_array['FormatString'])) {
				$value=FormatUtils::translateFormat($this->control_array['FormatString'],$value);
			}
	  		
			$this->addAttribute("value",$value);
		}
	}
	
	function getHTML($read_only=false) {
		if($read_only) {
			return "";
		}
		
		$value=$this->attributes['value'];
		
		$display_sql=$this->control_array['parameters']['display_sql'];
		if(empty($display_sql)&&DEBUG_MODE) {
			$value="No Display SQL SET";
		}
		else {
			if(!empty($display_sql)&&"{$value}"!="") {			
				$value=getdb()->getOne($display_sql,array($value));			
			}
			else {
				$value="";
			}
		}
		$str="<input".$this->getAttributesString()." />";
		$this->attributes=array();
		$str.=<<<EOD
		<table>
		<tr>
			<td style="padding:0px 5px;" {$this->getAttributesString()}>{$value}</td>
			<td valign='top'>
				<input type='submit' name='sb_{$this->control_id}' value="Select" />
				<input type='submit' name="sbc_{$this->control_id}" value="C" title="clear" onclick="document.getElementById('{$this->control_id}').value=''" />
			</td>
		</tr>
		</table>
EOD;
		return $str;
	}
}

class MultiSelectButton extends Input {
	function __construct($control_array,$control_id='') {
		if(isset($_POST["sb_".$control_id])&&!CSessionStack::isReturn($_GET["return_point"])) {
			unset($_POST["sb_".$control_id]);
			if(isset($_GET['return_point'])) {
				CSessionStack::cleanEntry($_GET['return_point']);
			}
			$key=CSessionStack::addEntry($_POST);
			CSessionStack::addCaller($key,$control_id);
			if(!empty($control_array["parameters"]["filter"])) {
				$filter="&".$control_array["parameters"]["filter"];
			}
			header("Location: ".$control_array["parameters"]["select_url"]."&return_point={$key}{$filter}&bkp=".urlencode($_SERVER['REQUEST_URI']));
			exit;
		}
		
		parent::__construct($control_array,$control_id,"hidden");		
	}
	
	function setValue($value) {
		if(isset($_POST["sbc_{$this->control_id}"])) {
			$this->addAttribute("value","");
			return;
		}
		if(CSessionStack::isReturn($_GET["return_point"])&&CSessionStack::getCaller($_GET["return_point"])==$this->control_id) {
			
			$this->addAttribute("value",$_GET["selected_keys"]);
		}
		else {
			$this->addAttribute("value",$value);
		}
	}
	
	function getHTML($read_only=false) {
		if($read_only) {
			return "";
		}
		
		$value=$this->attributes['value'];
		$display_sql=$this->control_array['parameters']['display_sql'];
		if(empty($display_sql)&&DEBUG_MODE) {
			$value="No Display SQL SET";
		}
		else {
			if(!empty($display_sql)&&"{$value}"!="") {			
				$value=getdb()->getcol(str_replace("_#VAL#_",$value,$display_sql));
				if(!empty($value)) {
					$value=implode(", ",$value);
				}
				
			}
			else {
				$value="";
			}
		}
		$str="<input".$this->getAttributesString()." />";
		$this->attributes=array();
		$str.=<<<EOD
		<table>
		<tr>
			<td style="padding:0px 5px;" {$this->getAttributesString()}>{$value}</td>
			<td valign='top'><input type='submit' name='sb_{$this->control_id}' value="Select" />
			<input type='submit' name="sbc_{$this->control_id}" value="C" title="clear" onclick="document.getElementById('{$this->control_id}').value=''" />
			</td>
		</tr>
		</table>
EOD;
		return $str;
	}
}



class Submit extends Input {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id,"submit");
		
	}
	
	function setValue($value) {
		if(isset($this->control_array['FormatString'])&&!empty($this->control_array['FormatString'])) {
			$value=FormatUtils::translateFormat($this->control_array['FormatString'],$value);
		}
  		
		$this->addAttribute("value",$this->control_array['Label']);
	}
	
	function getHTML($read_only=false) {
		if($read_only) {
			return "";
		}
		
		return "<input".$this->getAttributesString()." />";
	}
}

class CheckBox extends Input {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id,"checkbox");
	}
	
	function setValue($value) {
	
		
		$on_state='on';
		if(isset($this->control_array['states']['on'])) {
			$on_state=$this->control_array['states']['on'];
		}
		/*
		if((string)$value==(string)$on_state) {
			$this->addAttribute("checked","checked");
		}
		*/
		//dali da se adva kato value ???

		if(!is_null($value)) {
			if((string)$value===(string)$on_state||"$value"=="on") { 
				$this->addAttribute("checked","checked");
			}
		}
		if((string)$value==$on_state||"$value"=="on") {
			$value=$on_state;
			$this->addAttribute("value",$value);
		}
	}
	
	function getHTML($read_only=false) {
		if(!$read_only) {
			return parent::getHTML();
		}
		$on_state='on';
		if(isset($this->control_array['states']['on'])) {
			$on_state=$this->control_array['states']['on'];
		}
		$value=$this->attributes['value'];
		if("$value"=="$on_state"||"$value"=="on") {
			$checked=true;
		}
		else {
			$checked=false;
		}
		if($checked) {
			return "<b>&radic;</b>";
			//return "<img src='/be/i/checked.jpg' />";
		}
		return "";
		return $this->attributes["value"];
	}
}

class RadioButtons extends Input {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id,"radio");
	}
	
	function setValue($value) {
		$this->selectedValues=array(0=>$value);
	}
	
	function getHTML($read_only=false) {
		$br="&nbsp;&nbsp;&nbsp;";
		if(isset($this->control_array['direction'])&&$this->control_array['direction']==DIRECTION_VERTICAL) {
			$br="<br />";
		}
		$selected=isset($this->selectedValues[0])?$this->selectedValues[0]:"";
		unset($this->attributes['name']);
		unset($this->attributes['type']);

		if(!empty($this->validation_data)) {
			$obj_id=str_replace(array("[","]"),"_",$this->postName);
			$obj_id=' obj_id="'.$obj_id.'"';
			$indexes=range(0,count($this->values)-1);
			$indexes=implode(',',$indexes);
			$indexes=' indexes="'.$indexes.'"';
			$obj_to_validate=' obj_to_validate="radio"';
		}
		
		$str= "<span{$obj_id}{$indexes}{$obj_to_validate}".$this->getAttributesString().">";
		$index=0;
		
		foreach($this->values as $k=>$v) {
			$id=str_replace(array("[","]"),"_",$this->postName).'_'.$index;
			$index++;
			$checked=(string)$selected==(string)$k?" checked=\"checked\" ":"";
			if($read_only) {
				if($checked) {
					$str.="<b>[ &radic; ] {$v}</b>{$br}";
				}
				else {
					$str.="[&nbsp;&nbsp;&nbsp;&nbsp;] <label>{$v}</label>{$br}";
				}
			}
			else {	
				if(isset($this->control_array['input_attributes'])) {
					$extra_attr=$this->control_array['input_attributes'];
				}			
				else {
					$extra_attr="";
				}
				$str.="<input name=\"".$this->postName."\" id=\"{$id}\" type=\"radio\" {$extra_attr} value=\"".htmlspecialchars($k)."\"{$checked} /><label for=\"{$id}\">{$v}</label>{$br}";
			}
		}
		$str.="</span>";
		return $str;		
	}
}

class Select extends CommonControl {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
	}
	
	function setValue($value) {
		$this->selectedValues=array(0=>$value);
	}
	
	function getHTML($read_only=false) {
		if(!$read_only) {

			return "
			<select".$this->getAttributesString().">".CLib::draw_listbox_options($this->values,$this->selectedValues)."</select>
			";
		}
		
		$selected=array();
		
		if(is_array($this->selectedValues)) {
			foreach ($this->selectedValues as $k=>$v) {
				$selected[]=$this->values[$v];
			}
		}
		
		return implode(', ',$selected);
	}
}

class MultiSelect extends Select {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
		$this->addAttribute("multiple","true");
	}
	
	function setValue($value) {
		if(!is_array($value)) {
			$value=explode(',',$value);
		}
		$this->selectedValues=$value;
		
	}
	
}

class GroupCheckBox extends CommonControl {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
	}
	
	function setValue($value) {
		if(!is_array($value)) {
			$value=explode(',',$value);
		}
		$this->selectedValues=$value;
	}
	
function getHTML($read_only=false) {
		if(!$read_only) {
			
			$table=array();
			
			unset($this->attributes['value']);
			unset($this->attributes['type']);
			$name=$this->attributes['name'];
			$id=$this->attributes['id'];
			unset($this->attributes['name']);
			unset($this->attributes['id']);
			
			foreach ($this->values as $k=>$v) {
				if(in_array("$k",$this->selectedValues)) {
					$ch="checked";
				} else {
					$ch="";
				}
				
				$table[]="<input type=\"checkbox\"".$this->getAttributesString()." id=\"{$this->control_id}_{$k}\" value=\"{$k}\" name=\"{$name}[{$k}]\" {$ch} /><label for=\"{$this->control_id}_{$k}\">{$v}</label>";
			}
			$columns=1;
			if(isset($this->control_array['autoload']['value']['columns'])) {
				$columns=(int)$this->control_array['autoload']['value']['columns'];
				if($columns<1) {
					$columns=1;
				}
			}
			$table=FE_Utils::createTableCells($table,$columns);
			return <<<EOD
<table id="{$this->control_id}">
	{$table}
</table>
EOD;
		}
		
		$selected=array();
		if(is_array($this->selectedValues)) {
			foreach ($this->selectedValues as $k=>$v) {
				$selected[]=$this->values[$v];
			}
		}		
		return implode(', ',$selected);
	}
} 

class DoubleSelect extends Select {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
		//$this->addAttribute("multiple","true");
	}
	
	function setValue($value) {
		$this->selectedValues=$value;
	}
	
	function getHTML($read_only=false) {
		if(!$read_only) {
			$this->attributes['id']="li1_".$this->control_id;
			$name=$this->attributes['name'];
			unset($this->attributes['name']);
			
			$values=$this->values;
			$values1=array();
			if(!empty($this->selectedValues)) {
				$sel=explode(',',$this->selectedValues);			
				foreach ($sel as $k=>$v) {
					if(isset($this->values[$v])) {
						$values1[$v]=$this->values[$v];
						unset($values[$v]);
					}
				}
			}
			
			$dbl_clk=$this->attributes['ondblclick'];
			
			$str="<table>
				<tr>";
			
			$this->attributes['ondblclick']=$dbl_clk.";_add_list('{$this->control_id}');";
			$this->attributes['id']="li2_".$this->control_id;
			
			if(!isset($this->attributes['size'])) {
				$c=max(count($values),count($values1));
				if($c>40) {
					$c=40;
				}
				$this->addAttribute("size",$c);
			}
				
			$str.="
					<td valign=\"top\">
						<select".$this->getAttributesString().">".CLib::draw_listbox_options($values,null)."</select>
					</td>
					<td valign=\"middle\" style=\"width:20px;\">
						<input style=\"width:20px;text-align:center;\" type=\"button\" onclick=\"_remove_list('{$this->control_id}');\" value=\"&#171;\" />
						<br />
						<input style=\"width:20px;text-align:center;\" type=\"button\" onclick=\"_add_list('{$this->control_id}');\" value=\"&#187;\" />

					</td>";
				
				$this->attributes['ondblclick'].=";_remove_list('{$this->control_id}');";
			
				$this->attributes['id']="li1_".$this->control_id;
				$str.="<td valign=\"top\">
						<select".$this->getAttributesString().">".(empty($values1)?"":CLib::draw_listbox_options($values1,null))."</select>
						
					</td>
";
			if($this->control_array['use_reorder']) {
				$str.=<<<EOD
				<td valign="middle" style="width:10px;">
				<input type="button" onclick="_move_up('{$this->control_id}');" style="width:10px;" value="&#8593;" /><br />
				<input type="button" onclick="_move_down('{$this->control_id}');" style="width:10px;" value="&#8595;" />
				</td>
EOD;
			}
			$str.="	</tr>
			</table>
			";
			$this->attributes['name']=$name;
			$this->attributes['id']=$this->control_id;
			$this->attributes['ondblclick']=$dbl_clk;
			
			
			
			$str.="<input type=\"hidden\" name=\"{$name}\" id=\"{$this->control_id}\" value=\"{$this->selectedValues}\" />";
			return $str.$this->getScript();
		}
		
		$selected=array();
		if(is_array($this->selectedValues)) {
			foreach ($this->selectedValues as $k=>$v) {
				$selected[]=$this->values[$v];
			}
		}
		
		return implode(', ',$selected);
	}
	
	function getScript() {
		if(defined("double_list_script")) {
			return "";
		}
		define("double_list_script",1);
		return <<<EOD
<script>

		function _collectValues(li1,hd) {
			var com='';
			hd.value='';
			for(i=0;i<li1.options.length;i++) {
				hd.value+=com+li1.options[i].value;
				com=',';
			}	
		}
	
	function _remove_list(control_id) {
		var li1=document.getElementById('li1_'+control_id);
		var li2=document.getElementById('li2_'+control_id);
		var hd=document.getElementById(control_id);
		if(
			!li1||li1==undefined||
			!li2||li2==undefined||
			!hd||hd==undefined||
			li1=="undefined"||
			li2=="undefined"||
			hd=="undefined"
		)
		{
			return;
		}
		var i;
		for(i=li1.options.length-1;i>=0;i--)
		if(li1.options[i].selected) {
			var oOption = document.createElement("OPTION");
			var o=null;
			for(var t=0;t<li2.options.length;t++) {
				if(li2.options[t].text>li1.options[i].text) {
					o=li2.options[t];
					break;
				}
			}
			if(o) {
				li2.add(oOption,o);
			}
			else {
				li2.add(oOption);
			}
			//li2.options.add(oOption);
			oOption.text =li1.options[i].text;
			oOption.value = li1.options[i].value;
			li1.remove(i);
		}
		_collectValues(li1,hd);		
	}
	
	function _add_list(control_id) {
	
		var li1=document.getElementById('li1_'+control_id);
		var li2=document.getElementById('li2_'+control_id);
		var hd=document.getElementById(control_id);
	
		if(
			!li1||li1==undefined||
			!li2||li2==undefined||
			!hd||hd==undefined||
			li1=="undefined"||
			li2=="undefined"||
			hd=="undefined"
		)
		{
			return;
		}
		var i;
		var j;
		for(i=0;i<li2.options.length;i++)
		if(li2.options[i].selected) {
			var t=false;
			for(j=0;j<li1.options.length;j++)
				if(li1.options[j].value==li2.options[i].value) {
					t=true;
					break;;
				}
			if(t==true)
				continue;
			var oOption = document.createElement("OPTION");
	
			li1.options.add(oOption);
			oOption.text =li2.options[i].text;
			oOption.value = li2.options[i].value;
			li2.remove(i);
		}
		_collectValues(li1,hd);	
	}
		
		function  _move_up(control_id) {
			var li1=document.getElementById('li1_'+control_id);
			var hd=document.getElementById(control_id);
		
			if(
				!li1||li1==undefined||
				!hd||hd==undefined||
				li1=="undefined"||
				hd=="undefined"
			)
			{
				return;
			}
			if(li1.selectedIndex<1) {
				return;
			}
			var op=li1.options[li1.selectedIndex];
			var op1=li1.options[li1.selectedIndex-1];
			li1.remove(li1.selectedIndex);
			li1.add(op,op1);
			
			_collectValues(li1,hd);
		}
		
		function  _move_down(control_id) {
			
			var li1=document.getElementById('li1_'+control_id);
			var hd=document.getElementById(control_id);
		
			if(
				!li1||li1==undefined||
				!hd||hd==undefined||
				li1=="undefined"||
				hd=="undefined"
			)
			{
				return;
			}
			
			if(li1.selectedIndex>=li1.options.length-1) {
				return;
			}
			var op=li1.options[li1.selectedIndex+1];
			var op1=li1.options[li1.selectedIndex];
			li1.remove(li1.selectedIndex+1);
			li1.add(op,op1);
			_collectValues(li1,hd);
		}
</script>
EOD;
	}
	
}

class ManagedFile extends Input {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);		
	}
	
	function getHTML($read_only=false) {		
			if(!$read_only) {
				if(!empty($this->attributes['value'])) {				
					$ch=Control_Utils::getPostArray('ch_'.$this->postName);
				}
				if($ch=="on"||$ch==1) {
					$ch="checked";
				}
				$ext=FE_Utils::getFileExt($this->attributes['value']);
				if(empty($this->attributes['value'])) {
					$link="";
				}
				else {
					$link=$this->control_array['parameters']['view_dir'].$this->control_array['parameters']['table'].'/'.$this->control_array['parameters']['id'].'_'.$this->control_array['parameters']['field'].$ext;
				}
				$str_attr=$this->getAttributesString();
					$visible_attributes=$this->attributes;
					
					unset($visible_attributes['name']);
					unset($visible_attributes['type']);
					if(!is_array($visible_attributes)||empty($visible_attributes)) {
						$visible_attributes="";
					}
					else {
						$visible_attributes=" ".Control_Utils::getGetString($visible_attributes,' ','"',ENC_HTML,"=");
					}
				$display=empty($link)?"none":"inline";
				if(!empty($this->control_array['template'])) {
					if(is_file($this->control_array['template'])) {
						$str=file_get_contents($this->control_array['template']);
					}
					else {
						$str=$this->control_array['template'];
					}
					
					return str_replace(
						array("_#FILE#_","_#CH_DELETE#_","_#CH_LABEL#_","_#HREF#_","_#A_DISPLAY#_"),
						array("<input {$visible_attributes} type=\"file\" name=\"fl_{$this->postName}\" /><input type=\"hidden\" {$str_attr} />",
						"<input type=\"checkbox\" name=\"ch_{$this->postName}\" {$ch} id=\"ch_{$this->control_id}\" />",
						"ch_{$this->control_id}",$link,$display),
						$str
					);
				}
				
				return <<<EOD
				<table width="100%" cellpadding="3" cellspacing="0" border="0">
				<tr><td><input {$visible_attributes} type="file" name="fl_{$this->postName}" /><input type="hidden" {$str_attr} /></td></tr>
				<tr><td><input style="display:{$display}" type="checkbox" name="ch_{$this->postName}" {$ch} id="ch_{$this->control_id}" /><label style="display:{$display}" for="ch_{$this->control_id}">Delete</label>&nbsp;
<a href="{$link}" target="_blank" style="display:{$display}">view</a></td></tr>
</table>
				
EOD;
			}
			else {
				return "";
			}
	}
}

class ManagedVideo extends ManagedFile {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);		
	}
	
	function getHTML($read_only=false) {
		return parent::getHTML($read_only);
	}
}

class ManagedImage extends Input {
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);		
	}
	
	
	
	function getHTML($read_only=false) {		
		
			if(!$read_only) {
				$m_display="inline";
				if(!empty($this->attributes['value'])) {								
					$ch=Control_Utils::getPostArray('ch_'.$this->postName);
				}
				else {
					$m_display="none";
				}
				
				if($ch=="on"||$ch==1) {
					$ch="checked";
				}
				$links=array();
				$displays=array();
				$a=array();
				$SizeLabels = array();
				
				foreach ($this->control_array['parameters']['sizes'] as $k=>$v) {
					if(isset($v[2])) {
						$sz_label=$v[2];
					}
					else {
						$sz_label=$k;
					}
					if($v[2]) {
						$SizeLabels['_SL#'.$k."#_"]=$sz_label;
					}
					else {
						$SizeLabels['_SL#'.$k."#_"] = "{$sz_label}={$v[0]}x{$v[1]}";
					}
					if(empty($this->attributes['value'])) {
						$links['_#'.$k."#_"]="";
						$displays['_#A_'.$k.'#_']="none";
						$a["a_".$k]="";
					}
					else {
						$links['_#'.$k.'#_']=$this->control_array['parameters']['view_dir'].$this->control_array['parameters']['table'].'/'.$this->control_array['parameters']['id'].'_'.$this->control_array['parameters']['field'].'_'.$k.$this->attributes['value'];
						$displays['_#A_'.$k.'#_']="block";
						$view=!empty($v[2])?$v[2]:$k;
						$a["a_".$k]="<a target=\"_blank\" href=\"{$this->control_array['parameters']['view_dir']}{$this->control_array['parameters']['table']}/{$this->control_array['parameters']['id']}_{$this->control_array['parameters']['field']}_{$k}{$this->attributes['value']}\">{$view}</a>";
					}					
				}
				
				$str_attr=$this->getAttributesString();
				$visible_attributes=$this->attributes;
				
				unset($visible_attributes['name']);
				unset($visible_attributes['type']);
				if(!is_array($visible_attributes)||empty($visible_attributes)) {
					$visible_attributes="";
				}
				else {
					$visible_attributes=" ".Control_Utils::getGetString($visible_attributes,' ','"',ENC_HTML,"=");
				}
				
				$lk=array_keys($links);
				$dk=array_keys($displays);
				if(!empty($this->control_array['template'])) {
					if(is_file($this->control_array['template'])) {
						$str=file_get_contents($this->control_array['template']);
					}
					else {
						$str=$this->control_array['template'];
					}
					
					
					return str_replace(
						array("_#DISPLAY#_","_#FILE#_","_#CH_DELETE#_","_#CH_LABEL#_")+$lk+$dk,
						array($m_display,"<input {$visible_attributes} type=\"file\" name=\"fl_{$this->postName}\" /><input type=\"hidden\" {$str_attr} />",
						"<input style=\"display:{$m_display}\" type=\"checkbox\" name=\"ch_{$this->postName}\" {$ch} id=\"ch_{$this->control_id}\" />",
						"ch_{$this->control_id}")+$links+$displays,
						$str
					);
				}
				if(!empty($this->attributes['value'])) {
					
					$SizeLabels ="";
				}
				else {
					$SizeLabels = implode(', ', $SizeLabels);
				}
				$r= <<<EOD
				<table width="100%" cellpadding="3" cellspacing="0" border="0">
				<tr><td colspan="2"><input {$visible_attributes} type="file" name="fl_{$this->postName}" /><input type="hidden" {$str_attr} /><br/>$SizeLabels</td></tr>
				<tr style="display:{$m_display};"><td colspan="2"><input style="display:{$m_display}" type="checkbox" name="ch_{$this->postName}" {$ch} id="ch_{$this->control_id}" /><label style="display:{$m_display}" for="ch_{$this->control_id}">Delete</label><br />

				
EOD;
				if($this->control_array['parameters']['overwrite']==true) {
					$r.="</td></tr>";
					
					foreach ($this->control_array['parameters']['sizes'] as $k=>$v) {
						$view=!empty($v[2])?$v[2]:$k;
						$r.="<tr style=\"display:{$displays['_#A_'.$k.'#_']}\">
						<td><a target=\"_blank\" href=\"{$this->control_array['parameters']['view_dir']}{$this->control_array['parameters']['table']}/{$this->control_array['parameters']['id']}_{$this->control_array['parameters']['field']}_{$k}{$this->attributes['value']}\">{$view}</a></td>
						<td><input {$visible_attributes} type=\"file\" name=\"{$k}_fl_{$this->postName}\" /></td></tr>";
					}
				}
				else {
					$r.="&nbsp;".implode("&nbsp;",$a);
					$r.="</td></tr>";
				}
				$r.="</table>";
				
				if(FLASH_UPLOAD_ENABLED) {
					$a=array();
					$b=array();
					$s=array();
					foreach ($this->control_array['parameters']['sizes'] as $k=>$v) {
						$s[]=$k;
						$b[]="{s:'$k',s1:'{$v[0]}',s2:'{$v[1]}'}";
						$a[]=<<<EOD
<input type="hidden" name="h_data[{$this->control_id}][{$k}]" value="{$_POST['h_data'][$this->control_id][$k]}" id="h_{$k}_fl_{$this->control_id}" />
EOD;
					}					
					$a=implode($a);
					$b=implode(',',$b);
					$s=implode(',',$s);
					$nm=htmlspecialchars($_POST['h_data'][$this->control_id]["fln_name"]);
					$r.=<<<EOD
<input type="hidden" id="sz_{$this->control_id}" value="{$s}" />
<input type="hidden" id="h_fl_{$this->control_id}_name" name="h_data[{$this->control_id}][fln_name]" value="{$nm}" />
					{$a}<a href="#" onclick="showFlashUpload('{$this->control_id}',new Array($b));return false;">Image Upload Editor</a>
EOD;
				}
				
				return $r;
			}
			else {
				return "";
			}
	}
}


class DateControl extends Input {
	protected  $is_time=false;
	
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
		$this->addAttribute("style","width:80px");
	}
	
	function setValue($value) {
		$separator=$this->control_array['FormatString'];
		if(!empty($separator)) {
			$separator=substr($separator,2,1);
		}
		if(strpos($value,$separator)!==false) {
			$this->addAttribute("value",$value);
		}
		else {
			$this->addAttribute("value",FormatUtils::translateFormat($this->control_array['FormatString'],$value));
		}
	}
	

	function getHTML($read_only=false) {
		$name=$this->control_id;
		if($read_only) {
			return parent::getHTML($read_only);
		}
		
		$time=$this->is_time?"true":"false";
		$format = str_replace('%i', '%M', $this->control_array['FormatString']);
		return <<<EOD
		<div style="white-space:nowrap"><input{$this->getAttributesString()} />
<input type="button" value="" class="button_calendar" id="ib_{$name}"/></div>
<script>
Calendar.setup({inputField     :    '$name',          // id of the input field
          ifFormat       :    '{$format}',                          // format of the input field
          showsTime      :    {$time},                          // will display a time selector
          button         :    'ib_$name', // trigger for the calendar (button ID)
          singleClick    :    true,                                // double-click mode
          step           :    1                                    // show all years in drop-down boxes (instead of every other year as default)
        });
</script>
EOD;
	}
}

class TextArea extends CommonControl {
	public $is_html=false;
	function __construct($control_array,$control_id='') {
		parent::__construct($control_array,$control_id);
		if(isset($control_array['isHTML'])) {
			$this->is_html=$control_array['isHTML'];
		}
	}
	
	function setValue($value) {
		if(isset($this->control_array['FormatString'])&&!empty($this->control_array['FormatString'])) {
			$value=FormatUtils::translateFormat($this->control_array['FormatString'],$value);
		}
		
  		$this->controlValue=$value;
  		
		//$this->addAttribute("value",$value);
	}
	
	function getHTML($read_only) {
		if($this->is_html) {
			if($read_only) {
				return $this->controlValue;
			}			
			return "<textarea".$this->getAttributesString().">".$this->controlValue."</textarea>";
		}
		else {
			if($read_only) {
				return nl2br(htmlspecialchars($this->controlValue));
			}
			
			return "<textarea".$this->getAttributesString().">".htmlspecialchars($this->controlValue)."</textarea>";
		}
	}
}

class DateTimeControl extends DateControl {
	function __construct($control_array,$control_id='') {
		$this->is_time=true;
		parent::__construct($control_array,$control_id);
		$this->addAttribute("style","width:105px");
	}
}

class ControlValues {
	
	function createPostName($name,$index=null) {
		
		if(!is_null($index)) {
			$name=str_replace("_#INDEX#_",$index,$name);	
		}
		return $name;
	}
	
	function collectData($array,$postData,$useWrite=true,$skipFormatting=false,$validate=true,$repeat_index=null,$use_bound_field=false) {
		$controls=$array['controls'];
		$result=array();
		$errors=array();
		foreach ($controls as $k=>$v) {
			
			if($useWrite&&!isset($v['write_data'])) {
				continue;
			}
			
			$wd=$useWrite?$v['write_data']:$v['control'];
			$c=$v['control'];
			if($use_bound_field) {
				$k=$c['bound_field'];
			}
			//$input_value=Control_Utils::getPostArray($c['name'],$postData);
			$input_value=Control_Utils::getPostArray(ControlValues::createPostName($c['name'],$repeat_index),$postData);
			$skip=0;
			if(!isset($input_value)) {
				if(strtolower($c['attributes']['type'])=='checkbox'||$c['tagName']=='CheckBox') {
					$input_value=0;
				}
				else {
					
					switch ($c['tagName']) {
						case "GroupCheckBox":
						case "MultiSelect": {
							$input_value="";
							break;
						}
						default: {
							$skip=1;								
							break;;
						}
					}					
				}
			}
			if($skip==1) {
				
				continue;
			}
			if(strtolower($c['attributes']['type'])=='checkbox'||$c['tagName']=='CheckBox') {
				$input_value=!empty($input_value)?(isset($c['states']['on'])?$c['states']['on']:1):(isset($c['states']['off'])?$c['states']['off']:0);
			}
			$result[$k]['input']=$input_value;
			
			if(!$skipFormatting&&!empty($c['FormatString'])) {
			//	var_dump($c['FormatString']);
				$output_array=FormatUtils::decodeFormat($c['FormatString'],$input_value,true);
					
				$result[$k]['output']=$output_array['string'];
			}
			else {
				$output_array=$result[$k]['output']=$input_value;
			}
			$is_real_empty=(string)$input_value=='';
			if(!$is_real_empty&&isset($wd['invalid_values'])&&is_array($wd['invalid_values'])) {
				$is_real_empty=in_array("$input_value",$wd['invalid_values']);
			}
			
			//if ($wd['required']&&empty($input_value)) {
			if ($wd['required']&&$is_real_empty) {
				if(isset($array['translation_func'])&&!empty($array['translation_func'])) {
					if(isset($wd['req_message'])) {
						$errors[$k]=call_user_func($array['translation_func'],$wd['req_message']);
					}
					else {
						$errors[$k]=call_user_func($array['translation_func'],"Required field").' <b>'.call_user_func($array['translation_func'],$c['Label']).'</b> '.call_user_func($array['translation_func'],"left empty").'!';
					}
				}
				else {
					$errors[$k]=isset($wd['req_message'])?$wd['req_message']:"Required field <b>{$c['Label']}</b> left empty!";
				}
			}
			if($validate&&$c['tagName']!="ManagedImage") {
				
				//$err=self::validateType($wd['type'],$output_array,isset($wd['signed'])&&$wd['signed'],(int)$wd['size'],isset($c['accept_zero'])&&$c['accept_zero']==true);
				$err=self::validateType($wd['type'],$output_array,isset($wd['signed'])&&$wd['signed'],(int)$wd['size'],$wd['required']!=true);
				if($err!==true) {
					$errors[$k]=$err." <b>{$c['Label']}</b>";
				}
				else {
					if(!empty($wd["regex"]["pattern"])&&!empty($output_array)) {
						$err=preg_match($wd["regex"]["pattern"],$output_array);
						if(!$err) {
							if(!empty($wd["regex"]["msg"])) {
								$errors[$k]=str_replace("_#FIELD#_","<b>{$c['Label']}</b>",$wd["regex"]["msg"]);
							}
							else {
								$errors[$k]="<b>{$c['Label']}</b> does not match";
							}
						}
					}
				}
			}
		}
		return array('data'=>$result,'errors'=>$errors);
	}
	
function validateType($type,$value,$signed,$size=0,$acceptZeroDate=false) {
		$errors=array(
			DATA_DATE=>'Invalid date format!',
		);
		if(is_array($value)) {
			$array=$value['struct'];
			$value=$value['string'];
		}
		else {
			$array='';
		}
		switch (($type)) {
			case DATA_DATETIME:
			case DATA_DATE: {
			
				if(is_array($array)) {
					if($acceptZeroDate&&!(int)$array['month']&&!(int)$array['day']) {
						return true;
					}
					else {
						$fl=checkdate((int)$array['month'],(int)$array['day'],(int)$array['year']);
					}
				}
				else {
					if(empty($value))
						$fl=$acceptZeroDate;
					else {
						$fl=strtotime($value)!==-1;
					}

				}
				return $fl?true:"Invalid date";
			}
			case DATA_TINYINT: {
				if(empty($value))
					return true;
				return DBValidator::isValidTinyint($value,$signed);
				break;
			}
			case DATA_SMALLINT: {
				return DBValidator::isValidSmallint($value,$signed);
			}
			case DATA_MEDIUMINT: {
				return DBValidator::isValidMediumint($value,$signed);
			}
			case DATA_INT: {
				if(empty($value))
					return true;
				return DBValidator::isValidInt($value,$signed);
				break;
			}
			case DATA_BIGINT: {
				return DBValidator::isValidBigInt($value,$signed);
			}
			case DATA_TEXT:
			case DATA_VARCHAR: {
				return DBValidator::isValidString($value,$size);
			}
			case DATA_FLOAT:
			case DATA_DECIMAL:
			case DATA_DOUBLE:
			{
				if(empty($value))
					return true;
				return DBValidator::isValidFloat($value,$size);
			}
			default: {
				return true;
			}
		}
	}
	
	
	function getWriteData($array,$postData,$repeat_index=null,$use_bound_field=false) {
		$result=ControlValues::collectData($array,$postData,true,false,true,$repeat_index,$use_bound_field);
		if(is_array($result['errors'])&&!empty($result['errors'])) {
			return array('data'=>false,'errors'=>$result['errors']);
		}
		$write_data=array();
		foreach ($result['data'] as $k=>$v) {
			$write_data[$k]=is_array($v['output'])?implode(',',$v['output']):$v['output'];
		}
		return array('data'=>$write_data,'errors'=>false);
	}
	
	static function getFileArrayName($postName) {
			$name=$postName;
			$ch_pos=strpos($name,"[");
			$res=array();
			if($ch_pos!==false) {
				$start=substr($name,0,$ch_pos);
				$end=substr($name,$ch_pos);
				$res['name']=$start."[name]".$end;
				$res['type']=$start."[type]".$end;
				$res['tmp_name']=$start."[tmp_name]".$end;
				$res['size']=$start."[size]".$end;
			}
			else {
				$res['name']="[name]".$name;
				$res['tmp_name']="[tmp_name]".$name;
				$res['type']="[type]".$name;
				$res['size']="[size]".$name;
			}
			return $res;
		}
		
	
	
	static function processManagedFiles($id,$files,$controls,$update_db=true,$repeat_index=null) {
		$db=getdb();
		$errors=array();
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
			
			if($v['tagName']=='ManagedFile') {
				if(!isset($v['parameters'])||!isset($v['parameters']['dir'])||!isset($v['parameters']['table'])||!isset($v['parameters']['field'])) {
					continue;
				}
				
				$postName=ControlValues::createPostName($v['name'],$repeat_index);
				
				$file_names=ControlValues::getFileArrayName($postName);
				//Control_Utils::getPostArray("fl_".$file_names['name'],$files);
	
				
				$ch_val=Control_Utils::getPostArray("ch_".$postName);
				if($ch_val=='on') {	//delete
					$filename=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
					$e=FE_Utils::getFileExt($filename);
					if(file_exists($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$e)) {
						@$d=unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$e);
						if(!$d) {
							$errors[]="Cannot delete file {$filename}!";
						}
						else {
							if($update_db) {
								
								$fields_to_write=array(
									"{$v['parameters']['field']}=''"
								);
								if(is_array($v['parameters']['save_fields'])) {									
									$f=array(
									//	"file_name"=>1,
										"file_size"=>1,
										"file_type"=>1,
									);
									foreach ($f as $fk=>$fv) {
										if(isset($v['parameters']['save_fields'][$fk])) {
											$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=''";									
										}
									}
								}
								$fields_to_write=implode(',',$fields_to_write);
								
								$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id=?",array($id));
								//$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}='' where id=?",array($id));
								
							}
						}
					}
					Control_Utils::setPostArray($postName,'');
					Control_Utils::setPostArray("ch_".$postName,'');					
				}
				$tmp_name=Control_Utils::getPostArray("fl_".$file_names['tmp_name'],$files);
				if(!is_uploaded_file($tmp_name)) {
					continue;
				}
				else {
					if(!is_dir($v['parameters']['dir'].$v['parameters']['table'])) {
						@$d=mkdir($v['parameters']['dir'].$v['parameters']['table'],0777);
						if(!$d) {
							$errors[$v['parameters']['table']]="Cannot create directory!";
							continue;
						}
						chmod($v['parameters']['dir'].$v['parameters']['table'],0777);
					}
					$file_name=Control_Utils::getPostArray("fl_".$file_names['name'],$files);
					$file_size=Control_Utils::getPostArray("fl_".$file_names['size'],$files);
					$file_type=Control_Utils::getPostArray("fl_".$file_names['type'],$files);
					$ext=FE_Utils::getFileExt($file_name);
					
					@$d=move_uploaded_file($tmp_name,$v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$ext);
					if(!$d) {
						$errors[$v['parameters']['field']]="Cannot upload file <b>{$v['Label']}</b>!";
						continue;
					}
					chmod($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$ext,0777);
					$old_ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
					$old_ext=FE_Utils::getFileExt($old_ext);
					if($old_ext!=$ext) {
						@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$old_ext);
					}
					if($update_db) {
						
						$data_to_write=array($file_name);
						$fields_to_write=array(
							"{$v['parameters']['field']}=?"
						);
						if(is_array($v['parameters']['save_fields'])) {									
							$f=array(
								//"file_name"=>$file_name,
								"file_size"=>$file_size,
								"file_type"=>$file_type,
							);
							foreach ($f as $fk=>$fv) {
								if(isset($v['parameters']['save_fields'][$fk])) {
									$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=?";
									$data_to_write[]=$fv;
								}
							}
						}
						$fields_to_write=implode(',',$fields_to_write);
						
						$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id='{$id}'",$data_to_write);
						//$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}=? where id=?",array($file_name,$id));
						Control_Utils::setPostArray($postName,$file_name);
					}				
				}
			}
		}
		return $errors;
	}
	
	static function processManagedVideo($id,$files,$controls,$update_db=true,$repeat_index=null) {

		$db=getdb();
		$errors=array();
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
			
			if($v['tagName']=='ManagedVideo') {
				if(!isset($v['parameters'])||!isset($v['parameters']['dir'])||!isset($v['parameters']['table'])||!isset($v['parameters']['field'])) {
					continue;
				}

				$postName=ControlValues::createPostName($v['name'],$repeat_index);
				
				$file_names=ControlValues::getFileArrayName($postName);
				//Control_Utils::getPostArray("fl_".$file_names['name'],$files);
	
				
				$ch_val=Control_Utils::getPostArray("ch_".$postName);
				if($ch_val=='on') {	//delete
					$filename=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
					$e=FE_Utils::getFileExt($filename);
					if(file_exists($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$e)) {
						@$d=unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$e);
						if(!$d) {
							$errors[]="Cannot delete file {$filename}!";
						}
						else {
							if($update_db) {
								$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}='' where id=?",array($id));
								
							}
						}
					}
					Control_Utils::setPostArray($postName,'');
					Control_Utils::setPostArray("ch_".$postName,'');					
				}

				$tmp_name=Control_Utils::getPostArray("fl_".$file_names['tmp_name'],$files);

				if(!is_uploaded_file($tmp_name)) {
					continue;
				}
				else {
					if(!is_dir($v['parameters']['dir'].$v['parameters']['table'])) {
						@$d=mkdir($v['parameters']['dir'].$v['parameters']['table'],0777);
						if(!$d) {
							$errors[$v['parameters']['table']]="Cannot create directory!";
							continue;
						}
						chmod($v['parameters']['dir'].$v['parameters']['table'],0777);
					}
					$file_name=Control_Utils::getPostArray("fl_".$file_names['name'],$files);
					$file_size=Control_Utils::getPostArray("fl_".$file_names['size'],$files);
					$file_type=Control_Utils::getPostArray("fl_".$file_names['type'],$files);
					$ext=FE_Utils::getFileExt($file_name);
					
					if(!in_array($ext,$GLOBALS['VALID_VIDEO_EXTENSIONS'])) {
						$errors[$v['parameters']['field']]="Invalid upload video format <b>{$v['Label']}</b>!";
						continue;
					}
					
					$size=ControlValues::getVideoSize($tmp_name);

					$s_param="";
					if(!empty($size)&&$size[0]!=0&&$size[1]!=0) {
						$s_param=ControlValues::getVideoResizeString($size[0],$size[1],$v['parameters']['sizes']);
					}
					//$d=move_uploaded_file($tmp_name,$v['parameters']['dir'].$v['parameters']['table'].'/'.$file_name);
					
					$gg="{$v['parameters']['dir']}{$v['parameters']['table']}/{$id}_{$v['parameters']['field']}{$v['parameters']['convert_params']['output_format']}";
					if($ext==$v['parameters']['convert_params']['output_format']&&$size[0]<=$v['parameters']['sizes'][0]&&$size[1]<=$v['parameters']['sizes'][1]) {
						//copy 
						@move_uploaded_file($tmp_name,$gg);
						
					}
					else {
						exec("{$GLOBALS['FFMPEG_FILE']} -i {$tmp_name}{$s_param} {$v['parameters']['convert_params']['convert_line']} {$gg} 2>&1",$result,$val);
					}
					
					/*@$d=move_uploaded_file($tmp_name,$v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$ext);
					if(!$d) {
						$errors[$v['parameters']['field']]="Cannot upload file <b>{$v['Label']}</b>!";
						continue;
					}
					*/
					$file_name=pathinfo($file_name);
					$ext=$v['parameters']['convert_params']['output_format'];
					$file_name=$file_name['filename'].$ext;
					
					chmod($gg,0777);
					$old_ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
					$old_ext=FE_Utils::getFileExt($old_ext);
					if($old_ext!=$ext) {
						
						@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$old_ext);
					}
					if($update_db) {
						$extra_set="";
						$data_to_write=array($ext);
						$fields_to_write=array(
							"{$v['parameters']['field']}=?"
						);
						if(is_array($v['parameters']['save_fields'])) {									
							$f=array(
								"file_name"=>$file_name,
								"file_size"=>$file_size,
								"file_type"=>$file_type,
							);
							foreach ($f as $fk=>$fv) {
								if(isset($v['parameters']['save_fields'][$fk])) {
									$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=?";
									$data_to_write[]=$fv;
								}
							}
						}
						$fields_to_write=implode(',',$fields_to_write);
						$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id='{$id}'",$data_to_write);
						//$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}=? where id=?",array($file_name,$id));
						Control_Utils::setPostArray($postName,$ext);
					}				
				}
			}
		}
		return $errors;
	}
	
	static function prepareImageIdField($id) {
		if(is_array($id)) {
			$a=array();
			$a['fields']=array_keys($id);
			$a["ids"]=array_values($id);
			$s=array();
			$db=getdb();	//za da raboti mysql_real_escape_string, ina4e $db ne ni trqbwa
			foreach ($id as $k=>$v) {
				$s[]="`$k`='".mysql_real_escape_string($v)."'";
			}
			$a["string"]=implode(" AND ",$s);
			return $a;
		}
		return $id;
	}
	
	static function hasFileToProcess($name,$files,$repeat_index=null) {	
		$postName=ControlValues::createPostName($name,$repeat_index);
		$file_names=ControlValues::getFileArrayName($postName);
		$tmp_name=Control_Utils::getPostArray("fl_".$file_names['tmp_name'],$files);
		$ch_val=Control_Utils::getPostArray("ch_".$postName);
		if($ch_val=='on') {	//delete
			
			return true;
		}
		return is_uploaded_file($tmp_name);		
	}
	
	static function createDir($dir) {
		if(!is_dir($GLOBALS['MANAGED_FILE_DIR'])) {
			@$d=mkdir($GLOBALS['MANAGED_FILE_DIR'],0777);
			if(!$d) {
				return "Cannot create directory MF!";
				
			}
			chmod($GLOBALS['MANAGED_FILE_DIR'],0777);
		}
		if(!is_dir($dir)) {
			@$d=mkdir($dir,0777);
			if(!$d) {
				return "Cannot create directory!";				
			}
			chmod($dir,0777);
		}
		return "";
	}
	
	static function processManagedImages($id,$files,$controls,$update_db=true,$repeat_index=null) {
		$db=getdb();
		$errors=array();
		
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
				
			if($v['tagName']=='ManagedImage') {

				if(!isset($v['parameters'])||!isset($v['parameters']['dir'])||!isset($v['parameters']['table'])||!isset($v['parameters']['field'])) {
					continue;
				}
				//$v['name']=$f_name;
				$postName=ControlValues::createPostName($v['name'],$repeat_index);
				
				$file_names=ControlValues::getFileArrayName($postName);
				//Control_Utils::getPostArray("fl_".$file_names['name'],$files);
	
				
				$ch_val=Control_Utils::getPostArray("ch_".$postName);
				
				if($ch_val=='on') {	//delete
					$e=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
					//$file name e extention
					//$e=FE_Utils::getFileExt($filename);
					foreach ($v['parameters']['sizes'] as $pk=>$pv) {
						if(file_exists($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$e)) {
							@$d=unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$e);
							if(!$d) {
								$errors[]="Cannot delete file <b>{$v['Label']}</b>!";
							}
						}
					}
					if($update_db) {
						$fields_to_write=array(
							"{$v['parameters']['field']}=''"
						);
						if(is_array($v['parameters']['save_fields'])) {									
							$f=array(
								"file_name"=>1,
								"file_size"=>1,
								"file_type"=>1,
							);
							foreach ($f as $fk=>$fv) {
								if(isset($v['parameters']['save_fields'][$fk])) {
									$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=''";									
								}
							}
						}
						$fields_to_write=implode(',',$fields_to_write);
						$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id=?",array($id));
						Control_Utils::setPostArray($postName,'');
					}							
					Control_Utils::setPostArray("ch_".$postName,'');					
				}
				
				$er=ControlValues::createDir($v['parameters']['dir'].$v['parameters']['table']);
				if(!empty($er)) {
					$errors[]=$er;
					continue;
				}
				
				/* UPLOAD FROM FLASH */
				
				if($new_ext=ControlValues::uploadFlash($id,$_POST,$k,$v,$update_db,$repeat_index)) {
					//ima upload ot flash, ka4vame nego i prenebregvame input file controlata
					Control_Utils::setPostArray($postName,$new_ext);
					continue;
				}
				
				/* UPLOAD FROM FLASH */
				
				$tmp_name=Control_Utils::getPostArray("fl_".$file_names['tmp_name'],$files);
				$file_name=Control_Utils::getPostArray("fl_".$file_names['name'],$files);
				$file_size=Control_Utils::getPostArray("fl_".$file_names['size'],$files);
				$file_type=Control_Utils::getPostArray("fl_".$file_names['type'],$files);
				
				
				if(!is_uploaded_file($tmp_name)) {
					if($v['parameters']['overwrite']) {
						$err=array();
						$ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
						foreach ($v['parameters']['sizes'] as $pk=>$pv) {
							$t=Control_Utils::getPostArray($pk."_fl_".$file_names['tmp_name'],$files);
							if(is_uploaded_file($t)) {
								$err=ControlValues::resizeImage($t,$v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext,$pv,$v['parameters']['resize']);
								if(!empty($err)) {
									@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext);
								}
							}
						}
						if(!empty($err)) {
							$errors+=$err;
						}
					}
					continue;
				}
				else {
					
					$ext=CPictures::getImageExtension($tmp_name);
					if(!CPictures::isAcceptableImage($ext)) {
						
						$errors[]="Invalid image -> <b>{$file_name}</b>";
					}
					else {
						
						$err=array();
						foreach ($v['parameters']['sizes'] as $pk=>$pv) {
							$err=ControlValues::resizeImage($tmp_name,$v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext,$pv,$v['parameters']['resize']);
							if(!empty($err)) {
								@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext);
							}							
						}
						if(empty($err)) {
							$old_ext=$db->getone("select `{$v['parameters']['field']}` from {$v['parameters']['table']} where id=?",array($id));
							if($old_ext!=$ext) {
								foreach ($v['parameters']['sizes'] as $pk=>$pv) {
									@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$old_ext);
								}
							}
							if($update_db) {
								$extra_set="";
								$data_to_write=array($ext);
								$fields_to_write=array(
									"{$v['parameters']['field']}=?"
								);
								if(is_array($v['parameters']['save_fields'])) {									
									$f=array(
										"file_name"=>$file_name,
										"file_size"=>$file_size,
										"file_type"=>$file_type,
									);
									foreach ($f as $fk=>$fv) {
										if(isset($v['parameters']['save_fields'][$fk])) {
											$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=?";
											$data_to_write[]=$fv;
										}
									}
								}
								$fields_to_write=implode(',',$fields_to_write);
								
								$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id='{$id}'",$data_to_write);
								
								Control_Utils::setPostArray($postName,$ext);
							}
						}
						else {
							$errors+=$err;
						}						
					}						
				}
			}
		}
		return $errors;
	}
	
	static function uploadFlash($id,$postData,$control_id,$control,$update_db=true,$repeat_index=null) {		
		if(!isset($postData["h_data"])||!is_array($postData["h_data"])||!isset($postData["h_data"][$control_id])||
			!isset($postData["h_data"][$control_id]["fln_name"])||empty($postData["h_data"][$control_id]["fln_name"])) 
		{
			//fln_name ni e indikator 4e ima upload prez flash
			return false;
		}
		$v=$control;
		$p=pathinfo($postData["h_data"][$control_id]["fln_name"]);
		$new_ext=empty($p['extension'])?'':'.'.$p['extension'];
		if(empty($new_ext)) {
			return false;
		}
		$db=getdb();
		$new_ext=strtolower($new_ext);
		$ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
		foreach ($v['parameters']['sizes'] as $pk=>$pv) {
			if(!empty($postData["h_data"][$control_id][$pk])) {
				if($ext) {
					@unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext);
				}
				file_put_contents($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$new_ext,base64_decode($postData["h_data"][$control_id][$pk]));
			}
		}
		
		if($update_db) {
			$extra_set="";
			$data_to_write=array($new_ext);
			$fields_to_write=array(
				"{$v['parameters']['field']}=?"
			);
			if(is_array($v['parameters']['save_fields'])) {									
				$f=array(
					"file_name"=>$postData["h_data"][$control_id]["fln_name"],
//					"file_size"=>$file_size,
//					"file_type"=>$file_type,
				);
				foreach ($f as $fk=>$fv) {
					if(isset($v['parameters']['save_fields'][$fk])) {
						$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=?";
						$data_to_write[]=$fv;
					}
				}
			}
			$fields_to_write=implode(',',$fields_to_write);
			
			$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id='{$id}'",$data_to_write);
			
		}
		
		return $new_ext;	
	
	}
	
	static function deleteManagedImages($id,$controls,$update_db=true) {
		$errors=array();
		$db=getdb();
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
			if($v['tagName']!='ManagedImage') {
				continue;
			}
			
			$ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));
			if(empty($ext)) {
				continue;
				//return array();
			}			
			if(is_array($v['parameters']['sizes'])) {
				foreach ($v['parameters']['sizes'] as $pk=>$pv) {
					@$b=unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].'_'.$pk.$ext);
					if(!$b) {
						$errors[$pk]="Cannot delete <b>{$v['Label']} - {$pk}</b> image!";
					}
				}
			}
			if($update_db&&$b) {
				
				$fields_to_write=array(
					"{$v['parameters']['field']}=''"
				);
				if(is_array($v['parameters']['save_fields'])) {									
					$f=array(
						"file_name"=>1,
						"file_size"=>1,
						"file_type"=>1,
					);
					foreach ($f as $fk=>$fv) {
						if(isset($v['parameters']['save_fields'][$fk])) {
							$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=''";									
						}
					}
				}
				$fields_to_write=implode(',',$fields_to_write);
				$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id=?",array($id));
				
				
				//$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}='' where id=?",array($id));
				Control_Utils::setPostArray($v['name'],'');		
			}
		}
		
		return $errors;
	}
	
	static function deleteManagedFiles($id,$controls,$update_db=true) {
		$errors=array();
		$db=getdb();
		foreach ($controls as $k=>$v) {
			$v=$v['control'];
			if($v['tagName']!='ManagedFile'&&$v['tagName']!='ManagedVideo') {
				continue;
			}
			$ext=$db->getone("select {$v['parameters']['field']} from {$v['parameters']['table']} where id=?",array($id));			
			if(empty($ext)) {
				continue;
			}
			$ext=FE_Utils::getFileExt($ext);			
			if(is_array($v['parameters'])) {
				@$b=unlink($v['parameters']['dir'].$v['parameters']['table'].'/'.$id.'_'.$v['parameters']['field'].$ext);
				if(!$b) {
					$errors[$pk]="Cannot delete file for <b>{$v['Label']}</b>!";
				}				
			}
			if($update_db&&$b) {
				
				$fields_to_write=array(
					"{$v['parameters']['field']}=''"
				);
				if(is_array($v['parameters']['save_fields'])) {									
					$f=array(
					//	"file_name"=>1,
						"file_size"=>1,
						"file_type"=>1,
					);
					foreach ($f as $fk=>$fv) {
						if(isset($v['parameters']['save_fields'][$fk])) {
							$fields_to_write[]="`{$v['parameters']['save_fields'][$fk]}`=''";									
						}
					}
				}
				$fields_to_write=implode(',',$fields_to_write);
				$db->execute("update {$v['parameters']['table']} set {$fields_to_write} where id=?",array($id));
				
				
				
				//$db->execute("update {$v['parameters']['table']} set {$v['parameters']['field']}='' where id=?",array($id));
				Control_Utils::setPostArray($v['name'],'');				
			}
		}
		
		return $errors;
	}
	
	static function getVideoSize($src) {
		if(!isset($GLOBALS['FFMPEG_FILE'])) {
			return array(0,0);
		}
		$size=array(0,0);
		$search_for="Stream #0.0: Video:";
		exec("{$GLOBALS['FFMPEG_FILE']} -i {$src} 2>&1",$result,$val);
		if(!is_array($result)||empty($result)) {
			return $size;
		}
		foreach ($result as $k=>$v) {
			if(strpos($v,$search_for)!==false) {
				$v=explode(',',$v);
				if(!isset($v[2])||strpos($v[2],"x")<1) {
					return $size;
				}
				$v=explode('x',trim($v[2]));
				return $v;
			}
		}
		return $size;		
	}
	
	static function getVideoResizeString($width,$height,$par_array,$resize=true) {
		$w=0;
		$h=0;
		//352>200
		if($width>$par_array[0]&&$par_array[0]>0) {
			$w=$par_array[0];
		}
		//240>100
		if($height>$par_array[1]&&$par_array[1]>0) {
			$h=$par_array[1];
		}
		if($w==0&&$h==0) {
			return "";	//nqma nujda ot resize			
		}
		if($w==0) {
			$w=$width;
		}
		if($h==0) {
			$h=$height;
		}
		
		//w=352,h=240
		
		$widthScale = $w/$width;
		//ws=176
	    $heightScale = $h/$height;
	    //hs=2.4

			
	    if($widthScale < $heightScale){
	    	$dst_w = $w;
	    	$dst_h = round($height*$w/$width);
	    } else {
	    	$dst_w = round($width*$h/$height);
	    	$dst_h = $h;
	    }
	    
	    if($dst_h%2) {
	    	$dst_h++;
	    }
	    if($dst_w%2) {
	    	$dst_w++;
	    }
	    
	    
	    return " -s {$dst_w}x{$dst_h} ";
	}
	
	static function resizeImage($src,$dst,$par_array,$resize=true) 
	{
		//$par_array[0] - width //$par_array[1] - height //$par_array[2] - label //$par_array[3] - fit_out_window;
		
		if(!$resize) {
			@$b=copy($src,$dst);
			if(!$b) {
				return array("Cannot upload image!");
			}
			
			chmod($dst,0777);
			return array();
		}
		list($width, $height,$info,$attr) = getimagesize($src);
		$w=0;
		$h=0;
		if($width>$par_array[0]&&$par_array[0]>0) {
			$w=$par_array[0];
		}
		if($height>$par_array[1]&&$par_array[1]>0) {
			$h=$par_array[1];
		}
		if($w==0&&$h==0) {
			//patch za da sazdawa image sus sa6tiq extention //CPictures::createTumbnail('',$src,$dst,$width,$height);
			@$b=copy($src,$dst);
			if(!$b) {
				return array("Cannot upload image!");
			}
			
			chmod($dst,0777);
			return array();
		}
		if($w==0) {
			$w=$width;
		}
		if($h==0) {
			$h=$height;
		}
		
		$fit_out_window = (bool)$par_array[3];
		
		CPictures::createTumbnail('', $src, $dst, $w, $h, null, $fit_out_window);
		return array();
	}
	
	function getSearchArray($array,$postData,$extra_where=array(),$repeat_index=null) {
		$where=$extra_where;
		$controls=$array['controls'];
		
		foreach ($controls as $k=>$v) {
			if(!isset($v['search_data'])) {
				continue;
			}
		//	if (!isset($postData[$v['control']['name']])) {
		//		continue;
		//	}
			$sd=$v['search_data'];
			$c=$v['control'];
			
			if(!is_null($repeat_index)) {
				
				$c['name']=ControlValues::createPostName($c['name'],$repeat_index);
				
				
			}
			$ch_exclude=$sd['exclude_field'];
			$ch_strict=$sd['strict_field'];
			
			
			
			$f_value=Control_Utils::getPostArray($c['name'],$postData);
			if(isset($sd['right_field'])) {
				
				$fr_value=Control_Utils::getPostArray(ControlValues::createPostName($controls[$sd['right_field']]['control']['name'],$repeat_index) ,$postData);
			}
			else {
				$fr_value=null;
			}
			
			if(!isset($f_value)&&!isset($fr_value)) {
				continue;
			}
			if (is_string($f_value)&&!empty($c['FormatString'])) {
				$f_value=FormatUtils::decodeFormat($c['FormatString'],$f_value);
			}
			
			if (is_string($fr_value)&&!empty($controls[$sd['right_field']]['control']['FormatString'])) {
				$fr_value=FormatUtils::decodeFormat($controls[$sd['right_field']]['control']['FormatString'],$fr_value);
			}
			
			
			$cond=strtolower($sd['cond']);
			
			if($ch_strict) {
				$ch_st_val=Control_Utils::getPostArray(ControlValues::createPostName($controls[$ch_strict]['control']['name'],$repeat_index),$postData);
				
				if(!is_null($ch_st_val)&&$cond!='find_in_set'&&$cond!='in') {
					if(is_array($f_value)) {
						$f_value=array_values($f_value);
						$f_value=$f_value[0];
					}
					if(is_array($fr_value)) {
						$fr_value=array_values($fr_value);
						$fr_value=$fr_value[0];
					}
					$cond='=';	
				}
			}
			$extra_row_where=isset($sd["extra_where"])?$sd["extra_where"]:"";
			$ch_not='';
			if($ch_exclude) {
				$ch_ex_val=Control_Utils::getPostArray(ControlValues::createPostName($controls[$ch_exclude]['control']['name'],$repeat_index) ,$postData);
				if(!is_null($ch_ex_val)) {
					$ch_not='!';
				}
			}
			if(is_string($f_value)) {
				$f_value=mysql_real_escape_string($f_value);	
			}
			if(is_string($fr_value)) {
				$fr_value=mysql_real_escape_string($fr_value);
			}
			if(is_array($f_value)) {
				foreach ($f_value as &$f_value_v) {
					
					$f_value_v=mysql_real_escape_string($f_value_v);
				}
			}
			if(is_array($fr_value)) {
				foreach ($fr_value as &$fr_value_v) {
				//	var_dump($f_value_v);
				//	echo "<br />";
					$fr_value_v=mysql_real_escape_string($fr_value_v);
				}
			}
			
			if(isset($sd['search_table'])) {
				$where[$k]["search_table"]=$sd['search_table'];
			}
			
			switch ($cond) {
				case 'keywords': {
					if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
						$fields=explode(",",$sd['search_name']);
						$str=CSearch::getKeyWords($f_value,$fields);
						if(!empty($str)) {
							$where[$k]['str']=$str;
						}
					}
					break;
				}
				case 'select': {
					if(!empty($sd['search_phrase'])) {
						if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
						$search_str=str_replace(
							array("_#VAL#_"),
							array($f_value),
							$sd['search_phrase']
						);
						$where[$k]['str']="{$ch_not}({$sd['search_name']} {$search_str})";
						}
					}
					break;
				}
				case 'between': {
					if(empty($fr_value)) {
						if(!empty($f_value)) {
							$cnd=is_null($ch_st_val)?">=":'=';
							$where[$k]['left'][]=$f_value;
							$where[$k]['cond'][]=$cnd;
							$where[$k]['strinct']=false;
							$where[$k]['not']=$ch_not;
							$where[$k]['str']="{$ch_not}({$sd['search_name']} {$cnd} '{$f_value}')";
						}
					}
					else {
						if(empty($f_value)) {
							if(!empty($fr_value)) {
								$cnd=is_null($ch_st_val)?"<=":'=';
								$where[$k]['left'][]=$fr_value;
								$where[$k]['cond'][]=$cnd;
								$where[$k]['strinct']=false;
								$where[$k]['not']=$ch_not;
								$where[$k]['str']="{$ch_not}({$sd['search_name']} {$cnd} '{$fr_value}')";
							}
						}
						else {
							$where[$k]['left'][]=$f_value;
							$where[$k]['cond'][]='between';
							$where[$k]['right'][]=$fr_value;
							$where[$k]['strinct']=false;
							$where[$k]['not']=$ch_not;
							$where[$k]['str']="{$ch_not}({$sd['search_name']} between '{$f_value}' and '{$fr_value}')";
						}
					}
					break;
				}
				case 'find_in_set': {
					if(is_array($f_value)) {
						$tmp_arr=array();
						if(!(isset($sd['matchAllValue'])&&implode(',',$f_value)==$sd['matchAllValue'])) {
							foreach ($f_value as $fv) {
								$where[$k]['left'][]=$fv;
								$where[$k]['cond'][]=$ch_not.' find_in_set';
								$tmp_arr[]="(find_in_set('{$fv}',{$sd['search_name']}))";
							}
							if(!is_null($ch_st_val)) {
								$where[$k]['not']=$ch_not;
								$where[$k]['strinct']=true;
								$where[$k]['str']=implode(' AND ',$tmp_arr);
							}
							else {
								$where[$k]['not']=$ch_not;
								$where[$k]['strinct']=false;
								if(!empty($extra_row_where)) {
									$where[$k]['str']=$extra_row_where." ".$ch_not."(".implode(' OR ',$tmp_arr).")";
								}
								else {
									$where[$k]['str']=$ch_not."(".implode(' OR ',$tmp_arr).")";
								}
							}
						}
					}
					else {
						if((string)$f_value!='') {
							if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
								$where[$k]['not']=$ch_not;
								$where[$k]['left'][]=$f_value;
								$where[$k]['cond'][]='find_in_set';
								$where[$k]['strinct']=!is_null($ch_st_val);
								$where[$k]['str']="{$ch_not}(find_in_set('{$f_value}',{$sd['search_name']}))";
							}
						}
					}
					break;
				}
				case 'in': {
					$where[$k]['not']=$ch_not;		
					if(is_array($f_value)) {
						if(empty($f_value))
							break;
						$tmp_arr=array();
						if(!(isset($sd['matchAllValue'])&&implode(',',$f_value)==$sd['matchAllValue'])) {
							if(!is_null($ch_st_val)) {
								foreach ($f_value as $fv) {
									$where[$k]['left'][]=$fv;
									$where[$k]['cond'][]='=';
									$tmp_arr[]="{$ch_not}({$sd['search_name']}='{$fv}')";
								}
							}
							else {
								$f_t="'".implode("','",$f_value)."'";
								$tmp_arr[]="{$ch_not}({$sd['search_name']} in ({$f_t}))";
							}
							if(!is_null($ch_st_val)) {
								$where[$k]['strinct']=true;
								$where[$k]['str']=implode(' AND ',$tmp_arr);
							}
							else {
								$where[$k]['strinct']=false;
								$where[$k]['str']=implode(' OR ',$tmp_arr);
							}
						}
					}
					else {
						if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
							$where[$k]['left'][]=$f_value;
							$where[$k]['cond'][]='in';
							$where[$k]['strinct']=!is_null($ch_st_val);
							$where[$k]['str']="{$ch_not}(find_in_set('{$f_value}',{$sd['search_name']}))";
						}
					}
					break;
				}
				case 'like': {
					if(!empty($f_value)) {
						$where[$k]['not']=$ch_not;
						$where[$k]['left'][]=$f_value;
						$where[$k]['cond'][]=$cond;
						$where[$k]['strinct']=!is_null($ch_st_val);
						$where[$k]['str']="{$ch_not}({$sd['search_name']} {$cond} '%{$f_value}%')";
					}
					break;
				}
				case '=': {
					
					$where[$k]['not']=$ch_not;	
					if(!empty($fr_value)) {
						if(!empty($f_value)) {
							if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
								$cnd=is_null($ch_st_val)?">=":'=';
								$where[$k]['left'][]=$f_value;
								$where[$k]['right'][]=$fr_value;
								$where[$k]['cond'][]=$cnd;
								$where[$k]['strinct']=!is_null($ch_st_val);
								$where[$k]['str']="{$ch_not}({$sd['search_name']}{$cnd}'{$f_value}' and {$sd['search_name']}<='{$fr_value}')";
							}
						}
						else {
							$cnd=is_null($ch_st_val)?"<=":'=';
							$where[$k]['left'][]=$fr_value;
							$where[$k]['cond'][]=$cnd;
							$where[$k]['strinct']=!is_null($ch_st_val);
							$where[$k]['str']="{$ch_not}({$sd['search_name']}{$cnd}'{$fr_value}')";
						}
					}
					else {
						if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
							$where[$k]['left'][]=$f_value;
							$where[$k]['strinct']=!is_null($ch_st_val);
							if(!is_null($ch_st_val)||!isset($sd['strict_field'])) {
								$where[$k]['cond'][]='=';
								$where[$k]['str']="{$ch_not}({$sd['search_name']}='{$f_value}')";
							}
							else {
								$where[$k]['cond'][]='>=';
								$where[$k]['str']="{$ch_not}({$sd['search_name']}>='{$f_value}')";
							}
						}
					}
					break;
				}
				default: {
					if(!empty($f_value)) {
						if(!(isset($sd['matchAllValue'])&&$f_value==$sd['matchAllValue'])) {
							$where[$k]['not']=$ch_not;
							$where[$k]['left'][]=$f_value;
							$where[$k]['cond'][]=$cond;
							$where[$k]['strinct']=!is_null($ch_st_val);
							$where[$k]['str']="{$ch_not}({$sd['search_name']} {$cond} '{$f_value}')";
						}
					}
					break;
				}
			}
		}
		return $where;
	}
	
	function getSearchString($array,$postData,$extra_where=array(),$repeat_index=null) {
		$where=self::getSearchArray($array,$postData,$extra_where,$repeat_index);
		$str.='';
		$str_and='';
		$fl=false;
		$array=array();
		
		if(!empty($where)) {
			foreach ($where as $k=>$v) {
				if(!empty($v['str'])) {
					$array[]=$v['str'];
				}
			//	$str.=$str_and.$v['str'];
			//	$str_and.=") AND (";
			}
			//$str="(".$str.")";
		}
		if (empty($array)) {
			return '';
		}
//		echo "(".implode(") AND (",$array).")";
		return "(".implode(") AND (",$array).")";
		//return $str;
	}
}

class DBValidator {
	
    public function isValid ($val,$signed,$type) {
    	$intervals = array(
	      'tiny'     => '256',
	      'small'    => '65536',
	      'medium'   => '16777216',
	      'default'  => '4294967296',
	      'big'      => '18446744073709551616',
    	);
    	$interval=$intervals[$type];
    if (extension_loaded('bc_math')) {
      if (!$signed) {
        $min = '0';
        $max = bcsub($interval, '1');
      } else {
        $min = '-'.bcdiv($interval, '2');
        $max = bcsub(bcdiv($interval, '2'), '1');
      }
      
      if (bccomp($min, $val)>0 or bccomp($max, $val)<0)
        throw new InvalidInputValueException($val, "range $min : $max");
    }
    else {
	      if ($type=='big')
	        trigger_error('Extension "bc_math" not loaded, this extension is required for using 64-bit integers', E_USER_ERROR);
	      if (!$signed) {
	        $min = 0;
	        $max = $interval-1;
	      } else {
	        $min = 0-$interval/2;
	        $max = $interval/2 - 1;
	      }
	      
	      if ($min>$val or $max<$val)
	        return $val." must be in the range {$min} : {$max}";
	     
	    }
	    $t_val=(string)((int)$val);
	    return $t_val==$val?true:"Invalid value for ";

    }
    
       
    function isValidTinyint($int,$signed=false) {
		return self::isValid($int,$signed,'tiny');	
	}
	
	function isValidSmallint($int,$signed=false) {
		return self::isValid($int,$signed,'small');	
	}
	
	function isValidMediumint($int,$signed=false) {
		return self::isValid($int,$signed,'medium');	
	}
    
	function isValidInt($int,$signed=false) {
		return self::isValid($int,$signed,'default');	
	}
	
	function isValidBigInt($int,$signed=false) {
		return self::isValid($int,$signed,'big');	
	}
	
	function isValidFloat($val,$signed) {
		if (is_string($val)) {
      		if (!preg_match('#^-?([0-9]+(\.[0-9]+)?|\.[0-9]+)([eE][-+]?[0-9]+)?$#', $val)) 
      			return "Invalid Float value `{$val}`!";
      		$val = floatval($val);
    	}
        if (!is_double($val) && !is_integer($val)) 
        	return "Invalid Float value `{$val}`!";
	    $val = sprintf('%f', $val);
	    if (!$signed and $val<0) 
	    	"Invalid Unsigned Float value `{$val}`!";
	    return true;
	}
	
	function isValidString($string,$size) {
		if($size&&strlen($string)>(int)$size)
			return "Invalid String Size!";
		return true;
	}
	
	
}


class Master {
	
static function generateTemplate($array,$colcount=0,$TITLE='TITLE',$type='edit',$label_width='10%') {
		if(empty($array)) {
			return '';
		}
		
		if(empty($colcount))
		$colcount=1;
	$controls=$array['controls'];
	$c_span=$colcount*2;
	
	$cols=array();
	$cw=ceil(100/$c_span);
	for($i=0;$i<$colcount;$i++) {
		if($i==$colcount-1) {
			$cols[]="<col width='{$cw}%' align='right'>";
			$cols[]="<col width='{$cw}%*' align='left'>";
		}
		else {
			$cols[]="<col width='{$cw}%' align='right'>";
			$cols[]="<col width='{$cw}%' align='left'>";
		}
	//	$cols[]="<col width='*' align='right'>";
	//	$cols[]="<col width='0*' align='left'>";
	}
	$cols=implode("\n",$cols);
	
	$str=<<<EOD
<table cellpadding="5" cellspacing="0" class="test1" width="700" align="center" border='0'>
<colgroup span="{$c_span}" width="0*">
{$cols}
</colgroup>
<tbody>
	
EOD;
	if(!empty($TITLE)) {
		$str.=<<<EOD
		<tr><td  class="viewLabel" colspan="{$c_span}">{$TITLE}</td></tr>
EOD;
	}
	$arr=array();
	foreach ($controls as $k=>$v) {
		$v=$v['control'];
		if(empty($v)) {
			$arr[]="";
			$arr[]="";
			continue;
		}
		if($v['tagName']=='Select') {
			$focus="onclick=\"document.getElementById('{$v['bound_field']}').focus();\"";
		}
		else {
			$focus="for=\"{$v['bound_field']}\"";
		}
		$label="<label {$focus}>{$v['Label']}</label>";
		if($v['attributes']['type']=='checkbox'||$v['tagName']=='CheckBox') {
			$c="<ITTI field_name='{$k}'></ITTI> {$label}";	
			$label='';
		}
		else {
			$c="<ITTI field_name='{$k}'></ITTI>";
		}
		
		$arr[]=$label;
		$arr[]=$c;
	//	$str.=<<<EOD
	
	//<tr>
	//	<td>{$label}</td>
	//	<td>{$c}</td>
	//</tr>
//EOD;
	}
	$str.=FE_Utils::createTableCells($arr,$colcount*2,"\n<td>");
	if(isset($array['buttons'])) {
		$buttons=$array['buttons'];
	}
	else {
		switch ($type) {
			case 'search': {
				$buttons=<<<EOD
				<input type="submit" name="search" value="Търси" />&nbsp;&nbsp;&nbsp;<input type="submit" name='btClear' value="�?зчисти" />
EOD;
				break;
			}
			case 'edit': {
				$buttons=<<<EOD
				<input type="submit" name="btSave" value="Запис" />&nbsp;&nbsp;&nbsp;<input type="button" onclick="self.location='<?=(\$_GET['bkp']);?>'" value="Назад" />
EOD;
				break;
			}
		}
	}
		$str.=<<<EOD
		
	<tr>
		<td colspan="{$c_span}" align="center" style="padding-right:10px;">
			{$buttons}
	</td></tr>
</tbody></table>
EOD;
	return '<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">'.$str;
	}
	
	function importHTML($dom,$html,$parentNode=null,$node=null) {
		$doc = new DOMDocument();
		$html='<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"></head>'.$html;
		//tova e za da opravi kirilicata
		@$doc->loadHTML($html);
		$xp=new DOMXPath($doc);
		$b=$xp->Query('//body');
				
		if($b->item(0)) {
			$ch=$b->item(0)->childNodes;
			foreach ($ch as $nl) {
				$n=$dom->importNode($nl,true);
				//if(empty($parentNode)) {
				if(empty($node)) {
					$dom->appendChild($n);
				}
				else {
					//$parentNode->appendChild($n);
				//	var_dump($parentNode->tagName);
				//	echo "<br />";
					try {
					$parentNode->insertBefore($n,$node);
					}catch(Exception $e) {
						$node->appendChild($n);
					}
				}
			}
		}
	}
	
	function append_attributes(&$control,$node) {
		if($node->attributes) {
			foreach ($node->attributes as $k=>$v) {
				@$name=(string)($v->name);
				if($name=="field_name") {
					continue;
				}
				$control->attributes[$name]=(string)$v->nodeValue;
			}
		}
	}
	
	public static function getControlReadValues($controls,$postData=array()) {
		$data=array();
		foreach ($controls['controls'] as $name=>$v) {
			$tmp=$v['control'];
			$c=new $tmp['tagName']($tmp,$name);
			$c->setValue(Control_Utils::getPostArray($c->postName,$postData));
			$data[$name]=$c->getHTML(true);
		}
		
		return $data;
		
	}
	
	function create($controls,$template,$postData=array(),$repeat_index=null,$read_only=false,$bodyTag='<body>',$is_file=true) {
		//mb_internal_encoding("utf-8");
		if($is_file) {
			ob_start();
	        include ($template);
	        $str_template = ob_get_clean();
		}
		else {
			$str_template=$template;
		}
        //$dom = new DOMDocument();
        $dom = new DOMDocument('1.0',"utf-8");
		// we want a nice output
		$dom->formatOutput = true;
		
		
        @$dom->loadHTML($str_template);
        
        //$dom->standalone = true;
		$dom->substituteEntities=true;       
		$dom->encoding = 'utf-8'; 
		$itti=$dom->getElementsByTagName("itti");
		$items=array();
		foreach($itti as $k=>$v) {
			$name=$v->getAttribute('field_name');
			$tmp=$controls['controls'][$name]['control'];
			if(!is_null($repeat_index)) {
				$tmp['repeat_index']=$repeat_index;
			}
			if(DEBUG_MODE&&!class_exists($tmp['tagName'])) {
				var_dump($tmp['tagName']);
				echo "<br />";
				var_dump($name);
				echo "<br />";
				echo "<pre>";
				print_r($controls[$name]);
				echo "</pre>";
				echo "<pre>";
				print_r($controls);
				echo "</pre>";
			}
			$c=new $tmp['tagName']($tmp,$name);
			/* @var $c CommonControl */
			$required=isset($controls['controls'][$name]['write_data'])&&isset($controls['controls'][$name]['write_data']['required'])&&$controls['controls'][$name]['write_data']['required'];
			if($required) {
				$c->is_required=true;
			}
			if(isset($controls['js_validation'])&&$controls['js_validation']) {				
				if(isset($controls['controls'][$name]['js_validation'])&&!empty($controls['controls'][$name]['js_validation'])) {
					$c->validation_data=$controls['controls'][$name]['js_validation'];					
					if($required) {
						if(!isset($c->validation_data['required'])) {
							$c->validation_data['required']="true";
						}						
					}
					if(!isset($c->validation_data['required_type'])) {
						$c->validation_data['required_type']="{$controls['controls'][$name]['write_data']['type']}";
					}
				}
			}
			
			$c->setValue(Control_Utils::getPostArray($c->postName,$postData));
			$items[]=$v;
			
			Master::append_attributes($c,$v);
			
   			Master::importHTML($dom,$c->getHTML($read_only),$v->parentNode,$v);
		}  
		
		foreach($items as $v) {
				$v->parentNode->removeChild($v);
		}
		
		
		/*$body=$dom->getElementsByTagName("body");
		if($body->item(0)) {
			$str='';
			for($i=0;$i<$body->item(0)->childNodes->length;$i++) {
				$str.=$dom->saveXML($body->item(0)->childNodes->item($i));
			}
			

			//return $str;
			//return $dom->saveXML($body->item(0));
			$str=str_replace(array("<sscript>","</sscript>"),array("<script>","</script>"),$str);
			return $str;
			return html_entity_decode($str,ENT_NOQUOTES,"utf-8");
		}*/
		$s=$dom->saveHTML();
		;
		
		$pos=stripos($s,$bodyTag);
		if($pos!==false) {
 			$pos+=strlen($bodyTag);
		}
		else {
			$pos=201;
		}
		$end_body_tag=str_replace("<","</",$bodyTag);
		
		$pos1=strrpos($s,$end_body_tag);
		
		
		if($pos1==false) {
			$pos1=$pos+16;
			$len=strlen($s)-$pos1;
		}
		else {
			$len=$pos1-$pos;
		} 
		//return substr($s,$pos,$len);
		return html_entity_decode(substr($s,$pos,$len),ENT_NOQUOTES,"UTF-8");
//		return html_entity_decode(substr($s,201,strlen($s)-225),ENT_NOQUOTES,"utf-8");
        
	}
}

class CPageBar  {	
	private $page_count;
	private $pageSize;
//	public  $leftMessage;
	private $current_page;
	public $get_name;
	public $postback_function;
	public $custom_postback="";
	public $totalItems;
	public $show_page_count=true;
	public $show_total_items=true;
	public $show_prev_next=true;
	public $show_goto=true;
	private $events;
	public $counter_size=20;
	public $old_page;
	private $prerendered=false;
	public $href;
	
	public $control_id;
	
	public $request_method=POST_METHOD;
	
	
	function __construct($control_id,$totalItems,$page_size=20,$counter_size=20,$get_name='p',$request_method='POST') {
		//parent::__construct($control_id);
	
		$this->control_id=$control_id;
		
		$this->totalItems=$totalItems;
		$this->page_count=0;
		$this->current_page=0;
		$this->request_method=$request_method;
		$this->pageSize=$this->setPageSize($page_size);
		$this->get_name=$get_name;
		$this->setPage();
		$this->counter_size=$counter_size;
		$this->initFunctions();
		$this->events=array();	
	}
	
	function getPageSize() {
		return $this->pageSize;
	}
	
	function setPageSize($size) {
		if($size) {
			$this->pageSize=$size;
			$this->setPageCount();
		}
		return $this->pageSize;
	}
	
	protected function setPageCount() {
		if($this->pageSize)
			$this->page_count=(int)ceil($this->totalItems/$this->pageSize);
	}
	
	function alterPageCount() {
		$this->setPageCount();
	}
	
	function getPageCount() {
		return $this->page_count;
	}
	
	function addEvent($function_name,$event_name=EVENT_PAGE_CHANGE) {
		if(!defined($event_name))
			throw new Exception("Control has no event for {$event_name}");
		$this->events[$event_name][]=$function_name;
	}
	
	function getCurrentPage() {
		return (int)$this->current_page;
	}
	
	function setCurrentPage($page,$riseEvent=true) {
		if($page>=$this->page_count||$page<0) {
			return false;
		}
		if($riseEvent&&$this->current_page!=$page) {
			$this->current_page=$page;
			$this->OnEvent($page,EVENT_PAGE_CHANGE);
		}
	}
	
	protected function initFunctions() {
		$this->postback_function="_c_POSTBACK_{$this->control_id}";
	}
	
	
	protected function setPage() {
		switch ($this->request_method) {
			case POST_METHOD: {
				$this->setCurrentPage(intval($_POST['_c_page'.$this->control_id]));
				$this->old_page=intval($_POST['_c_page_old'.$this->control_id]);
				break;
			}
			case GET_METHOD: {
				$this->setCurrentPage(intval($_GET[$this->get_name]));
				break;	
			}
		}
		//echo $this->current_page;
	}
	
	protected function getURL($page,$text,$alt='') {
		if(empty($alt)) {
			$alt=$text;
		}
		switch ($this->request_method) {
			case POST_METHOD: {
				return "<a href='#' title='{$alt}' onclick=\"{$this->postback_function}(this, '".$page."')\">{$text}</a>";				
			}
			case GET_METHOD: {
				$arr=$_GET;
				$_GET[$this->get_name]=$page;
				$str=Control_Utils::getGetString($arr,'&','');
				if(empty($this->href)) {
					$str=Control_Utils::getGetString($arr,'&','');
					$href=empty($str)?$_SERVER['PHP_SELF'].'?'.$str:$_SERVER['PHP_SELF'];
				}
				else {
					$href=$this->href.$this->get_name.'='.$page;
				}
				return "<a title='{$alt}' href='{$href}'\">{$text}</a>";				
			}
		}
	}
	
	protected function renderPageLinks() {
		$pages = $this->page_count;
		$page = $this->current_page;
		$HTML="";
		
		$maxpages = $this->counter_size;
		
		$dl = ceil(($maxpages-1)/2);
		$dr = floor(($maxpages-1)/2);
		
		if ($maxpages>=$pages) {
			$fp = 0;
			$ep = $pages;
		} else {
			$fp = $page - $dl;
			$ep = $page + $dr;
			if ($fp<0) {
				$fp = 0;
				$ep = $maxpages;
			}
			if ($ep>$pages) {
				$ep = $pages;
				$fp = $pages - $maxpages;
			}
		}
		if ($page>0&&$this->show_prev_next) {
			$HTML .= "{$this->getURL($page-1,'&laquo;','Prev')} ";		
		}
		for ($i=$fp; $i<$ep; $i++) {
			if ($i==$page) {
				$HTML .= "<b>".($i+1)."</b> ";
			}			
			else {
				$HTML .=$this->getURL($i,$i+1).' ';
			}
		}
		if($this->show_prev_next&&$page<($pages-1)) {
			$HTML .= " {$this->getURL($page+1,'&raquo;','Next')}";
		}
		return $HTML;
  }
  
  protected function renderPageBar() {
  	
  	$str=<<<EOD
<table id="{$this->control_id}" class="page_bar">
<tr><td width="1%" nowrap="true" align="left">
EOD;

  	//Page 1 of 1<br/>14 Records</td><td align="center" nowrap="true"><b>1</b> </td><td width="1%" align="right" nowrap="true">Go to<input type="text" value="1" size="3" name="input_pb_page" onkeydown="if (event.keyCode==13 || event.keyCode==10) { _c_POSTBACK_pb(this, this.value-1); return false; }"/></td></tr></table>
  	
    
    if($this->show_page_count) {
   		$str.="Page ".($this->current_page+1)." of ".($this->page_count);
    }
    if($this->show_total_items&&$this->totalItems) {
    	if($this->show_page_count) {
    		$str.="<br />";
    	}
    	$str.="{$this->totalItems} Records";
    }
    $str.="</td>";
    
    $str.='<td align="center" nowrap="true">';
    $str.=$this->renderPageLinks($result);
    $str.='</td>';
    
    
    if($this->show_goto) {
    	$p=$this->current_page+1;
    	$goto=<<<EOD
    	Go to <input size="3" type="text" name="input_{$this->control_id}_page" value="{$p}" onkeydown="if (event.keyCode==13 || event.keyCode==10) { {$this->postback_function}(this, this.value-1); return false; }" />
EOD;
    }
    else {
    	$goto=null;
    }

   
    
    if($goto) {
    	$str.="<td style=\"width:1%\" nowrap align=\"right\">{$goto}</td>";    	
    }
    $str.="</tr></table>";
    return $str;
  }
  
  	public function renderScript() {
  		if(defined($this->control_id.'script'))
  			return null;
  		define($this->control_id.'script',1);
  		$str_event=EVENT_PAGE_CHANGE;
  		$ev=CScriptRenderer::_scr_findForm();
  		$fl=CScriptRenderer::_set_Fields();
  		$es=CScriptRenderer::_set_EventSource($this->control_id);
  		
  		$str=<<<EOD
  		<input type="hidden" name="_c_page{$this->control_id}" id="_c_page{$this->control_id}" value="{$this->current_page}" />
  		<input type="hidden" name="_c_page_event{$this->control_id}" id="_c_page_event{$this->control_id}" value="" />
  		<input type="hidden" name="_c_page_old{$this->control_id}" id="_c_page_old{$this->control_id}" value="$this->current_page" />
<script>
EOD;
  		
  		if($ev) {	$str.=$ev; }  		
  		if($fl) {	$str.=$fl;} 		
  		if($es) {	$str.=$es;}  		
  		if($this->custom_postback!="") {
  			$fld="return {$this->custom_postback}(sender,eventArgument);";
  		}
  		else {
  			$fld="form_obj.submit();";
  		}
  		$str.= <<<EOD

			{$scr_event}
			  function {$this->postback_function}(sender, eventArgument) {
			    _set_EventSource();
			    _set_Fields(sender,new Array('_c_page{$this->control_id}','_c_page_event{$this->control_id}'),new Array(eventArgument,'{$str_event}'));
			    {$fld}
			    return 0;
			  }
</script>
EOD;
		return $str;
  } 
  
  function PreRender() {
  	if(!$this->prerendered) {
	  	$this->renderPageBar();
	  	$this->renderScript();
	  	$this->prerendered=true;
  	}
  }
  
  function render() {
  	//$this->PreRender();
  	//if(!$this->parentPage) {
  	////	$this->PreRender();
  	//}
  	return $this->renderPageBar().$this->renderScript();
  		
  }
  
  function renderEvents() {
  	if(isPostback) {
	  	if($_POST['hd_event_element']=$this->control_id) {
	  		$f_args=func_get_args();
	  		$this->OnEvent($_POST['_c_page'.$this->control_id],$_POST['_c_page_event'.$this->control_id],$f_args);
	  	}
  	}
  }
  
  public function OnEvent($event_arg,$event_name) {
  	if($this->old_page==$event_arg)
  		return;
  	$arr=func_get_args();
  	$arr[0]=$this;
  	//code for this control post_back event
  	
  	if(is_array($this->events[$event_name]))
  	foreach ($this->events[$event_name] as $func) {
  		call_user_func($func,$event_arg,$arr);
  	}
  	
  }
	
}

class CScriptRenderer {
	function _scr_findForm() {
		if(defined('_SCR_FINDFORM'))
			return null;
		define('_SCR_FINDFORM',1);
		$str= <<<EOD
		function _scr_findForm(elem) {
				 form_obj = elem;
				 while (form_obj.tagName!='FORM') {
				    form_obj = form_obj.parentNode;
				    if (!form_obj) {
				      alert('Form not found! Please put the list control in a form!'); return 0;
				   }
				 }
				  return form_obj;
			}
EOD;
		return $str;
	}	
	
	function _set_Fields() {
		if(defined('_SET_FIELDS'))
			return null;
		define('_SET_FIELDS',1);
		$str= <<<EOD
			function _set_Fields(obj,fields,values) {
				if(fields.length&&fields.length!='undefined') {
					var form_obj=_scr_findForm(obj);
					try {
						var i;
						for(i=0;i<fields.length;i++) {
							if(form_obj.elements[fields[i]].length&&form_obj.elements[fields[i]].length!='undefined') {
								var g;
								for(g=0;g<form_obj.elements[fields[i]].length;g++)
									form_obj.elements[fields[i]][g].value = values[i];
							}
							else {
								form_obj.elements[fields[i]].value = values[i];
							}
						}
					}
					catch(e) {alert(e.description);}
					return form_obj;
				}
			}
EOD;
		return $str;
	}
	
	function _set_EventSource($control_id) {
		if(defined('_SET_EVENTSOURCE'))
			return null;
		define('_SET_EVENTSOURCE',1);
		$str= <<<EOD
			  function _set_EventSource() {
			    var hd=document.getElementById('hd_event_element');
			    try {
			    	if(hd&&hd!='undefined')
			    		hd.value='{$control_id}';
			    }catch(e){}
			  }
EOD;
		return $str; 
	}
}

?>