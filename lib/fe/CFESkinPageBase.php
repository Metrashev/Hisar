<?php

/*

class CMenuItem{
	public $name;
	public $href;
	public $target='';
	public $selected=false;
	public $visible=true;
	
}
*/

class CFESkinPageBase {

	public $styles = array();
	public $css = array();
	public $js = array();
	public $scripts = array();
	
	
	public $CrumbsPath=array();
	public $MenuItems=array();
	public $PageTitle=array();
	
	public $MetaKeyWords = '';
	public $MetaDescription = '';
	public $TrackerCode = '';
	
		
	public $data = array();
	
	private $fc;
	
	
	function __construct(){
		$this->fc = FrontControler::getInstance(1);
		
		$this->CrumbsPath = $this->fc->nodesPath;
		$this->MenuItems = $this->fc->tree->expandedMenu;
		
		foreach($this->CrumbsPath as $node){
			if(!$node['is_title_visible']) continue;
			$this->PageTitle[] = empty($node['page_title']) ? $node['value'] : $node['page_title'];
		}

		$nodesPath = $this->fc->nodesPath;
		$cnt = count($nodesPath)-1;
		for($i=$cnt; $i>=0; $i--)
		{
			$row = $nodesPath[$i];	
			if(!$this->MetaDescription){
				$this->MetaDescription = $row['meta_description'];
			}
			if(!$this->MetaKeyWords){
				$this->MetaKeyWords = $row['meta_keywords'];
			}
			if(!$this->TrackerCode){
				$this->TrackerCode = $row['tracker_code'];
			}
		}
	}

	static function isIE4(){
		return (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 5')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 4')!==false);
	}


	function getPageTitle(){
	  $Nodes = $this->PageTitle;
	  $PageTitle = array();
	  $prev = '';

	  foreach ($Nodes as $val){
	  	$next = mb_convert_case($val, MB_CASE_LOWER);
	  	if($prev==$next) {
	  		$prev=$next;
	  		continue;
	  	}
	  	$prev=$next;
	  	
	  	$PageTitle[] = $val;
	  }
	  return implode(' &gt; ', $PageTitle);
	}

	static function getBackLink(){
		return 'JavaScript:history.go(-'.CBackLinkCounter::getCnt().')';
	}

	function getCrumbsPathHtml(){
		$res = array();
		foreach($this->CrumbsPath as $item){
			if($item['is_crumb_visible']==0) continue;
			$res[] = "<a href=\"{$item['href']}\"{$node['target']}>{$item['value']}</a>";
		}		
		return implode(' &gt;&gt; ', $res);
	}
	

	
	function getData(){
		$data = $this->data;

		$data['PageTitle'] = self::getPageTitle();
		$data['BackLinkHref'] = self::getBackLink();
		$data['PrintLinkHref'] = FrontControler::getPrintLink();
		$data['HidePrintLink'] = $GLOBALS['HidePrintLink'];


		$data['Header'] = '';
		
		if(!empty($this->MetaDescription)){
			$data['Header'] .= '<meta name="description" content="'.$this->MetaDescription.'" />'."\n";
		}
		
		if(!empty($this->MetaKeyWords)){
			$data['Header'] .= '<meta name="keywords" content="'.$this->MetaKeyWords.'" />'."\n";
		}
		
		if(!empty($this->styles)){
			$data['Header'] .= '<style type="text/css">'.implode("\n", $this->styles).'</style>'."\n";
		}

		if(!empty($this->scripts)){
			$data['Header'] .= '<script type="text/javascript">'.implode("\n", $this->scripts).'</script>'."\n";
		}

		if(!empty($this->css)){
			$data['Header'] .= '<link rel="stylesheet" type="text/css"  href="'.implode('" />'."\n".'<link rel="stylesheet" type="text/css"  href="', $this->css).'" />'."\n";
		}

		if(!empty($this->js)){
			$data['Header'] .= '<script type="text/javascript" src="'.implode('"></script>'."\n".'<script type="text/javascript" src="', $this->js).'"></script>'."\n";
		}

		$data['CrumbsPath'] = $this->getCrumbsPathHtml();
		
		$data['TrackerCode'] = $this->TrackerCode;
		
		return $data;
	}

}

?>