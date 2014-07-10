<?php

class ControlInputHidden extends BaseControl {

	function getTemplateValue(){
		return htmlspecialchars($this->Value);
	}
	
	function Render(){
		$value = $this->getTemplateValue();
		return <<<EOD
<input type="hidden" id="{$this->FormElementId}" name="{$this->FormElementName}" value="{$value}" />
EOD;
	}


	function BuildTemplate($prefix, $RenderValues=false){

		if($RenderValues) return $this->Render();
		
		return <<<EOD
<input type="hidden" id="<?={$prefix}->FormElementId;?>" name="{$prefix}->FormElementName" value="<?={$prefix}->getTemplateValue();?>" />
EOD;
	}
}

class ControlTextInput extends BaseControl {


	public $Trim = true;
	public $MaxLength=255;
	
	public $Format;


	function setPostValue($v){
		$v = $this->Trim ? trim($v) : $v;
		parent::setPostValue($v);
	}

	function getTemplateValue(){
		$v = $this->Value;

		if($this->isValid && !$this->isEmpty && $this->Format) {
			$v = $this->Format($this->Format, $v);
		}
		return htmlspecialchars($v);
	}


	function Format($format, $value){
		return sprintf($format, $value);
	}


	function BuildTemplate($prefix, $RenderValues=false){

		$Value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$MaxLength = $RenderValues ? $this->MaxLength : "<?={$prefix}->MaxLength;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">{$Value}</div>
EOD;
		} else {
			return <<<EOD
<input type="text" id="{$FormElementId}" name="{$FormElementName}" value="{$Value}" maxlength="{$$MaxLength}" {$Attributes} />
EOD;
		}
	}

}

class ControlTextArea extends BaseControl {
	public $Trim = true;


	function setPostValue($v){
		$v = $this->Trim ? trim($v) : $v;
		parent::setPostValue($v);
	}

	function getTemplateValue(){
		$v = $this->Value;
		return htmlspecialchars($v);
	}


	function BuildTemplate($prefix, $RenderValues=false){

		$Value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">{$value}</div>
EOD;
		} else {
			return <<<EOD
<textarea id="{$FormElementId}" name="{$FormElementName}" {$Attributes}>{$Value}</textarea>
EOD;
		}
	}
}
/*
class ControlRangeInput extends BaseControl {

	function getDefaultControlJSID(){
		return $this->FormElementId.'_from';
	}
	
	function getTemplateValue($key){
		$v = $this->Value[$key];
		return htmlspecialchars($v);
	}
	
	function BuildTemplate($prefix, $RenderValues=false){

		$Value_from = $RenderValues ? $this->getTemplateValue('from') : "<?={$prefix}->getTemplateValue('from');?>";
		$Value_to   = $RenderValues ? $this->getTemplateValue('to') : "<?={$prefix}->getTemplateValue('to');?>";
		
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">from {$Value_from} to {$Value_to}</div>
EOD;
		}
		
			return <<<EOD
from <input type="text" id="{$FormElementId}_from" name="{$FormElementName}[from]" value="{$Value_from}" {$Attributes} size="5" />
to <input type="text" id="{$FormElementId}_to" name="{$FormElementName}[to]" value="{$Value_to}" {$Attributes} size="5" />
EOD;

	}

}
*/

class ControlRangeInput extends BaseCompositeControl  {

	//public $RenderStyle='inline';

	function BuildChildControls(){
		$this->addControl(new ControlTextInput('from','from'));
		$this->addControl(new ControlTextInput('to','to'));
		
		$this->Controls['from']->Attributes = 'size="5"';
		$this->Controls['to']->Attributes = 'size="5"';
	}

/*
	function getDefaultControlJSID(){
		return $this->FormElementId.self::ID_SEPARATOR.'from';
	}



	function buildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this'){
		$from = $ExpandControls ? $this->Controls['from']->BuildTemplate($prefix."->Controls['from']", $RenderValues) : "<?={$prefix}->Controls['from']->Render();?>";
		$to = $ExpandControls ? $this->Controls['to']->BuildTemplate($prefix."->Controls['to']", $RenderValues) : "<?={$prefix}->Controls['to']->Render();?>";
		
		return <<<EOD
from $from<br />to $to
EOD;
	}
	*/

}


class ControlButton extends BaseControl {
	
	public $RenderAsLink=false;

	function BuildTemplate($prefix, $RenderValues=false){
		if($this->ReadOnly) {
			return '';
		}
		
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		$Label = $RenderValues ? $this->Label : "<?={$prefix}->Label;?>";
		$MainViewId = $RenderValues ? $this->MainView->FormElementId : "<?={$prefix}->MainView->FormElementId;?>";
		
		
		if($this->RenderAsLink){
			$HTML = <<<EOD
<a href="JavaScript:ITTI.doActionPost('{$MainViewId}','{$FormElementName}','1');" id="{$FormElementId}"  {$Attributes}>{$Label}</a>
EOD;
		} else {
			$HTML = <<<EOD
<input type="button" onclick="JavaScript:ITTI.doActionPost('{$MainViewId}','{$FormElementName}','1');" id="{$FormElementId}" name="{$FormElementName}" value="{$Label}" {$Attributes} />
EOD;
		}
		return $HTML;
	}
}

class ControlSubmitButton extends BaseControl {

	function BuildTemplate($prefix, $RenderValues=false){
		if($this->ReadOnly) {
			return '';
		}
		
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		$Label = $RenderValues ? $this->Label : "<?={$prefix}->Label;?>";


			$HTML = <<<EOD
<input type="submit" id="{$FormElementId}" name="{$FormElementName}" value="{$Label}" {$Attributes} />
EOD;
		return $HTML;
	}
}

class ControlDateInput extends ControlTextInput {
	
	function __construct($Id, $Label='', $Attributes=''){
		parent::__construct($Id, $Label, $Attributes);
		$this->Format = DATE_FORMAT;
	}

	function getValue(){
		if(empty($this->Value)) return '';
		
		$t = strtotime($this->Value);
		if($t===false) return $this->Value;
		
		return date("Y-m-d H:i:s", $t);
	}
	
	function setValue($v){
		
		$this->isEmpty = FEAttributeDate::isEmpty($v);
		if($this->isEmpty) $v = '';
		parent::setValue($v);
	}

	function Format($format, $value){
		return strftime($format, strtotime($value));
	}

	function convFormatStr($str){
		$translation = array(
			'%Y'=>'yy',
			'%m'=>'mm',
			'%d'=>'dd',
		);
		return str_replace(array_keys($translation), $translation, $str);
	}
	
	function PreRender(){
		$this->View->scripts['jQDate'] = <<<EOD
 $(document).ready(function(){ 
$.datepicker.setDefaults({
   showOn: 'both',
   showAnim: false,
   buttonImageOnly: true,
   buttonImage: 'calendar.gif',
   buttonText: 'Calendar',
   changeYear:true,
   dateFormat: '{$this->convFormatStr($this->Format)}',
   firstDay: 1,
   buttonImage: '/be/i/design/calendar.png'
});
  });
EOD;

		$this->View->scripts[] = <<<EOD
 $(document).ready(function(){
    $("#{$this->FormElementId}").datepicker();
 });
EOD;
	}
}

class ControlDateTimeInput extends ControlDateInput {

	function __construct($Id, $Label='', $Attributes=''){
		parent::__construct($Id, $Label, $Attributes);
		$this->Format = DATE_FORMAT.' '.TIME_FORMAT;
	}

	
	function PreRender(){
		$this->View->css['calendar-win2k-cold-1.css'] = '/js/Calendar/calendar-win2k-cold-1.css';
		$this->View->js['calendar.js'] = '/js/Calendar/calendar.js';
		$this->View->js['calendar-setup.js'] = '/js/Calendar/calendar-setup.js';
		$this->View->js['calendar-en.js'] = '/js/Calendar/lang/calendar-en.js';

	}
	
	function BuildTemplate($prefix, $RenderValues=false){

		$Value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		$Format = $RenderValues ? $this->Format : "<?={$prefix}->Format;?>";
		
		$value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$this->FormElementId}">{$value}</div>
EOD;
		} else {
			return <<<EOD
<input type="text" id="{$FormElementId}" name="{$FormElementName}" value="{$Value}" {$Attributes} />
<input type="button" value="" class="button_calendar" id="{$FormElementId}_btn" />
<script type="text/javascript">
Calendar.setup({inputField     :    '{$FormElementId}',          // id of the input field
          ifFormat       :    '{$Format}',                          // format of the input field
          showsTime      :    true,                          // will display a time selector
          button         :    '{$FormElementId}_btn', // trigger for the calendar (button ID)
          singleClick    :    true,                                // double-click mode
          step           :    1                                    // show all years in drop-down boxes (instead of every other year as default)
        });
</script>
EOD;
		}
	}
	
}
/*
class ControlDateInput2 extends BaseControl {


	function getValue(){
		$v = $this->Value;
		if(empty($v)) return '';
		return sprintf("%04d-%02d-%02d",$v['y'], $v['m'], $v['d']);
	}


	function setValue($v){
		$tmp = $v;
		$v = array();
		$v['y'] = substr($tmp, 0,4);
		$v['m'] = substr($tmp, 5,2);
		$v['d'] = substr($tmp, 8,2);

		$this->Value = $v;

	}

	function getTemplateValue($key){
		return $this->Value[$key];
	}

	function BuildTemplate($prefix, $RenderValues=false){
		if($RenderValues){
			$d = $this->Value['d'];
			$m = $this->Value['m'];
			$y = $this->Value['y'];
		} else {
			$d = "<?={$prefix}->getTemplateValue('d');?>";
			$m = "<?={$prefix}->getTemplateValue('m');?>";
			$y = "<?={$prefix}->getTemplateValue('y');?>";
		}

		if($this->ReadOnly) {
			return <<<EOD
<div id="{$this->FormElementId}">{$d}-{$m}-{$y}</div>
EOD;
		} else {
			 $sep = self::ID_SEPARATOR;
			return <<<EOD
<input type="text" id="{$this->FormElementId}{$sep}d" name="{$this->FormElementName}[d]" value="{$d}" maxlength="2" size="2" />
<input type="text" id="{$this->FormElementId}{$sep}m" name="{$this->FormElementName}[m]" value="{$m}" maxlength="2" size="2" />
<input type="text" id="{$this->FormElementId}{$sep}y" name="{$this->FormElementName}[y]" value="{$y}" maxlength="4" size="4" />
EOD;
		}
	}

	function getDefaultControlJSID(){
		return $this->FormElementId.self::ID_SEPARATOR.'d';
	}


}
*/


class ControlCheckBox extends BaseControl {

	function getTemplateValue(){
		if($this->ReadOnly){
			return $this->Value ? '[x]' : '[ ]';
		} else {
			return $this->Value ? 'checked="checked"' : '';
		}

	}

	function BuildTemplate($prefix, $RenderValues=false){
		$checked = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		$Value = $RenderValues ? (int)$this->Value : "<?=(int){$prefix}->Value;?>";
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$this->FormElementId}">{$checked}</div>
EOD;
		} else {
			return <<<EOD
<input type="checkbox" id="{$FormElementId}" value="1" {$checked} {$Attributes} onClick="document.getElementById('{$FormElementId}_h').value = this.checked ? this.value : ''" />
<input type="hidden" id="{$FormElementId}_h" name="{$FormElementName}" value="{$Value}" />
EOD;
		}
	}

}

class ControlSelect extends BaseControl {

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


	function getTemplateValue(){
		if($this->ReadOnly){
			return $this->Options[$this->Value];
		} else {
			return self::renderOptions($this->Value, $this->Options);
		}

	}

	function BuildTemplate($prefix, $RenderValues=false){
		
		$Value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">{$Value}</div>
EOD;
		} else {
			return <<<EOD
<select id="{$FormElementId}" name="{$FormElementName}" {$Attributes}>
$Value
</select>
EOD;
		}
	}
}



class ControlMultipleSelect extends BaseControl {
	
	function getValue(){

		if(empty($this->Value)) return '';
		return implode(',',$this->Value);
	}
	
	function setValue($Value){
		$v = $Value===''?array():explode(',',$Value);
		parent::setValue($v);
	}

	function getReadOnlyValue(){
		$res = '';
		$val = $this->Value;
		if(is_array($val))
		foreach ($val as $v){
			$v = $this->Options[$v];
			$res .= <<<EOD
<div>$v</div>
EOD;
		}
		return $res;
	}


	function getTemplateValue(){
		if($this->ReadOnly){
			return $this->getReadOnlyValue();
		} else {
			return ControlSelect::renderOptions($this->Value, $this->Options);
		}

	}

	function BuildTemplate($prefix, $RenderValues=false){
		
		$Value = $RenderValues ? $this->getTemplateValue() : "<?={$prefix}->getTemplateValue();?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		$FormElementName = $RenderValues ? $this->FormElementName : "<?={$prefix}->FormElementName;?>";
		$Attributes = $RenderValues ? $this->Attributes : "<?={$prefix}->Attributes;?>";
		
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">{$Value}</div>
EOD;
		} else {
			return <<<EOD
<select id="{$FormElementId}" name="{$FormElementName}[]"  multiple="multiple" {$Attributes}>
$Value
</select>
EOD;
		}
	}

}

class ControlDoubleSelect extends BaseControl {
	function getValue(){

		if(empty($this->Value)) return '';
		return is_array($this->Value)?implode(',',$this->Value):$this->Value;
	}
	
	function getReadOnlyValue(){
		$res = '';
		$val = $this->Value;
		if(is_array($val))
		foreach ($val as $v){
			$v = $this->Options[$v];
			$res .= <<<EOD
<span>$v</span>
EOD;
		}
		return $res;
	}
	
	function getDefaultControlJSID(){
		return 'li1_'.$this->FormElementId;;
	}
	
	function Render(){
		$values=$this->Options;
		$values1=array();
		
		if(!empty($this->Value)) {
			$sel=is_array($this->Value)?$this->Value:explode(',',$this->Value);			
			foreach ($sel as $k=>$v) {
				if(isset($this->Options[$v])) {
					$values1[$v]=$this->Options[$v];
					unset($values[$v]);
				}
			}
		}
		$str="<table>
				<tr>";
		$c=max(count($values),count($values1));
		if($c>40) {
			$c=40;
		}
		if($c<5) {
			$c=5;
		}
		$str.="
		<td valign=\"top\">
			<select {$this->Attributes} size='{$c}' ondblclick='ITTI.DoubleSelect._add_list(&quot;{$this->FormElementId}&quot;);' id='li2_{$this->FormElementId}'>".ControlSelect::renderOptions(null,$values)."</select>
		</td>
		<td valign=\"middle\" style=\"width:20px;\">
			<input style=\"width:20px;text-align:center;\" type=\"button\" onclick=\"ITTI.DoubleSelect._remove_list('{$this->FormElementId}');\" value=\"&#171;\" />
			<br />
			<input style=\"width:20px;text-align:center;\" type=\"button\" onclick=\"ITTI.DoubleSelect._add_list('{$this->FormElementId}');\" value=\"&#187;\" />

		</td>";
			
			$str.="<td valign=\"top\">
					<select {$this->Attributes} id='li1_{$this->FormElementId}' ondblclick='ITTI.DoubleSelect._remove_list(&quot;{$this->FormElementId}&quot;);' size='{$c}'>".(empty($values1)?"":ControlSelect::renderOptions(null,$values1))."</select>
					
				</td>
";
			$str.=<<<EOD
			<td valign="middle" style="width:10px;">
			<input type="button" onclick="ITTI.DoubleSelect._move_up('{$this->FormElementId}');" style="width:10px;" value="&#8593;" /><br />
			<input type="button" onclick="ITTI.DoubleSelect._move_down('{$this->FormElementId}');" style="width:10px;" value="&#8595;" />
			</td>
EOD;
		$str.="	</tr>
		</table>
		";
		$this->attributes['name']=$name;
		$this->attributes['id']=$this->control_id;
		$this->attributes['ondblclick']=$dbl_clk;
		
		
		
		$str.="<input type=\"hidden\" name=\"{$this->FormElementName}\" id=\"{$this->FormElementId}\" value=\"".$this->getValue()."\" />";
		return $str;
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>";
	}
}

class ControlCheckBoxGroup extends BaseControl {
	function getValue(){

		if(empty($this->Value)) return '';
		return implode(',',$this->Value);
	}
	
	function setValue($Value){
		$v = $Value===''?array():explode(',',$Value);
		parent::setValue($v);
	}
	
	function getReadOnlyValue(){
		$res = '';
		$val = $this->Value;
		if(is_array($val))
		foreach ($val as $v){
			$v = $this->Options[$v];
			$res .= <<<EOD
<span>$v</span>
EOD;
		}
		return $res;
	}
	
	function getTemplateValue(){
		if($this->ReadOnly){
			return $this->getReadOnlyValue();
		} else {
			//return ControlSelect::renderOptions($this->Value, $this->Options);
		}

	}
	
	function Render(){
		$value = $this->Value;
		if(!is_array($value)) $value = array();
		if($this->ReadOnly) {
			return <<<EOD
<div id="{$this->FormElementId}">{$this->getReadOnlyValue()}</div>
EOD;
		} else {
			$str=array();
			foreach ($this->Options as $k=>$v) {
				$vv=htmlspecialchars($k);
				$selected = in_array($k, $value) ? 'checked="checked"' : '';
				$str[]=<<<EOD
	<input type="checkbox" id="{$this->FormElementId}_{$k}" name="{$this->FormElementName}[]" value="{$vv}" $selected />
	<label for="{$this->FormElementId}_{$k}">{$v}</label><br />
EOD;
			}
			
			return "<div id='{$this->FormElementId}' {$this->Attributes}>".implode("<br />",$str).'</div>';
		}
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>";
	}
}

class ControlTinyMCE extends ControlTextArea {
	
	function PreRender(){
		$this->View->js['tiny_mce_itti.js'] = '/be/tiny_mce/tiny_mce_itti.js';
		$this->View->js['tiny_mce.js'] = '/be/tiny_mce/tiny_mce.js';
		
/*		
		$this->View->scripts[] = <<<EOD
		

TinyMCEConfigTpl['elements'] = '{$this->FormElementId}';
tinyMCE.init(TinyMCEConfigTpl);

EOD;*/
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";
		
		$res = parent::BuildTemplate($prefix, $RenderValues);
		$res .= <<<EOD
<script>
TinyMCEConfigTpl['elements'] = '{$FormElementId}';
tinyMCE.init(TinyMCEConfigTpl);
</script>
EOD;
		return $res;
	}
}

/*
class ControlAutoComplete extends BaseView {
	public $RenderStyle='inline';
	public $Url="";
	
	function BuildChildControls(){
		
		
		$hidden=new ControlInputHidden("hidden");
		$this->addControl($hidden);
		
		$txt=new ControlTextInput('txt',$this->Label,"autocomplete='off' onkeyup=\"loadAjaxRequest(event,this,'".$hidden->FormElementId."','".htmlspecialchars($this->Url)."');\"  onkeydown=\"return processEnter(event,this,'".$hidden->FormElementId."');\" onblur=\"isMyDiv(event,this,'".$hidden->FormElementId."');\"");
		$this->addControl($txt);
		
	}
	
	function getValue() {
		$v =parent::getValue();
		//$v = $this->Value;
		if(empty($v)) return '';
		return $v['txt'];
	}
	
	function buildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$View'){
		$txt = $this->Controls['txt']->BuildTemplate($prefix."->Controls['txt']", $RenderValues);
		$hidden = $this->Controls['hidden']->BuildTemplate($prefix."->Controls['hidden']", $RenderValues);		
		return <<<EOD
		{$txt}{$hidden}
		<iframe disabled style="display:none;z-index:1;position:absolute;" id="{$this->Controls['txt']->FormElementId}_frm"></iframe>
<div id="{$this->Controls['txt']->FormElementId}_div" class="autocomplete_div"></div>
EOD;
	}
}
*/

/*
class ControlDateInput3 extends BaseView {

	public $RenderStyle='inline';

	function BuildChildControls(){
		$d = new ControlSelect('d','Day');
		$m = new ControlSelect('m','Month');
		$y = new ControlTextInput('y','Year','maxlength="4" size="4"');
		for ($i=1; $i<32; $i++) $d->Options[$i]=$i;
		for ($i=1; $i<13; $i++) $m->Options[$i]=$i;


		$this->addControl($d);
		$this->addControl($m);
		$this->addControl($y);
	}

	function getValue(){
		$v =parent::getValue();
		//$v = $this->Value;
		if(empty($v)) return '';
		return sprintf("%04d-%02d-%02d",$v['y'], $v['m'], $v['d']);
	}


	function setValue($v){
		$tmp = $v;
		$v = array();
		$v['y'] = substr($tmp, 0,4);
		$v['m'] = substr($tmp, 5,2);
		$v['d'] = substr($tmp, 8,2);


		parent::setValue($v);

	}

	function getDefaultControlJSID(){
		return $this->FormElementId.self::ID_SEPARATOR.'d';
	}



	function buildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$View'){
		$d = $this->Controls['d']->BuildTemplate($prefix."->Controls['d']", $RenderValues);
		$m = $this->Controls['m']->BuildTemplate($prefix."->Controls['m']", $RenderValues);
		$y = $this->Controls['y']->BuildTemplate($prefix."->Controls['y']", $RenderValues);
		return <<<EOD
$d $m $y
EOD;
	}

}
*/


class ControlDateRangeInput extends ControlRangeInput {

	function BuildChildControls(){
		$this->addControl(new ControlDateInput('from','from'));
		$this->addControl(new ControlDateInput('to','to'));
		$this->Controls['from']->Attributes = 'size="10"';
		$this->Controls['to']->Attributes = 'size="10"';
	}
}


class ControlDateTimeRangeInput extends ControlRangeInput {

	function BuildChildControls(){
		$this->addControl(new ControlDateTimeInput('from','from'));
		$this->addControl(new ControlDateTimeInput('to','to'));
	}

}

/*

Mnogo maka iadoh dokato hvana toia bug!!!!

Kogato kloniram View v koeto ima tazi kontrola, vsichko e ok, no cloniraneto minava avtomatichno Controls masiva, no ne i direktnite referencii kato $PreserveId i $PreserveTxt
I poneze v render gi polzvam tiah to se poluchava renderirane sas starite ID-ta predi kloniraneto i dobavianeto v novo View.

Izvod ili da polzvam controlite samo prez ->Controls ili da si pisha i __clone method za takiva implementacii

*/

class ControlIDRef extends BaseCompositeControl  {

	//public $RenderStyle='inline';
	public $LabelFunction='ControlIDRef::LabelFunc';
	public $SelectUrl='';
	
	public $RenderEmptyBtn=false;
	
	protected $Select;
	protected $Clear;
	protected $Empty;
	protected $PreserveId;
	protected $PreserveTxt;
	
	
	function __clone(){
		parent::__clone();
		$this->Select = $this->Controls['select'];
		$this->Clear = $this->Controls['clear'];
		$this->Empty = $this->Controls['Empty'];
		$this->PreserveId = $this->Controls['id'];
		$this->PreserveTxt = $this->Controls['Label'];
	}


	function getValue(){
		$v = parent::getValue();
		$v = $v['id'];
		if(is_array($v)) $v = implode(',',$v);
		return $v;
	}
	
	function setValue($v){
		if(is_array($v)) $v = implode(',',$v);
		$val = array();
		$val['id'] = $v;
		$val['Label'] = call_user_func_array($this->LabelFunction,array('id'=>$v));
		parent::setValue($val);
	}


	function BuildChildControls(){


		if($this->ReadOnly) return ;
		
		$this->Select = new ControlButton('select', Culture::Select);
		$this->Clear = new ControlButton('clear', Culture::Clear);
		$this->Empty = new ControlButton('Empty', Culture::EmptyStr);
		//$this->Select->RenderAsLink = true;
		
		$this->PreserveId = new ControlInputHidden('id');
		$this->PreserveTxt = new ControlInputHidden('Label');
		
		$this->addControl($this->Select);		
		$this->addControl($this->Clear);
		$this->addControl($this->Empty);
		
		$this->addControl($this->PreserveId);
		$this->addControl($this->PreserveTxt);


	}
	
	function setPostValue($v){
		$this->Value = $v;
		if(isset($v['select'])){
			$this->doAction();
		}
		if(isset($v['Empty'])){
			$this->Value = array('id'=>'0','Label'=>Culture::EmptyStr);
		}
		if(isset($v['clear'])){
			$this->Value = array();
		}
		parent::setPostValue($this->Value);
	}
	
	function doAction(){
		if(!CallManager::isReturn()){
			CallManager::goToUrl($this->SelectUrl, $this->Value['id'], $this->MainView->Label);
		} else {
			$this->Value['id'] = CallManager::$returnParams;
			$this->Value['Label'] = call_user_func_array($this->LabelFunction,array('id'=>$this->Value['id']));
			
		}
	}
	
	static function LabelFunc($id){
		 return  $id==='0' ? Culture::EmptyStr : $id;
	}
	/*
	function getDefaultControlJSID(){
		return $this->FormElementId.self::ID_SEPARATOR.'select';
	}
	*/


	function buildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this'){

		$Value = $RenderValues ? $this->Value['Label'] : "<?={$prefix}->Value['Label'];?>";
		$FormElementId = $RenderValues ? $this->FormElementId : "<?={$prefix}->FormElementId;?>";



		if($this->ReadOnly) {
			return <<<EOD
<div id="{$FormElementId}">{$$Value}</div>
EOD;
		} else {

			$id = $ExpandControls ? $this->PreserveId->BuildTemplate($prefix."->Controls['id']", $RenderValues) : "<?={$prefix}->Controls['id']->Render();?>";
			$txt = $ExpandControls ?  $this->PreserveTxt->BuildTemplate($prefix."->Controls['Label']", $RenderValues) : "<?={$prefix}->Controls['Label']->Render();?>";
			$btn = $ExpandControls ?  $this->Select->BuildTemplate($prefix."->Controls['select']", $RenderValues) : "<?={$prefix}->Controls['select']->Render();?>";
			$btnClear = $ExpandControls ?  $this->Clear->BuildTemplate($prefix."->Controls['clear']", $RenderValues) : "<?={$prefix}->Controls['clear']->Render();?>";
			if($this->RenderEmptyBtn){
				$btnEmpty = $ExpandControls ?  $this->Empty->BuildTemplate($prefix."->Controls['Empty']", $RenderValues) : "<?={$prefix}->Controls['Empty']->Render();?>";
			}
			return <<<EOD

			{$id}
			{$txt}
<div id="{$FormElementId}" {$this->Attributes}>{$Value}</div>
{$btn}
{$btnClear}
{$btnEmpty}
EOD;
		}
	}
}


class ControlManagedFile extends BaseControl {


	public $DoDelete = false;

	function getValue(){
		
		if(!empty($_FILES[$this->FormElementId]['tmp_name'])){
			return $_FILES[$this->FormElementId];
		}

		if($this->DoDelete) return array('delete'=>true);
		
		return null;
	}
	
	function setPostValue($v){
		if(isset($v['delete'])){
			$this->DoDelete = true;
		}
	}
	
	function PreRender(){
		if($this->DoDelete){
			$this->MainView->Actions['save']->doAction();
		}
	}
	
	
	function Render(){

		$value = $this->Value;

		$FormElementId = $this->FormElementId;
		$FormElementName = $this->FormElementName;
		if($this->ReadOnly) {
			if(!empty($value['name']))
			return <<<EOD
<div id="{$FormElementId}"><a href="{$value['url']}" target="_blank">{$value['name']}</a></div>
EOD;
		} else {
			
			$MaxFileSize = ini_get('upload_max_filesize');
			
			
			if(empty($value['name'])){
			return <<<EOD
<input type="file" id="{$FormElementId}" name="{$FormElementId}"  {$this->Attributes} />  Max File Size: {$MaxFileSize}
EOD;
			}
			$delLabel = Culture::Delete;
			$nameVal = htmlspecialchars($value['name']);
			return <<<EOD
<input type="file" id="{$this->FormElementId}" name="{$FormElementId}"  {$this->Attributes} />  Max File Size: {$MaxFileSize}

<div id="{$FormElementId}"><a href="{$value['url']}" target="_blank">{$nameVal}</a> ({$value['size']})
<input type="submit" name="{$FormElementName}[delete]" value="{$delLabel}" />
</div>
EOD;
		}
	}

	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>";
	}

}

class ControlManagedImage extends BaseControl  {
	
	public $DoDelete = false;
	public $Sizes=array();
	

	function getValue(){
		
		if(!empty($_FILES[$this->FormElementId]['tmp_name'])){
			return $_FILES[$this->FormElementId];
		}

		if($this->DoDelete) return array('delete'=>true);
		
		return null;
	}
	
	function setPostValue($v){
		if(isset($v['delete'])){
			$this->DoDelete = true;
		}
	}
	
	function PreRender(){
		if($this->DoDelete){
			$this->MainView->Actions['save']->doAction();
		}
	}
	
	function Render(){
		
		$value = $this->Value;
		if($this->ReadOnly) {
			if(!empty($value['url']))
			return <<<EOD
<div id="{$this->FormElementId}"><img src="{$value['url']}" /></div>
EOD;
		} else {
			
			$MaxFileSize = ini_get('upload_max_filesize');
			
			if(empty($value['ext'])){
			$str= <<<EOD
<input type="file" id="{$this->FormElementId}" name="{$this->FormElementId}"  {$this->Attributes} /> Max File Size: $MaxFileSize
<div id="{$this->FormElementId}">

EOD;
				$a=array();

				foreach($this->Sizes as $k=>$v) {
					$a[]=<<<EOD
	<span>{$v[0]}x{$v[1]}</span>
EOD;
				}

				$str.=implode("&nbsp;|&nbsp;",$a)."</div>";
				return $str;
			}
			$delLabel = Culture::Delete;
			$nameVal = htmlspecialchars($value['name']);
			$str= <<<EOD
<input type="hidden" name="MAX_FILE_SIZE" value="{$MaxFileSize}" />
<input type="file" id="{$this->FormElementId}" name="{$this->FormElementId}"  {$this->Attributes} /> Max File Size: $MaxFileSize
<div id="{$this->FormElementId}">

EOD;
			$a=array();
			if(is_array($value["urls"])) {
				foreach ($value["urls"] as $k=>$v) {
					$a[]=<<<EOD
	<a href="{$v['url']}" target="_blank">{$v["label"]}</a>
EOD;
				}
			}
			if(!empty($a)) {
				$str.=implode("&nbsp;|&nbsp;",$a)."&nbsp;";
			}
			$str.=<<<EOD
<input type="submit" name="{$this->FormElementName}[delete]" value="{$delLabel}" />
</div>
EOD;
			return $str;
		}
	}

	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>";
	}
	
}




class ControlPHPData extends BaseControl  {

	public $EditFile='';
	public $EditTemplate='';


	function getValue(){
		if(isEmptyArray($this->Value)) return '';
		return serialize($this->Value);
	}
	
	function setValue($v){
		$this->Value = unserialize($v);
	}
	
	function Render(){
		$Value = $this->Value;
		$NamePrefix = $this->FormElementName;

		ob_start();
		if($this->EditFile){
			include($this->EditFile);
		} elseif ($this->EditTemplate) {
			eval('?>'.$this->EditTemplate.'<?');
		}
		return ob_get_clean();
	}

	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>";
	}
	
}

class ControlCascadedList extends ControlSelect {
	
	public $PHPData="";
	/** @var FEAttribute*/
	public $attribute;
	
	function Render() {
		
		$tag="select";
		if($this->isSearch) {
			$isSearch="search:1,";
			$search_val='';
		}
		else {
			$isSearch="";
			$search_val=0;
		}
		
		$params=array();
		if(!empty($this->PHPData["params"])) {
			eval("\$params={$this->PHPData["params"]};");
		}
		
		$dep=array();
		$dep2=array();
		
		$js=str_replace("-","",$this->getDefaultControlJSID());
		foreach ($params["depend"] as $k=>$v) {
			$dep[]="e{$v[0]}_{$v[1]}:function(){ return $('#".$this->View->View->Controls[$v[0]]->Controls[$v[1]]->FormElementId."').val();}";
			$dep2[]="$('#".$this->View->View->Controls[$v[0]]->Controls[$v[1]]->FormElementId."').change({$js}fun);";
		}
	/*	if(count($params["depend"])>0) {
			$tag="div";
			$debug="debug:1,";
		}*/
		$dep=implode(',',$dep);
		$dep2=implode($dep2);
		
		$vl=getdb()->getOne(str_replace("_#VAL#_",$this->Value,$this->PHPData["display_sql"]));
		
		return <<<EOD
		<{$tag} name="{$this->FormElementName}" id="{$this->FormElementId}"><option value='{$this->Value}'>{$vl}</option></$tag>
		<script>
		$(document).ready(	
			function() {
				{$js}fun(null,'$this->Value');
				{$dep2}
			}
		);
		function {$js}fun(e,vl) {
			if(vl==undefined) {
				vl='{$search_val}';
			}
			$("#{$this->FormElementId}").load("{$params['callUrl']}",{{$isSearch}{$debug}value:vl,id:'{$this->attribute->Id}',form_id:'{$this->FormElementId}',group_id:'{$this->attribute->Group->Id}',{$dep}});			
		}
		</script>
EOD;

	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		return $RenderValues ? $this->Render() : parent::BuildTemplate($prefix,$RenderValues);
	
	}
}

class ControlAutoComplete extends ControlTextInput {
	
	public $ClusterId=0;	//zaradi render
	public $PHPData="";
	public $isSearch=0;
	/** @var FEAttribute*/
	public $attribute;
	
	function Render() {
		$params=array();
		if(!empty($this->PHPData["params"])) {
			eval("\$params={$this->PHPData["params"]};");
		}
		
		$dep=array();
		
		$js=str_replace("-","",$this->getDefaultControlJSID());
		foreach ($params["depend"] as $k=>$v) {
			$dep[]="e{$v[0]}_{$v[1]}:function(){ return $('#".$this->View->View->Controls[$v[0]]->Controls[$v[1]]->FormElementId."').val();}";			
		}
	
		$dep=implode(',',$dep);
		
		
		return <<<EOD
		<link rel="stylesheet" href="/js/jquery/jquery-autocomplete/jquery.autocomplete.css">		
		<script src='/js/jquery/jquery-autocomplete/jquery.autocomplete.js'></script>

		<script>
		$(document).ready(	
			function() {
				$('#{$this->FormElementId}').autocomplete(
					"{$params['callUrl']}",
					{
						delay:10,
				      	minChars:1,
				      	matchSubset:1,
				      	matchContains:1,
				      	cacheLength:10,
				      	extraParams: {search:'{$this->isSearch}',id:"{$this->attribute->Id}",group_id:'{$this->attribute->Group->Id}',ac_id:'{$this->ClusterId}',{$dep}},
				      	autoFill:true,
				      	selectFirst :true
					}
				);
			}
		);		
		</script>
EOD;
	
		
		
		
//		$c =  AttributeCluster::byId($ac_id);
//		foreach ($params["depend"] as $k=>$v) {
//			$c->OnlyById[$v[0]]->OnlyById[$v[1]]->AddWhere("value='_#GID_AID#_'");
//		}
	//	return parent::Render();
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		if($this->ReadOnly) {
			return parent::BuildTemplate($prefix,$RenderValues);
		}
		return parent::BuildTemplate($prefix,$RenderValues).$this->Render();
	
	}
}

require_once(dirname(__FILE__).'/../be2/OrderField.php');

?>