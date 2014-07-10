<?

define("DISTRIBUTION_TYPE_HORIZONTAL",1);
define("DISTRIBUTION_TYPE_VERTICAL",2);

$PrintVersionURL = "";


class CLib {
	
function format_date_for_edit($date){
	//list($res[d], $res[m], $res[y]) = explode("/", date("j/n/Y", $date));
	//return $res;
	if($date>0) return date("Y-m-d", $date);
	}
	
	function format_datetime_for_edit($date){
	//list($res[d], $res[m], $res[y], $res[h], $res[i], $res[s]) = explode("/", date("j/n/Y/H/i/s", $date));
	//return $res;
	if($date>0) return date("Y-m-d H:i:s", $date);
	}
	
var $babuk = 5;
	function render_admin_header($charset='utf-8',$title='',$css='',$js=''){
		if(empty($css)) {
			$css=BE_CSS_DIR."lib.css";
		}
		if(empty($js)) {
			$js=JS_DIR."lib.js";
		}
return <<<EOD
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$charset}">
	<title>{$title}</title>
	<link rel="stylesheet" href="{$css}">
	<script language="JavaScript" src="{$js}"></script>
	<meta name="DesignAndProgramming" content="ITTI Ltd. http://www.studioitti.com/">
</head>
EOD;
	}
	
	function format_errors_message(&$messages){
		if( count($messages)<=0 ) return;
		$HTML = "Those changes have not been allowed for the following reason(s):<br>\n";
		reset($messages);
		while( list($key, $val) = each($messages) )
		{
			$HTML .= "- $val <a href='#' onClick=\"return focus_control('$key');\">correct</a><br>\n";
		}
		return $HTML."<br>\n";
	}
	
	function format_warrnings_message(&$messages){
		if( count($messages)<=0 ) return;
		$HTML = "Warrnings for the following reason(s):<br>\n";
		reset($messages);
		while( list($key, $val) = each($messages) )
		{
			$HTML .= "- $val<br>\n";
		}
		return $HTML."<br>\n";
	}
	
function my_strtotime($str){
	if($str=="") return 0;
	return strtotime($str);
	//return strtotime(ereg_replace("([0-9]{1,2})/([0-9]{1,2})","\\2/\\1", $str));
	list($d, $m, $y) = explode("/",$str);
	list($y, $time) = explode(" ", $y, 2);
	if(!$y) $y = date("Y");
	if(!$time) $time = "00:00:00";
		if(strlen($m)>2)
		{
			return strtotime("$d $m $y $time");
		} else {
			return strtotime("$y-$m-$d $time");
		}
	
	}
	function my_format_date($date){
	if($date>0) return date("Y-m-d", $date);
	}
	
	function my_format_datetime($date, $delimeter=" "){
	//if($date>0) return date("j/M/Y".$delimeter."H:i:s", $date);
	if($date>0) return date("Y-m-d H:i:s", $date);
	}

	public static function convert_1d_to_2d_array($in_array, $DistributionDirection, $Cols, $Rows=0){

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
	$RowColVar = $DistributionDirection==DISTRIBUTION_TYPE_HORIZONTAL ? $Cols : $Rows;
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

	public static function render2dTable($cells){
		$TABLE = "<table class='radiotable'><tbody>";
		for($row=0; $row<count($cells); $row++){
			$TABLE .= "<tr>\n<td>".implode("</td>\n<td>", $cells[$row])."</td>\n</tr>\n";
		}
		$TABLE .= "</tbody></table>";

		return $TABLE;
	}

	function htmlspecialchars_array(&$arr){
		if( is_array($arr) )
		{
			reset($arr);
			while( list($key, $val) = each($arr) )
			{
				if( is_array($val) )
				{
					self::htmlspecialchars_array($val);
					$arr[$key] = $val;
				} else {
					$arr[$key] = htmlspecialchars($val);
				}
			}
		}
	}

/*        <------------              LIB2.PHP              ----------------->*/

	static function draw_listbox_options($options, $selected, $no_key=FALSE,$render_extra_attributes=false){
		if(!is_array($options)) return;
		//reset($options);
		$str="";
		$keys=array();
		foreach ($options as $k=>$v) {
			if(is_array($v)) {
				if(empty($keys)) {
					$keys=array_keys($v);
				}
				$values=array_values($v);
				$val=$values[0];
			}
			else {
				$val=$v;
			}
			if($no_key) {
				$k=$v;
			}
			$selected_str="";
			if(is_array($selected)&&in_array("$k",$selected)) {
				$selected_str=" selected=\"selected\" class=\"selected\"";
			}
			else {
				if("$k"=="$selected") {
					$selected_str=" selected=\"selected\" class=\"selected\"";
				}
			}
			$str_extra="";
			if($render_extra_attributes&&is_array($values)) {
				foreach ($keys as $kk=>$vv) {
					if(strtolower($vv)=="value") {
						continue;
					}
					$str_extra.=" {$vv}=\"".htmlspecialchars($values[$kk])."\" ";
				}
			}
			if($no_key) {
				$str.="<option{$selected_str}{$str_extra}>{$val}</option>";
			}
			else {
				$str.="<option{$selected_str}{$str_extra} value=\"".htmlspecialchars($k)."\">{$val}</option>";
			}
			
		}
		return $str;
		
		/*while( list($key, $val) = each($options) )
		{
			if($no_key) $key = $val;
			if( is_array($selected) && in_array("$key", $selected) )
			{
				$selectedStr = " SELECTED=\"true\" class=\"selected\"";
			} else if ("$key"=="$selected") {
				$selectedStr = " SELECTED=\"true\" class=\"selected\"";
			} else {
				$selectedStr = "";
			}
			if($no_key)
			{
				$HTML .= "<OPTION$selectedStr>".($val)."</OPTION>";
				//$HTML .= "<OPTION$selectedStr>".$val."\n";
			} else {
				$HTML .= "<OPTION$selectedStr VALUE=\"".htmlspecialchars($key)."\">".($val)."</OPTION>";
				//$HTML .= "<OPTION$selectedStr VALUE=\"".htmlspecialchars($key)."\">".$val."\n";
			}
		}
		return $HTML;*/
	}

	function draw_page_bar($pages, $page, $href, $maxpages = 20,$page_offset=1){
		$HTML = "";
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

		if ( $page > $page_offset ) $HTML .= "[<a href='$href".($page - 1)."'><<</a>] ";

		for( $i=$fp; $i <= $ep; $i++)
		{
			$str=$page_offset==0?$i+1:$i;

			if ( $i == $page ) $HTML .= "<b>$str</b> ";
			else $HTML .= "<a href='$href$i'>$str</a> ";
		}

		if ( $page < $pages ) $HTML .= "[<a href='$href".($page+1)."'>>></a>]";
		return $HTML;
	}

/*        <------------              LIB2.PHP              ----------------->*/


	function identify_credit_card_number($cc_no) {

		$cc_no = ereg_replace('[^0-9]+', '', $cc_no);

		// Get card type based on prefix and length of card number
	  if (ereg ('^4(.{12}|.{15})$', $cc_no))
	      return 'Visa';
	  if (ereg ('^5[1-5].{14}$', $cc_no))
	      return 'Mastercard';
	  if (ereg ('^3[47].{13}$', $cc_no))
	      return 'American Express';
	  if (ereg ('^3(0[0-5].{11}|[68].{12})$', $cc_no))
	      return 'Diners Club/Carte Blanche';
	  if (ereg ('^6011.{12}$', $cc_no))
	      return 'Discover Card';
	  if (ereg ('^(3.{15}|(2131|1800).{11})$', $cc_no))
	      return 'JCB';
	  if (ereg ('^2(014|149).{11})$', $cc_no))
	      return 'enRoute';

	  return 'unknown';
	}

	function draw_radio_options($name, $options, $selected, $no_key=FALSE,$extra='',$direction=0){
		if(!is_array($options)) return;
		reset($options);
		$id=0;
		while( list($key, $val) = each($options) )
		{
			if($no_key) $key = $val;
			if( is_array($selected) && in_array("$key", $selected) )
			{
				$selectedStr = " CHECKED ";
			} else if ($key==$selected) {
				$selectedStr = " CHECKED ";
			} else {
				$selectedStr = "";
			}
			if(is_array($extra)) {
				$strExtra=$extra[$key];
			}
			else {
				$strExtra=$extra;
			}
			if($direction==0) {
				$dir="<br>\n";
			}
			if($no_key)
			{
				$HTML .= "<input name=\"$name\" id=\"$name$id\" type=radio$selectedStr value=\"$val\" $strExtra>$dir";
			} else {
				$HTML .= "<input name=\"$name\" id=\"$name$id\" type=radio$selectedStr $strExtra VALUE=\"".htmlspecialchars($key)."\">&nbsp;".$val."$dir";
			}
			$id++;
		}
		return $HTML;
	}

	function draw_arrayToTable($arr,$columns=4,$td_attr='',$attributes="width='100%' cellpadding='0' cellspacing='0' border='0'") {
		$str="<table {$attributes}>";
		$counter=0;
		$needtr=false;
		foreach ($arr as $value) {
			if($counter==0) {
				$str.="<tr>";
				$needtr=true;
			}
			$str.="<td {$td_attr}>".$value."</td>";
			$counter++;
			if($counter==$columns)	{
				$str.="</tr>";
				$counter=0;
				$needtr=false;
			}
		}
		if($needtr)
			$str.="</tr>";
		return $str."</table>";
	}

	function checkImageType($src,$allowed_types=array(IMG_GIF,IMG_JPEG,IMG_JPG,IMG_PNG,3)) {
	//1=gif,2=jpeg,3=png,15=wbmp
		$width = 0;
		$height = 0;

		$info=-1;
		$attr=-1;
		list($width_orig, $height_orig,$info,$attr) = getimagesize($src);
		if($info==-1)
			return false;
		for($i=0;$i<count($allowed_types);$i++)
			if($allowed_types[$i]==$info)
				return array('width'=>$width_orig,'height'=>$height_orig,'type'=>$info,'attr'=>$attr);
		return false;
	}

}

class CValidation {
	function is_valid_postcode($str) {
		return eregi("^[a-pr-uwyz]([0-9]|[0-9][0-9]|[a-hk-y][0-9]|[a-hk-y][0-9][0-9]|[0-9][a-hjkstuw]|[a-hk-y][0-9][abehmnprv-y])\ ?[0-9][abd-hjlnp-uw-z][abd-hjlnp-uw-z]$", $str);
	}

	function is_valid_email_address(&$email){
		return ereg("^[^@]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$", $email);
	}

	function is_valid_credit_card_number($cc_no) {
	  // Reverse and clean the number
	  $cc_no = strrev(ereg_replace ('[^0-9]+', '', $cc_no));

		if(strlen($cc_no)<10) return FALSE;
	  // VALIDATION ALGORITHM
	  // Loop through the number one digit at a time
	  // Double the value of every second digit (starting from the right)
	  // Concatenate the new values with the unaffected digits
	  for ($ndx = 0; $ndx < strlen ($cc_no); ++$ndx)
	      $digits .= ($ndx % 2) ? $cc_no[$ndx] * 2 : $cc_no[$ndx];

	  // Add all of the single digits together
	  for ($ndx = 0; $ndx < strlen ($digits); ++$ndx)
	      $sum += $digits[$ndx];

	  // Valid card numbers will be transformed into a multiple of 10
	  return ($sum % 10) ? FALSE : TRUE;
	}
}
?>