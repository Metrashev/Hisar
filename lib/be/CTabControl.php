<?php

class CTab {
	public $tabs=array();
	public $active_tab=0;
	public $control_id='';
	public $use_cookies=true;
	public $custom_switch_function="";
	public $has_full_screen=false;
	
	public $use_ajax=false;
	
	
	function __construct($control_id,$add_full_screen=false) {
		$this->control_id=$control_id;
		$this->active_tab=$_COOKIE[$this->control_id];
		$this->has_full_screen=$add_full_screen;
	}
	
	function add_tab_ajax($caption,$file_to_include,$parameters=array(),$custom_index="") {
		if(empty($custom_index)) {
			$this->tabs[]=array('caption'=>$caption,'ajax'=>$file_to_include,'params'=>$parameters);
		}
		else {
			$this->tabs[$custom_index]=array('caption'=>$caption,'ajax'=>$file_to_include,'params'=>$parameters);
		}
		return count($this->tabs);
	}
	
	function add_tab($caption,$body,$custom_index="") {
		if(empty($custom_index)) {
			$this->tabs[]=array('caption'=>$caption,'body'=>$body);
		}
		else {
			$this->tabs[$custom_index]=array('caption'=>$caption,'body'=>$body);
		}
		return count($this->tabs);
	}
	
	function setActiveTab($index) {		
		$p=pathinfo($_SERVER['SCRIPT_NAME']);
		if(substr($p['dirname'],-1)!="/") {
			$p['dirname'].="/";
		}
		setcookie($this->control_id,$index,time()+3600*24*365,$p['dirname']);		
		$this->active_tab=$index;
	}
	
	function getFullScreenFunc() {
		$cook=$this->use_cookies?"1":"0";
		return <<<EOD
	
	function setFullScreen(obj,control_id,force) {
		var use_cookie={$cook};
		var tables=document.getElementsByTagName('table');
		if(tables&&tables.length) {
			try {
				var i=0;
				
				for(i=0;i<tables.length;i++) {
					if(tables[i].className=="test1") {
						if(!force) {
							if(tables[i].style.display=="none") {
								tables[i].style.display=tables[i].ols_display;
								obj.innerHTML="&uarr;";
								if(use_cookie) {
									DelCookie(control_id+'fs');
								}
								return;
							}
							else {
								tables[i].ols_display=tables[i].style.display;
								tables[i].style.display="none";
								obj.innerHTML="&darr;";
								if(use_cookie) {
									SetCookie(control_id+'fs',"none");
								}
								return;
							}
						}
						else {
							tables[i].ols_display=tables[i].style.display;
							tables[i].style.display=force;
							obj.innerHTML="&darr;";
							return;
						}
					}
				}
			}
			catch(e) {}
		}
	}
EOD;
	}
	
	function renderScript() {
		if(defined("TAB_SCRIPT")) {
			return '';
		}
		$cook=$this->use_cookies?"SetCookie(control_id,active_index);":"";
		
		if(!empty($this->custom_switch_function)) {
			return <<<EOD
<script>
	function switchTab(control_id,active_index,tab_count,ajax_request,ajax_params) {
		{$this->custom_switch_function}(control_id,active_index,tab_count,ajax_request,ajax_params);
	}
	{$this->getFullScreenFunc()}
</script>
EOD;
		}
		
		return <<<EOD
<script>
	function switchTab(control_id,active_index,tab_count,ajax_request,ajax_params) {

		var i;
		for(i=0;i<tab_count;i++) {
			var sp=document.getElementById(control_id+'_sp_'+i);
			if(sp) {
				sp.className=(i==active_index)?'current':'tab_normal';
			}
			
			var dv=document.getElementById(control_id+'_div_'+i);
			if(dv) {
				dv.style.display=(i==active_index)?'block':'none';
				if(i==active_index&&ajax_request) {					
					if(sp.getAttribute('is_loaded')!=1) {
						requestPage(ajax_request,ajax_params,control_id+'_div_'+i,control_id+'_sp_'+i);
					}					
				}				
			}	
		}
		{$cook}
	}
	{$this->getFullScreenFunc()}
	
</script> 
EOD;
	}
	
	function renderStyle() {
		return <<<EOD
<style type="text/css">
<!--
UNKNOWN {
        FONT-SIZE: small
}

.tabDiv {
	background:#fff;
}

.div_tabs {
	background:#E6E6E6;
	padding:5px;	
}

#tab_header {
        FONT-SIZE: 93%; BACKGROUND: url(/i/tabs/bg.gif) #dae0d2 repeat-x 50% bottom; FLOAT: left; WIDTH: 100%; LINE-HEIGHT: normal;
}
#tab_header UL {
        PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 10px; LIST-STYLE-TYPE: none;
}
#tab_header LI {
        PADDING-RIGHT: 0px; PADDING-LEFT: 9px; BACKGROUND: url(/i/tabs/left.gif) no-repeat left top; FLOAT: left; PADDING-BOTTOM: 0px; MARGIN: 0px;margin-right:2px; PADDING-TOP: 0px
}
#tab_header A {
        PADDING-RIGHT: 9px; DISPLAY: block; PADDING-LEFT: 0px; FONT-WEIGHT: bold; BACKGROUND: url(/i/tabs/right.gif) no-repeat right top; FLOAT: left; PADDING-BOTTOM: 4px; COLOR: #fff; PADDING-TOP: 5px; TEXT-DECORATION: none
}
#tab_header A {
        FLOAT: none
}
#tab_header A:hover {
        COLOR: #fff;
}
#tab_header .current {
        BACKGROUND-IMAGE: url(/i/tabs/left_on.gif)
}
#tab_header .current A {
        BACKGROUND-IMAGE: url(/i/tabs/right_on.gif); PADDING-BOTTOM: 5px; COLOR: #000000;
}
-->
</style>
EOD;
	}
	
	private function render_active_tab_ajax($tab) {
		$___ctab_params=$tab['params'];
		if(!empty($___ctab_params)&&is_array($___ctab_params)) {
			extract($___ctab_params);
		}
		require_once(dirname(__FILE__)."/../../be/common/template_index.php");
		$__template_index=new IndexTemplate(0);
		$__template_index->form="";
$__template_index->body="";
$__template_index->html_head="";
$__template_index->title="";
$__template_index->meta="";
$__template_index->scripts=array();
$__template_index->css=array();
$__template_index->hidden=array();
		ob_start();
		include(dirname(__FILE__).'/../../be/'.$tab['ajax'].'/index.php');
		return ob_get_clean();		
	}
	
	private function getEncodedParams($tab) {
		if(!empty($tab['params'])&&is_array($tab['params'])) {
			return urlencode(base64_encode(gzcompress(serialize($tab['params']))));
		}
		return "";
	}
	
	function render() {
		$str="<div class='div_tabs'><div id=\"tab_header\"><ul>";
		$spans=array();
		$div=array();
		$count=count($this->tabs);
		foreach ($this->tabs as $k=>$v) {
			$class=$k==$this->active_tab?'current':'';
			$display=$k==$this->active_tab?"block":"none";
			if($this->use_ajax) {
				$ajax_request='"'.$v['ajax'].'"';
				$ajax_params=$this->getEncodedParams($v);
				
			}
			else {
				$ajax_params="";
				$ajax_request="null";
				
			}
			$is_loaded=($this->use_ajax&&$k==$this->active_tab)||isset($v['body'])?" is_loaded='1' ":"";
			$spans[]= "<li {$is_loaded} id='{$this->control_id}_sp_{$k}' class='{$class}'><a href='#' onclick='switchTab(\"{$this->control_id}\",\"{$k}\",\"{$count}\",{$ajax_request},\"{$ajax_params}\");return false;'>{$v['caption']}</a></li>";
			if($k==$this->active_tab&&$this->use_ajax&&isset($v['ajax'])) {
				$div[]="<div id='{$this->control_id}_div_{$k}' class='tabDiv' style='clear:both;display:{$display};'>{$this->render_active_tab_ajax($v)}</div>";    
			}
			else {
				if(!isset($v['body'])&&$this->use_ajax) {
					$div[]="<div id='{$this->control_id}_div_{$k}' class='tabDiv' style='clear:both;display:{$display};'><div style='color:#993300;font-weight:bold;text-align:center;padding-top:15px;'><img align=\"absmiddle\" src=\"".BE_IMG_DIR."loader.gif\" />Loading ...</div></div>";
				}
				else {
					$div[]="<div id='{$this->control_id}_div_{$k}' class='tabDiv' style='clear:both;display:{$display};'>{$v['body']}</div>";    
				}
			}
		}
		if($this->has_full_screen) {
			$spans[]="<li><a id='{$this->control_id}_fs' href='#' onclick='setFullScreen(this,&quot;".$this->control_id."&quot;);' title='Full Screen'>&uarr;</a></li>";
		}
		$str.=implode("\n",$spans)."</ul></div>";
		$str.="<div style='border:1px solid #ccc;background:#fff;'>".implode("",$div)."</div>";
		$str.="</div>";	//div_tabs
		$extra="";
		if($this->use_cookies) {
			if(isset($_COOKIE[$this->control_id."fs"])) {
				$extra=<<<EOD
					<script>
					setFullScreen(document.getElementById('{$this->control_id}_fs'),"{$this->control_id}","{$_COOKIE[$this->control_id."fs"]}");
					</script>
EOD;
			}
		}
		
		return $this->renderStyle().$this->renderScript().$str.$extra;		
	}
}

?>