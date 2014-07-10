<?php

define("xls_replace_currency","_#CURRENCY_SIGN#_");
define("xls_replace_color","_#COLOR#_");
define("xls_replace_charset","_#CHARSET#_");
define("xls_replace_fontname","_#FONTNAME#_");
define("xls_replace_fontfamily","_#FONTFAMILY#_");
define("xls_replace_fontsize","_#FONTSIZE#_");
define("xls_replace_bold","_#BOLD#_");
define("xls_replace_strikethrough","_#STRIKETHROUGH#_");
define("xls_replace_fontalign","_#FONTALIGN#_");
define("xls_replace_aligment","_#ALIGMENT#_");
define("xls_replace_wrap","_#WRAP#_");
define("xls_replace_rotate","_#ROTATE#_");
define("xls_replace_pattern","_#PATTERN#_");

define("DEFAULT_CHARSET",204);
define("DEFAULT_CURRENCY_SIGN","лв.");
define("FONT_SUBSCRIPT","Subscript");
define("PATTERN_SOLID","Solid");

define("ALIGN_TOP","Top");
define("ALIGN_BOTTOM","Bottom");
define("ALIGN_CENTER","Center");
define("ALIGN_JUSTIFY","Justify");

define("DATATYPE_STRING","String");
define("DATATYPE_DATETIME","DateTime");
define("DATATYPE_NUMBER","Number");



define("FORMAT_SHORT_DATE",0);
define("FORMAT_DATETIME",1);
define("FORMAT_CURRENCY",2);
define("FORMAT_CHARSET",3);
define("FORMAT_COLOR",4);
define("FORMAT_FONTNAME",5);
define("FORMAT_FONTFAMILY",6);
define("FORMAT_FONTSIZE",7);
define("FORMAT_BOLD",8);
define("FORMAT_STRIKETHROUGH",9);
define("FORMAT_FONTALIGN",10);



define("FORMAT_ALIGNVERTICAL",11);
define("FORMAT_ALIGNHORIZONTAL",12);
define("FORMAT_WARPTEXT",13);
define("FORMAT_ROTATE",14);

define("FORMAT_BACKGROUNDCOLOR",15);

class CExcelXml {
	
	public $_xml_root='
<?xml version="1.0"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
';
	
	public $_styles=array(
		FORMAT_SHORT_DATE=>array('tag'=>'NumberFormat','val','ss:Format="Short Date"'),
		FORMAT_DATETIME=>array('tag'=>'NumberFormat','val'=>'ss:Format="General Date"'),
		FORMAT_CURRENCY=>array('tag'=>'NumberFormat','val'=>'ss:Format="_-* #,##0.00\ &quot;_#CURRENCY_SIGN#_&quot;_-;\-* #,##0.00\ &quot;_#CURRENCY_SIGN#_&quot;_-;_-* &quot;-&quot;??\ &quot;_#CURRENCY_SIGN#_&quot;_-;_-@_-"'),
		
		FORMAT_CHARSET=>array('tag'=>"Font",'val'=>'x:CharSet="_#CHARSET#_"'),
		FORMAT_COLOR=>array('tag'=>"Font",'val'=>'ss:Color="_#COLOR#_"'),
		FORMAT_FONTNAME=>array('tag'=>"Font",'val'=>'ss:FontName="_#FONTNAME#_"'),
		FORMAT_FONTFAMILY=>array('tag'=>"Font",'val'=>'ss:FontName="_#FONTFAMILY#_"'),
		FORMAT_FONTSIZE=>array('tag'=>"Font",'val'=>'ss:FontName="_#FONTSIZE#_"'),
		FORMAT_BOLD=>array('tag'=>"Font",'val'=>'ss:Bold="_#BOLD#_"'),
		FORMAT_STRIKETHROUGH=>array('tag'=>"Font",'val'=>'ss:StrikeThrough="_#STRIKETHROUGH#_"'),
		FORMAT_FONTALIGN=>array('tag'=>"Font",'val'=>'ss:VerticalAlign="_#FONTALIGN#_"'),
		
		FORMAT_ALIGNVERTICAL=>array('tag'=>"Alignment",'val'=>'ss:Vertical="_#ALIGMENT#_"'),
		FORMAT_ALIGNHORIZONTAL=>array('tag'=>"Alignment",'val'=>'ss:Horizontal="_#ALIGMENT#_"'),
		FORMAT_WARPTEXT=>array('tag'=>"Alignment",'val'=>'ss:WrapText="_#WRAP#_"'),
		FORMAT_ROTATE=>array('tag'=>"Alignment",'val'=>'ss:Rotate="_#ROTATE#_"'),
		
		
		FORMAT_BACKGROUNDCOLOR=>array('tag'=>"Interior",'val'=>'ss:Color="_#COLOR#_"'),
		FORMAT_BACKGROUNDPATTERN=>array('tag'=>"Interior",'val'=>'ss:Pattern="_#PATTERN#_"'),
		
		
	);
	
	public $_default_style='<Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font x:CharSet="204"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>';
	
	public $rows=array();
	public $cellstyles=array();
	public $data_types=array();
	public $merges=array();
	
	function addStyle($tag,$value) {
		$this->_styles[]=array('tag'=>$tag,'val'=>$value);
		return count($this->_styles)-1;
	}
	
	function addCellStyle($row,$col,$styles=array()) {
		$this->cellstyles[$row][$col]=$styles;
	}
	
	function setType($row,$col,$type) {
		$this->data_types[$row][$col]=$type;
	}
	
	function mergeCells($row,$col,$count,$direction="H") {
		$this->merges[$row][$col]=array('c'=>(int)$count,'d'=>$direction);
	}
	
	function addRow($cells,$row_index=null) {
		if(is_null($row_index)) {
			$this->rows[]=$cells;
			$k=array_keys($this->rows);
			return end($k);
		}
		$this->rows[$row_index]=$cells;
		return $row_index;
	}
	
	function addCell($row_index,$cellData,$cell_index=null) {
		if(is_null($cell_index)) {
			$this->rows[$row_index][]=$cellData;
			$k=array_keys($this->rows[$row_index]);
			return end($k);
		}
		$this->rows[$row_index][$cell_index]=$cellData;
		return $cell_index;
	}	
	
}

class CExcelTolkenizer {
	
	public static function getTokens() {
		$token_M=array(
			"H"=>array(
				//"#"=>'#',	//number
			),
			"V"=>array(
				//"#"=>'#',	//number
			),
			
		);
		
		$tokens=array(
			"S"=>array(	//style				
				//'$'=>'$',	//string
			),
			'H'=>array(	//header
				"M"=>$token_M,
			),
			'M'=>$token_M,	//merge
		);
		
		return $tokens;
	}
	
	public static function tolk_command($string,$command,$tokens,&$result) {
		
	}
	
	public static function tolkenize($string,$tokens,$old_command='') {
		
		$result=array();
		$i=0;
		while ($i<strlen($string)) {
			$command=$string[$i];
			$i++;
			if(is_array($tokens)&&isset($tokens[$command])) {				
				$r=self::tolkenize(substr($string,$i),$tokens[$command],$old_command);
				$result[$command]=$r['r'];
				$i+=$r['i'];				
			}
			else {
				switch ($command) {	//terminali
					case '#': {	//number
						$num=0;
						while ($i<strlen($string)) {
							$ch=$string[$i];
							if($ch>='0'&&$ch<='9') {
								$i++;
								$ch=(int)$ch;
								$num=$num*10+$ch;
							}
							else {
								break;
							}
						}
						$result=$num;
						break;
					}
					case '$': {	//string, za krai e _ ili $
						$str="";
						while($i<strlen($string)) {
							$ch=$string[$i];
							if($ch=='-'||$ch=='$') {
								break;
							}
							$i++;
							$str.=$ch;
						}
						$result=$str;
						break;
					}
					case '_' : {	//next
						$i++;
						return array('i'=>$i,'r'=>$result);
						break;
					}
					default: {	//nqkva gre6ka mai
						return array('i'=>$i,'r'=>$result);
						break;
					}
				}
			}
		}
		return array('i'=>$i,'r'=>$result);
	}
	
	public static function parse($string) {
		$items=explode("_",$string);
		$a=array();
		foreach ($items as $k=>$v) {
			if(!empty($v)) {
				$a[]=self::tolkenize($v,self::getTokens());
			}
		}
		
		$b=array();
		foreach ($a as $k=>$v) {
			foreach ($v['r'] as $vk=>$vr) {
				if(!isset($b[$vk])) {
					$b[$vk]=$vr;
				}
				else {
					$b[$vk]=array($b[$vk],$vr);
				}
			}
		}
		
		return $b;
	}
}

class CExcelUtils {
	
	
	
	/**
	 * primerni danni za $data:
	 * $data=array(
	 * 	0=>array(
	 * 		'n$id'=1,	//ste go zapi6e kato number, header=Id
	 * 		'name'='zapryan'	//type=string, header=Name
	 * 		'dt$created_date'	//type=Short Date, header=Created Date
	 * 		'dtt$created_date'	//type=General time, header=Created Date
	 * 		'SHMV(H)10_SHs10_Ss11$description'	//type=string(default), kletkata e Merge-nata vertikalno,
	 * 		//za headera e zadaden style s ID=s10, za vsi4ki drugi kletki se zadawa style id=s11
	 * 	)
	 * );
	 * 
	 * 
	 *
	 * Za medifikatori mogat da se polzwat za na4alo na poleto:
	 * S(H|M)##_ (s glawna bukva S) zadawa style ID za kletkata
	 * ako S e sledvano ot H se otnasq samo za headera
	 * ako sled S e M tazi kletka se mergva, sledva6tiq element
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $add_header
	 * @param unknown_type $skip_headers
	 */
	function renderDataFromDb($data,$add_header=true,$skip_headers=array()) {
		$header=array();
		$inner_index=0;
		
		foreach ($data as $k=>$v) {
		}		
	}
	
	
	
	function getMerge($value) {
		
	}
	
	function parseStyles() {
		
	}
	
	function parseHeader($field) {
		$result=array(
			'header'=>$field,
		);
		
		$parts=explode("$",$field);
		if(count($parts)==1) {
			$styles="";
			$data=$parts[0];
		}
		else {
			$styles=$parts[0];
			$data=$parts[1];
		}
		
		$result+=$this->parseStyles($styles);
		$result+=$this->parseData($data);
		
		
		$types=array("n","dt","dtt",);
		
		$s=explode("_");
		$work=$s[0];
		if($s[0][0]==="S") {
			if($s[0][1]==="H") {
				$style=substr($s[0],2);				
			}
			else {
				$style=substr($s[0],1);
			}
			$result['header_style']=$style;
			$work=$s[1];
		}
		
		$str=str_replace("_"," ",$k);
		
		if(!empty($str)) {
			$str[0]=strtoupper($str[0]);
		}
		$array[]=$str;
	}
	
}

?>