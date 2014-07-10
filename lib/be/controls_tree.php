<?
class HTMLElement {
	var $elementName;
	var $Attributes;
	var $_innerHTML;

	function HTMLElement($elementName, $innerHTML=""){
		$this->elementName = $elementName;
		$this->setInnerHTML($innerHTML);
	}

	function _getAttrString($attributes){
		$strAttr = '';

		if(is_array($attributes)) {
			foreach ($attributes as $key => $value) {
				if(is_array($value)) $value = implode(";", $value);
				if($value===false || $value==="" || $value===NULL) continue;
				$strAttr .= " {$key}";
				if($value!==true) $strAttr .= '="'.str_replace("\"", "&quot;", $value).'"';
			}
		}
		return $strAttr;
	}

	function setInnerHTML($HTML){
		$this->_innerHTML = $HTML;
	}

	function toHtmlOpenTag(){
		return "<".strtoupper($this->elementName).$this->_getAttrString($this->Attributes).">";
	}

	function toHtml(){
		$elements_wthout_end_tag = Array('BR', 'INPUT', 'META');

		$this->elementName = strtoupper($this->elementName);

		$HTML = "<{$this->elementName} ";
		$HTML .= $this->_getAttrString($this->Attributes);

		if(!in_array($this->elementName, $elements_wthout_end_tag)) {
			$HTML .= ">{$this->_innerHTML}</{$this->elementName}>";
		} else {
			$HTML .= "/>{$this->_innerHTML}";
		}
		return $HTML;
	}
}

class MyFormatedDate{
	
	function MyFormatedDate($date){
		$this->DateFormat = 'd/m/y';
		$this->TimeFormat = 'H:i';
		$this->DateTimeFormat = 'd/m/y H:i';
		$this->setDateTime($date);
	}
	
	function setDateTime($date){
		if(is_int($date)){
			$this->date = $date;
		} elseif(is_string($date)){
			$date = eregi_replace('^ *([0-9]{1,2}) */ *([0-9]{1,2})(.*)', "\\2/\\1\\3", $date);
			if(eregi('[a-z]+', $date))
				$date = ereg_replace("[/-]", " ", $date);
			$this->date = strtotime($date);
		}
	}
	
	function formatedDate(){
		return date($this->DateFormat, $this->date);
	}
	
	function formatedDateTime(){
		return date($this->DateTimeFormat, $this->date);
	}
	
	function formatedTime(){
		return date($this->TimeFormat, $this->date);
	}
	
	function getISODate(){
		return date("Y-m-d", $this->date);
	}
	
	function getISODateTime(){
		return date("Y-m-d H:i:s", $this->date);
	}
}


function render_html_tag($tagname, $tagparameters){
	return "<$tagname $tagparameters/>$body</$tagname>";
}

function render_input_tag($tagparameters){
	return "<INPUT $tagparameters/>";
}

class CForm{
}

class CButton extends HTMLElement {
	var $control_name;
	var $value;
	var $static_render;
	
	function CButton($name,$value,$is_submit, $properties) {
		$this->static_render = false;

		$this->value=$value;
		$this->control_name = $name;
		if (!is_array($properties)) $properties = array();
		
		parent::HTMLElement('INPUT');
		$this->Attributes = $properties;
		$type=$is_submit?'submit':'button';
		$this->Attributes['type'] = $type;
	}

	function loadData(&$array) {
		
	}

	function getDataForDb() {
		return array('__skip_write'=>1);
	}
	
	function setValue($value){
		$this->value = $value;
	}

	function getErrorMessage() {
		return '';
	}

	function validate(&$errors_array) {
		return true;
	}

	function render() {
		$this->Attributes['name'] = $this->control_name;
		$this->Attributes['value'] = $this->value;

		if ($this->static_render) return $this->value;

		return parent::toHtml();
	}

	function toTD($addLabel=false) {
		return "<TD>".$this->render()."</TD>";
	}

	function toTR() {
		$input = $this->render();
		return <<<EOD
<TR>
<TD>{$input}</TD>
</TR>
EOD;
	}
	
}
	

class CTextInput extends HTMLElement {
	var $control_name;
	var $value;
	var $label_text;
	var $required;
	var $regexp;
	var $static_render;
	var $password=false;

	function CTextInput($name, $label_text, $properties, $required, $regexp) {
		$this->static_render = false;

		$this->control_name = $name;
		if (!is_array($properties)) $properties = array();
		$this->label_text = $label_text;
		$this->required = $required ? true : false;
		$this->regexp = $regexp;

		parent::HTMLElement('INPUT');
		$this->Attributes = $properties;
		$this->Attributes['type'] = 'text';
	}

	function loadData(&$array) {
		
		if (array_key_exists($this->control_name, $array)) {
			$this->value = $array[$this->control_name];
		} else {
			$this->value = '';
		}
	}

	function getDataForDb() {
		return $this->value;
	}
	
	function setValue($value){
		$this->value = $value;
	}

	function getErrorMessage() {
		return "Field '{$this->label_text}' is incorrect!";
	}

	function validate(&$errors_array) {
		if (!$this->required && $this->value=='') return true;

		if ($this->regexp=='')
			if ($this->value!='') return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				return false;
			}
		else
			if (eregi($this->regexp, $this->value)) return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				return false;
			}
	}

	function render() {
		if($this->password)
			$this->Attributes['type'] = 'password';
			
		$this->Attributes['name'] = $this->control_name;
		$this->Attributes['value'] = $this->value;

		if ($this->static_render) return $this->value;

		return parent::toHtml();
	}

	function toTD($addLabel=false) {
		if($addLabel)
			return "<TD><label style='width:100px'>".$this->label_text."</label>".$this->render()."</TD>";
		return "<TD>".$this->render()."</TD>";
	}

	function toTR() {
		$input = $this->render();
		return <<<EOD
<TR>
<TH align=right>{$this->label_text}:</TH>
<TD>{$input}</TD>
</TR>
EOD;
	}
}

class CTextarea extends HTMLElement {
	var $control_name;
	var $value;
	var $label_text;
	var $required;
	var $regexp;
	var $static_render;
	var $password=false;

	function CTextarea($name, $label_text, $properties, $required, $regexp) {
		$this->static_render = false;

		$this->control_name = $name;
		if (!is_array($properties)) $properties = array();
		$this->label_text = $label_text;
		$this->required = $required ? true : false;
		$this->regexp = $regexp;

		parent::HTMLElement('TEXTAREA');
		$this->Attributes = $properties;
	}

	function loadData(&$array) {
		if (array_key_exists($this->control_name, $array)) {
			$this->value = $array[$this->control_name];
		} else {
			$this->value = '';
		}
	}

	function getDataForDb() {
		return $this->value;
	}
	
	function setValue($value){
		$this->value = $value;
	}

	function getErrorMessage() {
		return "Field '{$this->label_text}' is incorrect!";
	}

	function validate(&$errors_array) {
		if (!$this->required && $this->value=='') return true;

		if ($this->regexp=='')
			if ($this->value!='') return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				return false;
			}
		else
			if (eregi($this->regexp, $this->value)) return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				return false;
			}
	}

	function render() {
		$this->Attributes['name'] = $this->control_name;
		$this->_innerHTML = htmlspecialchars($this->value);
		if ($this->static_render) return $this->value;

		return parent::toHtml();
	}

	function toTD($addLabel=false) {
		if($addLabel)
			return "<TD><label style='width:100px'>".$this->label_text."</label>".$this->render()."</TD>";
		return "<TD>".$this->render()."</TD>";
	}

	function toTR() {
		$input = $this->render();
		return <<<EOD
<TR>
<TH align=right valign=top>{$this->label_text}:</TH>
<TD>{$input}</TD>
</TR>
EOD;
	}
}

class CPasswordInput extends HTMLElement {
	var $control_name;
	var $value;
	var $value_confirm;
	var $label_text;
	var $label_text_confirm;
	var $required;
	var $regexp;
	var $static_render;

	function CPasswordInput($name, $label_text, $properties, $required, $regexp) {
		$this->static_render = false;

		$this->control_name = $name;
		if (!is_array($properties)) $properties = array();
		$this->label_text = $label_text;
		$this->label_text_confirm = "Confirm password";
		$this->required = $required ? true : false;
		$this->regexp = $regexp;

		parent::HTMLElement('INPUT');
		$this->Attributes = $properties;
		$this->Attributes['type'] = 'password';
	}

	function loadData(&$array) {
		if (array_key_exists($this->control_name, $array)) {
			$this->value = $array[$this->control_name];
			$this->value_confirm = $array[$this->control_name];
		} elseif (array_key_exists($this->control_name."_1", $array) && array_key_exists($this->control_name."_2", $array)) {
			$this->value = $array[$this->control_name."_1"];
			$this->value_confirm = $array[$this->control_name."_2"];
		}
	}

	function getDataForDb() {
		return $this->value;
	}
	
	function setValue($value){
		$this->value = $value;
		$this->value_confirm = $value;
	}

	function getErrorMessage() {
		return "Field '{$this->label_text}' is incorrect!";
	}

	function validate(&$errors_array) {
		if (!$this->required && $this->value=='') return true;

		if ($this->regexp=='')
			if ($this->value!='' && $this->value==$this->value_confirm) return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				$this->label_text_confirm = '<font color=red>'.$this->label_text_confirm.'</font>';
				return false;
			}
		else
			if (eregi($this->regexp, $this->value) && $this->value==$this->value_confirm) return true;
			else {
				$errors_array[$this->control_name]=$this->getErrorMessage();
				$this->label_text = '<font color=red>'.$this->label_text.'</font>';
				$this->label_text_confirm = '<font color=red>'.$this->label_text_confirm.'</font>';
				return false;
			}
	}

	function render($confirm=false) {
		$this->Attributes['name'] = $this->control_name.($confirm ? "_2" : "_1");
		$this->Attributes['value'] = ( $confirm ? $this->value_confirm : $this->value );
		
		if ($this->static_render) return $this->value;
		
		return parent::toHtml();
	}

	function toTD($confirm=false,$addLabel=false) {
		return "<TD>".$this->render($confirm)."</TD>";
	}

	function toTR() {
		$input1 = $this->render(false);
		$input2 = $this->render(true);
		
		$label1 = $this->label_text;
		$label2 = $this->label_text_confirm;
		
		return <<<EOD
<TR>
<TH align=right>{$label1}:</TH>
<TD>{$input1}</TD>
</TR>
<TR>
<TH align=right>{$label2}:</TH>
<TD>{$input2}</TD>
</TR>
EOD;
	}
}

class CSelectOption extends HTMLElement {
	var $name;
	var $multiple;
	var $options;
	var $value;
	var $label_text;
	var $static_render;
	var $autoPostBack=false;
	var $null_option = false;
	var $required = false;

	function CSelectOption($name, $options, $multiple, $label='') {
		$this->static_render = false;

		$this->name = $name;
		$this->options = $options;
		$this->multiple = $multiple;
		$this->label_text = $label;
		parent::HTMLElement('SELECT');
	}

	function loadData(&$array) {
		if (array_key_exists($this->name, $array)) {
			$value = $array[$this->name];
			if ($this->multiple && !is_array($value)) {
				$value = explode(',',$value);
			}
			$this->value = $value;
		}
	}
	
	function setValue($value){
		if ($this->multiple && !is_array($value)) {
			$value = explode(',',$value);
		}
		$this->value = $value;
	}

	function getDataForDb() {
		if (is_array($this->value)) return implode(',', $this->value);
		else return (string)$this->value;
	}

	function validate(&$errors_array) {
		if (!$this->required || !$this->null_option) return true;
		
		if ($this->value>0) {
			return true;
		} else {
			$this->label_text = "<font color=red>".$this->label_text."</font>";
			$errors_array[$this->name] = "You must select an option for '{$this->label_text}'";
			return false;
		}
	}

	function render() {
		
		if ($this->static_render) return $this->options[$this->value];

		$opt = CLib::draw_listbox_options($this->options, $this->value);
		if ($this->null_option) $opt = "<option value=0>".$opt;
		$this->setInnerHTML($opt);
		$this->Attributes['name'] = $this->name . ($this->multiple?"[]":"");
		$this->Attributes['multiple'] = $this->multiple;
		if($this->autoPostBack)
			$this->Attributes['onChange'] = "__doPostBack('".$this->name."','')";
		return  $this->toHtml();

		$select = "<select name=" . $this->name . ($this->multiple?"[]":"") . ($this->multiple?" multiple":"") . ">";
		return <<<EOD
{$select}
{$opt}
</select>
EOD;
	}

	function toTD($addLabel=false) {
		if($addLabel)
			return "<TD>\n<label style='width:100px'>".$this->label_text."</label>".$this->render()."</TD>";
		return "<TD>\n".$this->render()."</TD>";
	}

	function toTR() {
		$td = $this->toTD();
		if ($this->multiple) $top = " valign=top";
		return <<<EOD
<TR>
<TH align=right{$top}>{$this->label_text}:</TH>
{$td}
</TR>
EOD;
	}
}

class CCheckBox extends HTMLElement {
	var $value;
	var $name;
	var $label_text;
	var $static_render;

	function CCheckBox($name, $label) {
		$this->static_render = false;

		$this->name = $name;
		$this->label_text = $label;
		parent::HTMLElement('INPUT');
		$this->Attributes['type'] = 'checkbox';
	}

	function loadData(&$data) {
		if (!array_key_exists($this->name, $data)) {
			$this->value = "";
		} elseif (($data[$this->name]==1) || ($data[$this->name]==="checked")) {
			$this->value = "checked";
		} else {
			$this->value = "";
		}
	}

	function getDataForDb() {
		if ($this->value=='checked') return 1;
		else return 0;
	}

	function validate(&$errors) {
		return true;
	}

	function render() {
		if ($this->static_render) {
			return ($this->value=='checked'?'Yes':'No');
		}

		$this->Attributes['name'] = $this->name;
		$this->Attributes['id'] = $this->name;
		$this->Attributes['value'] = 'checked';
		$this->Attributes['checked'] = (boolean)$this->value;

		return parent::toHtml();
		//return "<input type='checkbox' name='{$this->name}' id='{$this->name}' value='checked' {$this->value}>";
	}

	function renderLabel(){
		return "<label for=\"{$this->name}\">{$this->label_text}</label>";
	}

	function toTD($addLabel=false) {
		if($addLabel)
			return "<TD><label style='width:100px'>".$this->label_text."</label>".$this->render()."</TD>";
		return "<TD>".$this->render()."</TD>";
	}

	function toTR($label_postition='right') {
		$input = $this->render();

		if ($this->static_render)
			return <<<EOD
<TR>
<TH align=right>{$this->label_text}:</TH>
<TD align=left>{$input}</TD>
</TR>
EOD;
	if($label_postition=='right')

		return <<<EOD
<TR>
<TH>&nbsp;</TH>
<TD align=left>{$input} <label for="{$this->name}">{$this->label_text}</label></TD>
</TR>
EOD;
	return <<<EOD
<TR>
<TH align='left'><label for="{$this->name}">{$this->label_text}</label></TH>
<TD align=left> {$input}</TD>
</TR>
EOD;
	
	}
}


function draw_page_bar2($pages, $page, $name, $maxpages = 20){
	$HTML = "";
	$dl = ceil( ($maxpages-1) / 2 );
	$dr = floor( ($maxpages-1) / 2 );

	if( $maxpages >= $pages )
	{
		$fp = 1;
		$ep = $pages;
	} else {
		$fp = $page - $dl;
		$ep = $page + $dr;
		if ( $fp < 1 )
		{
			$fp = 1;
			$ep = $maxpages;
		}

		if ( $ep > $pages )
		{
			$ep = $pages;
			$fp = $pages - $maxpages;
		}
	}

	if ( $page > 1 ) $HTML .= "[<a href='#' onClick=\"__doPostBack('$name', '".($page - 1)."');\"><<</a>] ";

	for( $i=$fp; $i <= $ep; $i++)
	{
		if ( $i == $page ) $HTML .= "<b>$i</b> ";
		else $HTML .= "<a href='#' onClick=\"__doPostBack('$name', '$i');\">$i</a> ";
	}

	if ( $page < $pages ) $HTML .= "[<a href='#' onClick=\"__doPostBack('$name', '".($page + 1)."');\">>></a>]";
	return $HTML;
}

class Table_view{
	var $table_title;
	var $table_view_name;
	var $data_processing_function;
	var $number_of_columns;
	var $number_of_rows;
	// var $header_column;
	var $href;
	var $ord;
	
	var $tableWidthStr = '98%';
	var $pageBarWidth = 590;

function Table_view(&$columns, $default_order_field, $records_per_page, $table_view_name, $data_processing_function, $count_query, $data_query, &$DB_Connection){

	$this->Query =& $DB_Connection;
	$this->number_of_columns = count($columns);
	$this->add_columns($columns);
//echo "<pre>"	
//print_r($_SERVER);
	//$this->href = basename($_SERVER['SCRIPT_NAME']);
	$this->href = $_SERVER['SCRIPT_URL'];
	$this->table_view_name = $table_view_name;
	$this->data_processing_function = $data_processing_function;
	$this->records_per_page = $records_per_page;

	$ORDER = $this->build_order_query_part($default_order_field);
	$LIMIT = $this->build_limit_query_part($count_query);
	$this->data_query = $data_query.$ORDER.$LIMIT;
}

function build_order_query_part($default_order_field){

	if($_GET[ord])
		$_SESSION[$this->table_view_name][ord] = $_GET[ord];

	$this->ord = $_SESSION[$this->table_view_name][ord];
	if(!$this->orders[abs($this->ord)])
	{
		$this->ord = $default_order_field;
		$_SESSION[$this->table_view_name][ord] = $this->ord;
	}
	if(!$this->orders[abs($this->ord)]) return "";
	$ORDER = " ORDER BY ".$this->orders[abs($this->ord)].($this->ord>0 ? " ASC" : " DESC");
	return $ORDER;
}

function build_limit_query_part($count_query){

if($_GET[page])
	$_SESSION[$this->table_view_name][page] = $_GET[page];

if($_SESSION[$this->table_view_name][page] < 1)
	$_SESSION[$this->table_view_name][page] = 1;
$page = $_SESSION[$this->table_view_name][page];

	$this->number_of_rows = $this->Query->getOne($count_query);
	$this->number_of_pages = ceil($this->number_of_rows/$this->records_per_page);
	if($this->number_of_pages>1)
	{
		if($page>$this->number_of_pages) $page = $this->number_of_pages;

		$this->page_bar_HTML .= "<table bgcolor=#EEEEEE id=tbl cellspacing=0 cellpadding=3 border=0 width={$this->pageBarWidth}><form action=\"$this->href\"><tr>\n";
		$this->page_bar_HTML .= "<td width=1% nowrap>Page $page of $this->number_of_pages<br>$this->number_of_rows Records</td><td align=center>\n";
		$this->page_bar_HTML .= CLib::draw_page_bar($this->number_of_pages, $page, $this->href."?page=", 20);

		$this->page_bar_HTML .= "</td><td align=right width=1% nowrap>Go to <input size=3 type=text name=page value=\"$page\"></td></tr></form></table>\n";

		$starting_record = ($page-1) * $this->records_per_page;
		$LIMIT = " LIMIT $starting_record, $this->records_per_page";
	} else {
		$LIMIT = "";
	}
	return $LIMIT;
}

function set_query($SQL){
	$this->build_limit_query_part();
	$this->main_query = $SQL.$ORDER.$this->LIMIT;
}


//function add_column($type, $title, $order_field, $ord, $header_align, $rows_align){

function add_columns(&$columns){
	for($n = 0; $n<$this->number_of_columns; $n++)
	{
	$this->header_column[$n][type] = $columns[$n][0];
	$this->header_column[$n][title] = $columns[$n][1];
	$this->header_column[$n][ord] = $columns[$n][3];
	if($columns[$n][4]!="" && $columns[$n][4]!="left")
		$this->header_column[$n][align] = " align=".$columns[$n][4];

	if($columns[$n][0]=="ord")
		$this->orders[abs($columns[$n][3])] = $columns[$n][2];

	if($columns[$n][5]!="" && $columns[$n][5]!="left")
		$this->aligns[$n] = " align=".$columns[$n][5];
	}
}


function draw_header(){

	$HTML .= "<table id=tbl cellspacing=0 cellpadding=3 border=0 width={$this->tableWidthStr}><tr>";
	reset($this->header_column);
	while( list(, $col) = each($this->header_column) )
	{
		if($col[type]=="ord")
		{
			if(abs($col[ord])==abs($this->ord))
			{
				$HTML .= "<td id=\"sel_title\"$col[align]><a href=\"$this->href?ord=".(-1*$this->ord)."\">$col[title]</a></td>";
			} else {
				$HTML .= "<td id=\"nor_title\"$col[align]><a href=\"$this->href?ord=$col[ord]\">$col[title]</a></td>";
			}
		} else if($col[type]=="hli") {
			$HTML .= "<td id=\"hl_title\"$col[align]>$col[title]</td>";
		} else {
			$HTML .= "<td id=\"nor_title\"$col[align]>$col[title]</td>";
		}
	}

	$HTML .= "</tr>\n<tr><td bgcolor=#FFFFFF colspan=".$this->number_of_columns." height=6></td></tr>\n";
	return $HTML;
}

function draw_body(){
	
	$RecordsColors = Array('trCol1','trCol2');
	$RecordsColorsCount = count($RecordsColors);
	$RecordHighlightClass = "trHighlight";
	$dpf = $this->data_processing_function;

	$row_num = 0;
	$res =& $this->Query->getAll($this->data_query, array(), DB_FETCHMODE_ASSOC);

	foreach ($res as $row) {
		if($dpf) $dpf($row);
		$trClass = $RecordsColors[$row_num++ % $RecordsColorsCount];
		$HTML = <<<EOD
<tr class="$trClass" onmouseover="this.className='$RecordHighlightClass'" onmouseout="this.className='$trClass'">

EOD;

		$col = 0;
		foreach($row as $col_value) {
			$HTML .=  "\t<td".$this->aligns[$col++].">$col_value</td>\n";
		}
		$HTML .= "</tr>\n";
		echo $HTML;
	}
	echo "</table>";
}

function draw(){
	echo $this->page_bar_HTML;
	if($this->page_bar_HTML) echo "<br>";
	echo $this->draw_header();
	$this->draw_body();
	if($this->page_bar_HTML) echo "<br>";
	echo $this->page_bar_HTML;
}

}

// ----------------------- FE Table View Class Starts HERE -----------------------

class FETable_view{
	var $table_title;
	var $table_view_name;
	var $data_processing_function;
	var $number_of_columns;
	var $number_of_rows;
	// var $header_column;
	var $href;
	var $ord;
	
	var $tableWidthStr = '520';
	var $pageBarWidth = '520';

function FETable_view(&$columns, $default_order_field, $records_per_page, $table_view_name, $data_processing_function, $count_query, $data_query, &$DB_Connection){

	$this->Query =& $DB_Connection;
	$this->number_of_columns = count($columns);
	$this->add_columns($columns);
//echo "<pre>"	
//print_r($_SERVER);
	//$this->href = basename($_SERVER['SCRIPT_NAME']);
	$this->href = $_SERVER['SCRIPT_URL'];
	$this->table_view_name = $table_view_name;
	$this->data_processing_function = $data_processing_function;
	$this->records_per_page = $records_per_page;

	$ORDER = $this->build_order_query_part($default_order_field);
	$LIMIT = $this->build_limit_query_part($count_query);
	$this->data_query = $data_query.$ORDER.$LIMIT;
}

function build_order_query_part($default_order_field){

	if($_GET[ord])
		$_SESSION[$this->table_view_name][ord] = $_GET[ord];

	$this->ord = $_SESSION[$this->table_view_name][ord];
	if(!$this->orders[abs($this->ord)])
	{
		$this->ord = $default_order_field;
		$_SESSION[$this->table_view_name][ord] = $this->ord;
	}
	if(!$this->orders[abs($this->ord)]) return "";
	$ORDER = " ORDER BY ".$this->orders[abs($this->ord)].($this->ord>0 ? " ASC" : " DESC");
	return $ORDER;
}

function build_limit_query_part($count_query){

if($_GET[page])
	$_SESSION[$this->table_view_name][page] = $_GET[page];

if($_SESSION[$this->table_view_name][page] < 1)
	$_SESSION[$this->table_view_name][page] = 1;
$page = $_SESSION[$this->table_view_name][page];

	$this->number_of_rows = $this->Query->getOne($count_query);
	$this->number_of_pages = ceil($this->number_of_rows/$this->records_per_page);
	if($this->number_of_pages>1)
	{
		if($page>$this->number_of_pages) $page = $this->number_of_pages;

		$this->page_bar_HTML .= "<table class=pageBar cellspacing=0 cellpadding=3 border=0 width={$this->pageBarWidth}><tr>\n";
		$this->page_bar_HTML .= "<td align=center>\n";
		$this->page_bar_HTML .= CLib::draw_page_bar($this->number_of_pages, $page, $this->href."?ord={$_GET['ord']}&keywords={$_GET['keywords']}&page=", 20);

		$this->page_bar_HTML .= "</td></tr></table>\n";

		$starting_record = ($page-1) * $this->records_per_page;
		$LIMIT = " LIMIT $starting_record, $this->records_per_page";
	} else {
		$LIMIT = "";
	}
	return $LIMIT;
}

function set_query($SQL){
	$this->build_limit_query_part();
	$this->main_query = $SQL.$ORDER.$this->LIMIT;
}


//function add_column($type, $title, $order_field, $ord, $header_align, $rows_align){

function add_columns(&$columns){
	for($n = 0; $n<$this->number_of_columns; $n++)
	{
	$this->header_column[$n][type] = $columns[$n][0];
	$this->header_column[$n][title] = $columns[$n][1];
	$this->header_column[$n][ord] = $columns[$n][3];
	if($columns[$n][4]!="" && $columns[$n][4]!="left")
		$this->header_column[$n][align] = " align=".$columns[$n][4];

	if($columns[$n][0]=="ord")
		$this->orders[abs($columns[$n][3])] = $columns[$n][2];

	if($columns[$n][5]!="" && $columns[$n][5]!="left")
		$this->aligns[$n] = " align=".$columns[$n][5];
	}
}


function draw_header(){

	$HTML .= "<table class=ViewTable cellspacing=0 cellpadding=3 border=0 width={$this->tableWidthStr}><tr>";
	reset($this->header_column);
	while( list(, $col) = each($this->header_column) )
	{
		if($col[type]=="ord")
		{
			if(abs($col[ord])==abs($this->ord))
			{
				$HTML .= "<td class=\"sel_title\"$col[align]><a href=\"$this->href?keywords={$_GET['keywords']}&page={$_GET['page']}&ord=".(-1*$this->ord)."\">$col[title]</a></td>";
			} else {
				$HTML .= "<td class=\"nor_title\"$col[align]><a href=\"$this->href?keywords={$_GET['keywords']}&page={$_GET['page']}&ord=$col[ord]\">$col[title]</a></td>";
			}
		} else if($col[type]=="hli") {
			$HTML .= "<td class=\"hl_title\"$col[align]>$col[title]</td>";
		} else {
			$HTML .= "<td class=\"nor_title\"$col[align]>$col[title]</td>";
		}
	}

	$HTML .= "</tr>\n<tr><td bgcolor=#FFFFFF colspan=".$this->number_of_columns." height=6></td></tr>\n";
	return $HTML;
}

function draw_body(){
	$RecordsColors = Array("#F8F8F8","#EEEEEE");
	$RecordsColorsCount = count($RecordsColors);
	$RecordHighlightColor = "#DBEE94";

	$dpf = $this->data_processing_function;

	$row_num = 0;
	$res =& $this->Query->getAll($this->data_query, array(), DB_FETCHMODE_ASSOC);

	foreach ($res as $row) {
		if($dpf) $dpf($row);
		$bgcolor = $RecordsColors[$row_num++ % $RecordsColorsCount];
		$HTML = <<<EOD
<tr bgcolor="$bgcolor" onmouseover="this.style.backgroundColor='$RecordHighlightColor'" onmouseout="this.style.backgroundColor='$bgcolor'">

EOD;

		$col = 0;
		foreach($row as $col_value) {
			$HTML .=  "\t<td".$this->aligns[$col++].">$col_value</td>\n";
		}
		$HTML .= "</tr>\n";
		echo $HTML;
	}
	echo "</table>";
}

function draw(){
	echo $this->page_bar_HTML;
	//if($this->page_bar_HTML) echo "<br>";
	echo $this->draw_header();
	$this->draw_body();
	//if($this->page_bar_HTML) echo "<br>";
	echo $this->page_bar_HTML;
}

}


// ------------------------ CLASS Checkbox_ctrl START ---------------------

class Checkbox_ctrl{
	var $name;
	var $data;
	var $selected;

function Checkbox_ctrl($name, $options, $selected, $no_key=FALSE)
{
	$this->data = Array();
	$this->selected = Array();

	if(!is_array($options)) return;
	reset($options);
	while( list($key, $val) = each($options) )
	{
		if($no_key) $key = $val;	//// Beshe $key
		if( is_array($selected) && in_array("$key", $selected) )
		{
			$selectedStr = $key;
		} else if ("$key"=="$selected") {
			$selectedStr = $key;
		} else {
			$selectedStr = "";
		}

		$this->data[$key] = $val;
		$this->selected[$key] = $selectedStr;
		$this->name = $name;
	}
}

function draw_one_check($key)
{
	$name = $this->name;
	$val = $this->data[$key];
	if($this->selected[$key]) $checked = " CHECKED";
	$for_tmp = $this->name."['$key']";
	return "<input id=\"$for_tmp\" type=\"checkbox\" name=\"$for_tmp\" value=\"$key\"$checked>";
}

function draw_one_label($key, $text)
{
	$for_tmp = $this->name."['$key']";
	return "<label for=\"$for_tmp\">$text</label>";
}

function draw_one($key, $label=true, $delimiter="&nbsp;", $how="left")
{
	if($label)
		$text = $this->draw_one_label($key, $this->data[$key]);
	else
		$text = $this->data[$key];

	if($how=="left")
	{
		$ret = $this->draw_one_check($key).$delimiter.$text;
	}
	else
	{
		$ret = $text.$delimiter.$this->draw_one_check($key);
	}
	return $ret;
}

function draw($label=true, $cols=1, $how="left", $params="border=0")
{
	if($how=="left")
	{
		$align1 = "right";
		$align2 = "left";
	}
	else
	{
		$align1 = "left";
		$align2 = "right";
	}

	$ret = "<table $params>\n";
	reset($this->data);
	$tmp1 = 0;

	while(list($key, $val) = each($this->data))
	{
		if($tmp1%$cols==0)
			$ret.= "<tr>\n";

		$ret.= "\t<td align=\"$align1\">";
		$ret.= $this->draw_one($key, $label, "</td><td align=\"$align2\">", $how);
		$ret.= "</td>\n";

		if($tmp1%$cols>=($cols-1))
			$ret.= "</tr>\n";

		$tmp1++;
	}
	$ret.= "</table>";
	return $ret;
}
}
// ------------------------ CLASS Checkbox_ctrl END ---------------------


/*function format_errors_message(&$messages){
	if( count($messages)<=0 ) return;
	$HTML = "The request have not been accepted for the following reason(s):<br>\n";
	reset($messages);
	while( list($key, $val) = each($messages) )
	{
		$HTML .= "- $val <a href='#' onClick=\"return focus_control('$key');\">correct</a><br>\n";
	}
	return $HTML."<br>\n";
}*/

/*function format_warrnings_message(&$messages){
	if( count($messages)<=0 ) return;
	$HTML = "Warrnings for the following reason(s):<br>\n";
	reset($messages);
	while( list($key, $val) = each($messages) )
	{
		$HTML .= "- $val<br>\n";
	}
	return $HTML."<br>\n";
}
*/
// --- START File processing routines


$BASE_URL_PATH_FOR_UPLOADING = "/";
$BASE_FILE_PATH_FOR_UPLOADING = dirname(__FILE__) . '/../';

function process_attachment($table, $at_name, $key_name, $key_val)
{
	$del_name = $table."_".$at_name."_delete";
	$input_name = $table."_".$at_name;

	if($key_val>0)
	{
		$SQL = "SELECT $at_name FROM $table WHERE $key_name='$key_val'";
		$res = my_query($SQL);
		$at_value = my_fetch($res);
		$at_value = $at_value[$at_name];
		$file_path = get_upload_path("FILE_PATH", $table, $key_val, $at_value);

		if($_POST[$del_name]!="" && $at_value!="")
		{
			@unlink($file_path);
			$SQL = "UPDATE $table SET $at_name='' WHERE $key_name='$key_val'";
			my_query($SQL);
			$at_value = "";
		}
		if( is_uploaded_file($_FILES[$input_name]['tmp_name']) )
		{
			if($at_value!="")
				@unlink($file_path);

			$at_value = $_FILES[$input_name]['name'];
			$file_path = get_upload_path("FILE_PATH", $table, $key_val, $at_value);

			move_uploaded_file($_FILES[$input_name]['tmp_name'], $file_path);
			$SQL = "UPDATE $table SET $at_name='$at_value' WHERE $key_name='$key_val'";
			my_query($SQL);
		}
		$url_path = get_upload_path("URL_PATH", $table, $key_val, $at_value);
	}
	//$table, $at_name, $key_name, $key_val
	return "<input type=file name=\"$input_name\">&nbsp;".($at_value?("Current: <a href=\"$url_path\">".$at_value."</a>&nbsp;<input type=submit name=\"".$del_name."\" value=\"Delete\" onClick=\"return confirm('Are You sure?')\">"):"No File Uploaded");
}

function process_attachment_nodel($table, $at_name, $key_name, $key_val)
{
	$input_name = $table."_".$at_name;

	if($key_val>0)
	{
		$SQL = "SELECT $at_name FROM $table WHERE $key_name='$key_val'";
		$res = my_query($SQL);
		$at_value = my_fetch($res);
		$at_value = $at_value[$at_name];
		$file_path = get_upload_path("FILE_PATH", $table, $key_val, $at_value);

		if( is_uploaded_file($_FILES[$input_name]['tmp_name']) )
		{
			if($at_value!="")
				@unlink($file_path);

			$at_value = $_FILES[$input_name]['name'];
			$file_path = get_upload_path("FILE_PATH", $table, $key_val, $at_value);

			move_uploaded_file($_FILES[$input_name]['tmp_name'], $file_path);
			$SQL = "UPDATE $table SET $at_name='$at_value' WHERE $key_name='$key_val'";
			my_query($SQL);
		}
		$url_path = get_upload_path("URL_PATH", $table, $key_val, $at_value);
	}
	//$table, $at_name, $key_name, $key_val
	return "<input type=file name=\"$input_name\">&nbsp;".($at_value?("Current: <a href=\"$url_path\">".$at_value."</a>"):"No File Uploaded");
}


function get_upload_path($path_type, $path_context, $id, $ext="")
{
	global $BASE_URL_PATH_FOR_UPLOADING, $BASE_FILE_PATH_FOR_UPLOADING;

	if($path_type == "URL_PATH")
		$precess = $BASE_URL_PATH_FOR_UPLOADING;
	else
		$precess = $BASE_FILE_PATH_FOR_UPLOADING;

	switch($path_context)
	{
		case "items": $context = "i/assortment/";break;

	}

	if($ext) $ext = "_".$ext;
	return $precess.$context.$id.$ext;
}

// --- END File processing routines


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


class CTable {
	var $cells; // 2D Array

	function render($cells){
		$TABLE = "<TABLE border=1>";
		for($row=0; $row<count($cells); $row++){
			$TABLE .= "<TR>\n<TD>".implode("</TD>\n<TD>", $cells[$row])."</TD>\n</TR>\n";
		}
		$TABLE .= "</TABLE>";

		return $TABLE;
	}
}

class CRepeater
{
	var $RepeatDirection; // Vertical | Horizontal
	//var $RepeatRows;
	var $RepeatColumns;
	var $RepeatLayout; // Flow | Table
	var $Items; // Array of Items;

	function CRepeater(){

	}

	function add_item(){

	}

	function render(){

	}
} // Class CRepeater ends

class CDateTimeInput extends CTextInput {
	var $d;
	
	function CDateTimeInput($name, $label_text, $properties, $required){
		$this->d = new MyFormatedDate("");
		parent::CTextInput($name, $label_text, $properties, $required, '');
	}
	
	function getDataForDb() {
		return $this->d->date;
	}
	
	function loadData(&$array) {
		if (array_key_exists($this->control_name, $array)) {
			$value = $array[$this->control_name];
			$this->setValue($value);
		} else {
			$this->value = '';
		}	
	}
	
	function setValue($value){
		$this->d->setDateTime($value);
		$this->value = ($this->d->date==-1) ? $value : $this->d->formatedDateTime();
	}
	
	function validate(&$errors_array) {
		if (!$this->required && $this->value=='') return true;

		if($this->d->date==-1)
		{
			$errors_array[$this->control_name]=$this->getErrorMessage();
			$this->label_text = '<font color=red>'.$this->label_text.'</font>';
			return false;
		}
	}
}

class CDateInput extends CDateTimeInput{
	function setValue($value){
		$this->d->setDateTime($value);
		$this->value = ($this->d->date==-1) ? $value : $this->d->formatedDate();
	}
}

?>