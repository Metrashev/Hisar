<?php


function ob_include($file, $data=array()){
  ob_start();
  include($file);
  return ob_get_clean();
}

/**
 * Encapsulated include
 *
 * @param string $file
 */
function enc_include($file){
	include($file);
}

/**
 * Encapsulated require_once
 *
 * @param unknown_type $file
 */
function enc_require_once($file){
	require_once($file);
}

/*
function __autoload($funcName) {
	//require_once(dirname(__FILE__).'/'.$GLOBALS['CONFIG']['AutoLoad'][$funcName]);
	if(DEBUG_MODE) {
		if(!is_file($GLOBALS['CONFIG']['AutoLoad'][$funcName])) {

			throw new Exception("File is Missing <b>{$funcName}</b>");
		}
	}
	require_once($GLOBALS['CONFIG']['AutoLoad'][$funcName]);
}
*/
function stripSlashesRecurive($data)
{
	return is_array($data)?array_map('stripSlashesRecurive',$data):stripslashes($data);
}

function beginRequest()
{
  static $inputStripped = false;

	if(!$inputStripped && get_magic_quotes_gpc())
	{
		if(isset($_GET))
			$_GET=stripSlashesRecurive($_GET);
		if(isset($_POST))
			$_POST=stripSlashesRecurive($_POST);
		if(isset($_REQUEST))
			$_REQUEST=stripSlashesRecurive($_REQUEST);
		if(isset($_COOKIE))
			$_COOKIE=stripSlashesRecurive($_COOKIE);
		$inputStripped = true;
	}
}

function isEmptyArray($data)
{
	foreach ($data as $k=>$val) {
		if(!(is_array($val) ? isEmptyArray($val) : empty($val))) return false;
	}
	return true;
}


function getFileExt($name) {
	$p=pathinfo($name);
	return empty($p['extension'])?'':'.'.$p['extension'];
}

/*
convert_1d_to_2d_array - this function converts 1 dimensional array to 2 dimensional array

$in_array - Input 1 dimentional array. assoc arrays are accepted.
$DistributionDirection - Horizontal | Vertical
$Cols - Number of cols in output array.
$Rows - Number of rows in output array.
If $Cols is set $Rows must be 0 and vice versa
*/

function convert_1d_to_2d_array($in_array, $DistributionDirection, $Cols, $Rows=0){

	if(!is_array($in_array)) return false;
	if($Cols + $Rows === 0) return false;

	if($Cols>0){
		$Rows = ceil(count($in_array)/$Cols);
	} else if ($Rows>0) {
		$Cols = ceil(count($in_array)/$Rows);
	}

	$out_array=Array();
/*
	for($row=0; $row<$Rows; $row++) {
		for($col=0; $col<$Cols; $col++) {
			$index = $DistributionDirection=='Horizontal' ? $row*$Cols+$col : $row+$Rows*$col;
			if( isset($in_array[$index]) ){
				$out_array[$row][$col] = $in_array[$index];
			} else {
				$out_array[$row][$col] = "";
			}
		}
	}
*/
	$RowColVar = $DistributionDirection=='Horizontal' ? $Cols : $Rows;
	$index = 0;
	foreach($in_array as $val){
		$row = (int)($index/$RowColVar);
		$col = $index % $RowColVar;
		$out_array[$row][$col] = $val;
		$index++;
	}

	for($index; $index<$Rows*$Cols; $index++){
		$row = (int)($index/$RowColVar);
		$col = $index % $RowColVar;
		$out_array[$row][$col] = "";
	}

	return $out_array;
} // End function convert_1d_to_2d_array


function arrayToTable($table, $tr='<tr>', $td='<td>'){
	$res = '';
	foreach ($table as $row)
		$res .= "\n$tr\n$td".implode("</td>\n$td", $row)."</td>\n</tr>\n";
	return $res;
}

function arrayToTableStyle($table){
	$res = '';
	$r = 0;
	$TRcnt = count($table);
	$TDcnt = count(reset($table));
	foreach ($table as $row){
		$r++;
		$TRclass = $r % 2 ? 'odd' : 'even';
		if($r==1) $TRclass.=' first';
		if($r==$TRcnt) $TRclass.=' last';
		$TRclass .= ' tr'.$r;
		
		$res .= "<tr class='$TRclass'>\n";
		$c = 0;
		foreach ($row as $cell){
			$c++;
			$TDclass = $c % 2 ? 'odd' : 'even';
			if($c==1) $TDclass.=' first';
			if($c==$TDcnt) $TDclass.=' last';
			$TDclass .= ' td'.$c;
			$res .= "<td class='$TDclass'>$cell</td>\n";
		}
		$res .= "</tr>\n";
	}
	return $res;
}

class CSelectOptionsRenderer {

	public $Options;
/*
array(

array(
	'style'=>'color:#FF',
	'label'=>'dfdsdsfdfs',
	'id'=>'1',

	'style'=>'color:#FF',
	''
),
array(
'isOptionGroup'=>true,
	'style'=>'color:#FF',
	'label'=>'Zelencuci',

	'style'=>'color:#FF',
	'Options'=>array(1=>'Zele','2'=>'Morkovi'),
),

5=>'Morkov',
6=>'Krastavica',
-1=>array('isOptionGroup'=>true,'label'=>'Plodove'),
7=>'Krusha',
8=>'Iabalka',

)
*/



	static function renderOptions($val, $Options){
		$res = '';
		foreach ($Options as $k=>$v){
			if(is_array($v)){
				if(isset($v['isOptionGroup'])){
					unset($v['isOptionGroup']);
					if(isset($v['Options'])){
						$Options = $v['Options'];
						unset($v['Options']);
						$attr = self::buildAttributes($v);
						$res .= "<optgroup {$attr}>";
						$res .= self::renderOptions($val, $Options);
						$res .= '</optgroup>';
					} else {
						$attr = self::buildAttributes($v);
						$res .= "<optgroup {$attr}/>";
					}
				} else { // Render na 1 Option ot DataSource
					$v['value']=$v['id'];
					$label = $v['label'];
					unset($v['label']);
					unset($v['id']);
					$selected = self::isSelected($val, $v['value']);
					$attr = self::buildAttributes($v);
					$res .= "<option {$attr}{$selected}>$label</option>";
				}
			} else {
				$selected = self::isSelected($val, $k);
				$k = htmlspecialchars($k);
				$res .= "<option value=\"$k\" $selected>$v</option>";
			}
		}
		return $res;
	}

	static function isSelected($val, $k){
		if(is_array($val)){
			return in_array("$k", $val) ? 'selected="selected" ' : '';
		} else {
			return "$val"=="$k" ? 'selected="selected" ' : '';
		}
	}

	static function buildAttributes($a){
		$res = '';
		foreach ($a as $k=>$v) {
			$res .= $k.'="'.htmlspecialchars($v).'" ';
		}
		return $res;
	}
}



class CFEPageBar {

  private $GETVarName;

  public $pages;
  public $CurrentPage;

  function __construct($ItemsPerPage, $ItemsCnt, $GETVarName='p'){
  	$this->GETVarName = $GETVarName;
    $this->pages = ceil($ItemsCnt/$ItemsPerPage);
    $this->CurrentPage = (int)$_REQUEST[$this->GETVarName];
    if($this->CurrentPage==0) $this->CurrentPage = 1;

  }

  function getData($href){
  	
  	if(empty($href)){
  		$href = $_GET;
  		unset($href[$this->GETVarName]);
  		$href = empty($href) ? '' : '?'.http_build_query($href);
  	}

    $result = array();

    $separator = strpos($href, "?")===false ? '?':'&amp;';
    $separator .= $this->GETVarName.'=';

    $result['total'] = $this->pages;
    $result['current'] = $this->CurrentPage;

    if ($this->CurrentPage==1){
      $result['prev'] = '';
      $result['first'] = '';
    } else {
      $result['prev'] = $this->CurrentPage>2 ? $href.$separator.($this->CurrentPage-1) : $href;
      $result['first'] = $href;
    }

    if ($this->CurrentPage==$this->pages){
      $result['next'] = '';
      $result['last'] = '';
    } else {
      $result['next'] = $href.$separator.($this->CurrentPage+1);
      $result['last'] = $href.$separator.($this->pages);
    }


    list($fp, $lp) = self::getPagesRange($this->pages, $this->CurrentPage);
    for($i=$fp; $i<=$lp; $i++){
      if($i==1) {
        $result['pages'][$i] = $href;
      } else {
        $result['pages'][$i] = $href.$separator.$i;
      }
    }
    $result['pages'][$this->CurrentPage] = '';
    return $result;
  }

  static function getPagesRange($pages, $page, $maxpages = 20){
        $page_offset=1;
        $dl = ceil( ($maxpages-1) / 2 );
        $dr = floor( ($maxpages-1) / 2 );

        if( $maxpages >= $pages )
        {
            $fp = $page_offset;
            $ep = $pages;
        } else {
            $fp = $page - $dl;
            $ep = $page + $dr;
            if ( $fp < $page_offset )
            {
                $fp = $page_offset;
                $ep = $maxpages;
            }

            if ( $ep > $pages )
            {
                $ep = $pages;
                $fp = $pages - $maxpages;
            }
        }

      return array($fp, $ep);
  }


}

define('REGEXP_EMAIL', '^[^ ,;]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$');
define('REGEXP_EMAIL_LIST', '^([^ ,;]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4})( *[,;] *[^ ,;]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4})*$');

function is_valid_email_address($email){
	return preg_match('/'.REGEXP_EMAIL.'/', $email);
}

if(!function_exists('get_called_class')) { 
function get_called_class($bt = false,$l = 1) { 
	
    if (!$bt) $bt = debug_backtrace(); 
    if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep."); 
    if (!isset($bt[$l]['type'])) { 
        throw new Exception ('type not set'); 
    } 
    else switch ($bt[$l]['type']) { 
        case '::': 
            $lines = file($bt[$l]['file']); 
            $i = 0; 
            $callerLine = ''; 
            do { 
                $i++; 
                $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine; 
            } while (stripos($callerLine,$bt[$l]['function']) === false); 
            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', 
                        $callerLine, 
                        $matches); 
            if (!isset($matches[1])) { 
                // must be an edge case. 
                throw new Exception ("Could not find caller class: originating method call is obscured."); 
            } 
            switch ($matches[1]) { 
                case 'self': 
                case 'parent': 
                    return get_called_class($bt,$l+1); 
                default: 
                    return $matches[1]; 
            } 
            // won't get here. 
        case '->': switch ($bt[$l]['function']) { 
                case '__get': 
                    // edge case -> get class of calling object 
                    if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object."); 
                    return get_class($bt[$l]['object']); 
                default: return $bt[$l]['class']; 
            } 

        default: throw new Exception ("Unknown backtrace method type"); 
    } 
} 
} 


#####################################
## BELOW ADDED BY ROSEN 2011-08-12 ##
#####################################


/**
 * If you want to use an expression in a HEREDOC string, you can use the following syntax:
 * {$heredoc(expression)}
 * $heredoc is a global variable containing the name of the heredoc function ('heredoc')
 *
 * @param mixed $parameter
 * @return mixed
 */
function heredoc($parameter) {
	return $parameter;
}

$GLOBALS['heredoc'] = 'heredoc';


/**
 * Sorts $array by key, using $sort_like as the order of keys desired (using the values of $sort_like for comparison).
 * Keys from $array that do not exist in $sort_like are placed last.
 *
 * @param array $array
 * @param array $sort_like
 * @return void
 */
function array_ksort_like_another_array(&$array, $sort_like) {
	$copy = $array;
	$array = array();

	foreach ($sort_like as $k) {
		if (isset($copy[$k])) {
			$array[$k] = $copy[$k];
			unset($copy[$k]);
		}
	}

	$array = $array + $copy;
}


/**
 * PHP equivalent of the Javascript function
 *
 * @param string $str
 * @return string
 */
function encodeURIComponent($str) {
	$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	return strtr(rawurlencode($str), $revert);
}


/**
 * If you call "explode" on an empty string, it will return an array with one element (the empty string). For ID lists,
 * this doesn't make sense - this function removes empty elements instead, so empty string will result in an empty array.
 *
 * @return array
 * @param string $separator
 * @param string $string
*/
function my_explode($separator, $str) {
	if (strlen($separator)==0) return false;

	$result = explode($separator, $str);
	$real_result = array();

	if (is_array($result)) {
		foreach (array_keys($result) as $key)
			if ($result[$key]!='') $real_result[] = $result[$key];
	} else {
		return false;
	}

	return $real_result;
}


/**
 * Explodes an ID list and ensures that the result contains only integers (removing zeros by default)
 *
 * @param string $separator
 * @param string $string
 * @return array
 */
function explode_id_list($separator, $string, $remove_zeros=true) {
	$result = my_explode($separator, $string);
	$result = array_map('trim', $result);
	$result = array_map('intval', $result);

	if ($remove_zeros) $result = array_remove_zero_values($result);

	return $result;
}


/**
 * Returns all the values of $array whose keys are listed in $keys
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_intersect_by_keys($array, $keys) {
	foreach ($array as $k=>$v)
		if (!in_array($k, $keys)) unset($array[$k]);

	return $array;
}


/**
 * Same as array_intersect_by_keys, but values are sorted according to $keys
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_intersect_by_keys_sorted($array, $keys) {
	$result = array();

	foreach ($keys as $key) {
		if (isset($array[$key]))
			$result[$key] = $array[$key];
	}

	return $result;
}


/**
 * @param array $array
 * @return mixed
 */
function array_get_last_key($array) {
	if (!is_array($array)) return null;

	end($array);
	return key($array);
}


/**
 * @param array $array
 * @return mixed
 */
function array_get_last_value($array) {
	if (!is_array($array)) return null;

	end($array);
	return current($array);
}


/**
 * Returns the key of the first value in the array
 *
 * @param array $array
 * @return mixed
 */
function array_get_first_key($array) {
	if (!is_array($array)) return null;
	$keys = array_keys($array);
	if (isset($keys[0])) return $keys[0];
	return null;
}


/**
 * Returns the first value from the array
 *
 * @param array $array
 * @return mixed
 */
function array_get_first_value($array) {
	return $array[array_get_first_key($array)];
}


/**
 * Recursive function, trims every element of a multidimensional array
 *
 * @param array $array
 */
function trim_array(&$array) {
	if (is_array($array)) {
		foreach ($array as $k=>$v) trim_array($array[$k]);
	} else {
		$array = trim($array);
	}
}


/**
 * Alias for trim_array()
 *
 * @param array $array
 */
function array_trim(&$array) {
	trim_array($array);
}


/**
 * Removes all elements with value==$value, which could be more than one
 *
 * @param array $array
 * @param mixed $value
 */
function array_delete_value(&$array, $value) {
	foreach (array_keys($array, $value) as $k) unset($array[$k]);
}


/**
 * Removes all values which string representation is empty
 *
 * @param array $array
 * @return array
 */
function array_remove_empty_values($array) {
	foreach ($array as $k=>$v) {
		if ("$v"==='') unset($array[$k]);
	}

	return $array;
}


/**
 * Removes all values which float representation is zero
 *
 * @param array $array
 * @return array
 */
function array_remove_zero_values($array) {
	foreach ($array as $k=>$v) {
		if (floatval($v)==0) unset($array[$k]);
	}

	return $array;
}


/**
 * Removes all values which number representation is less than zero
 *
 * @param array $array
 * @return array
 */
function array_remove_negative_values($array) {
	foreach ($array as $k=>$v) {
		if (floatval($v)<0) unset($array[$k]);
	}

	return $array;
}


/**
 * Since this is intended for checking whether $string can be used as "col IN ($string)",
 * the function returns FALSE if $string is empty
 *
 * @param string $string
 * @return boolean
 */
function is_valid_sql_id_list($string) {
	if ($string=='') return false;

	return (boolean)preg_match('/^(\d+,)*\d+$/', $string);
}


/**
 * @param array $array
 * @return mixed
 */
function array_get_first_nonempty_value($array) {
	foreach ($array as $val) {
		if (!empty($val)) return $val;
	}
}


/**
 * If you cast a scalar value to an array using (array), you will get an array with that value as its single element.
 * This function will return an empty array instead. Useful when you expect to get an empty string, NULL, or false from another function.
 *
 * @param mixed $value
 * @return array
 */
function get_array($value) {
	return is_array($value) ? $value : array();
}

