<?php

class CCustomListBox {
	
	public $control_id;	
	public $name;
	
	public $selected;
	
	public $attributes=array(
		'selected_color'=>'#0A246A',		
	);
	
	function __construct($control_id,$selected,$name="") {
		$this->control_id=$control_id;
		$this->name=empty($name)?$this->control_id:$name;
		$this->setSelected($selected);
	}
	
	function setSelected($val) {
		$this->selected=$val;
	}
	
	function renderScript() {
		return <<<EOD
		<script type="text/javascript">
		var {$this->control_id}_selected=null;
		function list_box_find_selected(obj_id) {
			var d=document.getElementById(obj_id);
			if(!d||d==undefined) {
				return;
			}
			
			if(d.childNodes&&d.childNodes.length) {
				var i;
				for(i=0;i<d.childNodes.length;i++) {
					var ch=d.childNodes[i];
					
					if(!ch||ch.tagName.toLowerCase()!='div') {
						continue;
					}
					
					if(ch.getAttribute('selected')=='selected') {
						listbox_set_selected(ch,obj_id);
						return;
					}
				}
			}
		}
		
		function listbox_set_selected(obj,control_id) {
			if(!obj||obj==undefined) {
				return;
			}
			obj.setAttribute('old_class',obj.className);
			obj.className="listbox_selected";
			if(obj.getAttribute('sel_style')) {
				if(obj.getAttribute('style')) {
					obj.setAttribute('old_style',obj.getAttribute('style'));
				}
				obj.setAttribute('style',obj.getAttribute('sel_style'));
			}
			if({$this->control_id}_selected!=null) {
				if({$this->control_id}_selected.getAttribute('old_class')) {
					{$this->control_id}_selected.className={$this->control_id}_selected.getAttribute('oldClass');
				}
				else {
					{$this->control_id}_selected.className="";
				}
				if({$this->control_id}_selected.getAttribute('old_style')) {
					{$this->control_id}_selected.setAttribute('style',{$this->control_id}_selected.getAttribute('style'));
				}
			}
			{$this->control_id}_selected=obj;
			document.getElementById(control_id).value=obj.getAttribute('value');
		}
		
		function listbox_processKey(e,obj,control_id) {
			
			if(e.keyCode!=38&&e.keyCode!=40&&e.keyCode!=36&&e.keyCode!=35) {
				return;
			}
			if({$this->control_id}_selected!=null) {
				pred=null;
				switch(e.keyCode) {
					case 38: {	//up
						pred={$this->control_id}_selected.previousSibling;
						break;
					}
					case 40: {	//down
						pred={$this->control_id}_selected.nextSibling;
						break;
					}
					case 36: {	//home
						pred=obj.childNodes&&obj.childNodes.length?obj.childNodes[0]:null;
						break;
					}
					case 35: {	//end
						pred=obj.childNodes&&obj.childNodes.length?obj.childNodes[obj.childNodes.length-1]:null;
						break;
					}					
				}				
				if(pred&&pred.tagName.toLowerCase()=="div") {
					listbox_set_selected(pred,control_id);
					return;
				}
				return;
			}
			if(obj.childNodes&&obj.childNodes.length) {
				listbox_set_selected(obj.childNodes[0],control_id);
			}
		}
		
		</script>
EOD;
	}
	
	function render($items) {
	
		$selected=htmlspecialchars($this->selected);
		$str= <<<EOD
		
		<input type="hidden" id="{$this->control_id}" name={$this->control_id} value="{$selected}" />
		<div id="{$this->control_id}_div" class="listBox" onkeyup="listbox_processKey(event,this,'{$this->control_id}')">
EOD;
		if(is_array($items)) {
		foreach ($items as $k=>$v) {			
			$keys=array();			
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
					$selected_str=" id='{$this->control_id}_s' selected=\"selected\" class=\"listbox_selected\"";
				}
				else {
					if("$k"=="$selected") {
						$selected_str=" id='{$this->control_id}_s' selected=\"selected\" class=\"listbox_selected\"";
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
				$selected_str.=<<<EOD
 onclick="listbox_set_selected(this,'{$this->control_id}');"
EOD;
				if($no_key) {
					$str.="<div{$selected_str}{$str_extra}>{$val}</div>";
				}
				else {
					$str.="<div{$selected_str}{$str_extra} value=\"".htmlspecialchars($k)."\">{$val}</div>";
				}
				
			}
		}
		$str.=<<<EOD
		</div>
		<script type="text/javascript">
		listbox_set_selected(document.getElementById('{$this->control_id}_s'),'{$this->control_id}');
		</script>
EOD;
		return $str;
	}
	
}

?>