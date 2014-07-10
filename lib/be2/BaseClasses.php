<?php


interface IView {

	function BuildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this');
	//function RenderView($template='');

}


abstract class BaseControl {
	
	const ID_SEPARATOR='-';

	/**
	 * Enter description here...
	 *
	 * @var BaseView
	 */
	public $View=null;
	public $MainView=null;
	public $Label;
	public $Id;
	public $FormElementName;
	public $FormElementId;

	public $Attributes;

	protected $ReadOnly=false;
	public $Required=false;
	public $Value;
	
	public $isValid = true;
	public $isEmpty = true;


	function __construct($Id, $Label='', $Attributes=''){

		$this->Id = $Id;
		$this->Label = $Label;
		$this->Attributes = $Attributes;

		$this->FormElementName = $this->Id;
		$this->FormElementId = $this->Id;
	}
	
	static function staticRender($cn, $Id, $Value=null, $Attributes=''){
		//$cn = get_called_class();
		$c = new $cn($Id, '', $Attributes);
		$c->Init();
		if(!is_null($Value)) $c->setValue($Value);
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$c->setPostValue($_POST[$Id]);
		}
		return $c->Render();
	}
	
	function Init(){
		
	}

	function setReadOnly($flag){
		$this->ReadOnly = (boolean)$flag;
	}

	function setView(IView $View){
		$this->View = $View;
		$this->MainView = $View->MainView;
		$this->FormElementName = $this->View->getControlName($this->Id);
		$this->FormElementId = $this->View->getControlId($this->Id);
	}

	/**
	 * For complex control with more than one simple HTML controls in it.
	 *
	 * @return String
	 */
	function getDefaultControlJSID(){
		return $this->FormElementId;
	}

	/**
	 * Returns value in format for DB Storage OR Iternal PHP Format
	 *
	 * @return mixed
	 */
	function getValue(){
		return $this->Value;
	}


	function setValue($v){
		$this->Value = $v;
	}

	function setPostValue($v){
		$this->Value = $v;
		$this->isValid = false;
	}

	function getLabel(){
		return $this->Label;
	}

	function isValid(){
		return $this->isValid;
	}

	
	function PreRender(){
		
	}

	function Render(){
		return $this->BuildTemplate('', true);
	}
	
	static function buildAttributes($a){
		$res = '';
		foreach ($a as $k=>$v) {
			$res .= $k.'="'.htmlspecialchars($v).'" ';
		}
		return $res;
	}
}




class BaseView extends BaseControl implements IView {

	public $Id;
	public $Label;
	public $RenderStyle=''; //inline
	public $RenderColumnsCnt=2;
	public $Template = null;
	public $RenderChildsInTabs=false;
	public $DisplayTitleToggle=false;
	
	public $HeaderMessage=''; // Renderira se v hedara na Viewto
	public $CrumbsPath='';
	



	public $Controls = array();
	public $ChildViews = array();
	public $Actions = array();

	protected $ReadOnly=false;

	protected $areChildControlsCreated = false;
	
	
	public $styles=array();
	public $scripts=array();
	public $css=array();
	public $js=array();
	
	function __construct($Id, $Label='', $Attributes=''){
		parent::__construct($Id, $Label, $Attributes);
		$this->MainView = $this;
	}


	function __clone(){
		$this->ChildViews = array();
		foreach ($this->Controls as $id=>$c) {
			$this->Controls[$id] = clone $c;
			if ($c instanceof BaseView) $this->ChildViews[$id] = $this->Controls[$id];
		}
		foreach ($this->Actions as $id=>$c) $this->Actions[$id] = clone $c;
	}
	
	function Init(){
		$this->BuildChildControls();
		$this->areChildControlsCreated = true;
		foreach ($this->Controls as $c) $c->Init();
		foreach ($this->Actions as $c) $c->Init();
	}

	
	function setReadOnly($flag){
		$this->ReadOnly = $flag;
		foreach ($this->Controls as $Control) $Control->setReadOnly($flag);
	}
	
	function setView(IView $View){
		parent::setView($View);
		foreach ($this->Controls as $c) $c->setView($this);
		foreach ($this->Actions as $c) $c->setView($this);
		// za ChildViews niama zashtoto te sa i v Controls masiva.
	}

	function BuildChildControls(){
		$this->areChildControlsCreated = true;
	}



	function LoadDataFromRequest(){
		$ControlsData = $_REQUEST[$this->Id];
		$this->setPostValue($ControlsData);
	}

	function getValue(){
		
		$data = array();
		foreach ($this->Controls as $id=>$control) {
			if($control->ReadOnly) continue;
			$data[$id] = $control->getValue();
		}
		return $data;
	}


	function setValue($Value){
		$this->Value = $Value;
		foreach ($this->Controls as $id=>$control) {
			$control->setValue($Value[$id]);
		}

	}


	function setPostValue($Value){
		
		$this->Value = $Value;
		foreach ($this->Controls as $id=>$control) {
			/*
			// TODO - Dali e samo pri tazi situacia i dali tova e proverkata? Celta e pri kombinacia na formata ot aktivni i readonly controli da nezamazvame stoinosta na readonly controlite ot _POST zashtoto tam gi niama, a da im ostane stoinostta ot modela.
			*/
			if(!$control->ReadOnly || $control instanceof BaseView ){ 
				$control->setPostValue($Value[$id]);
			}
		}
	}


	function getControlId($name){
		return $this->FormElementId.self::ID_SEPARATOR.$name;
	}

	function getControlName($name){
		return $this->FormElementName."[{$name}]";
	}


	function addControl(BaseControl $control){
		if($control instanceof IView) {
			$this->ChildViews[$control->Id] = $control;
		}
		$this->Controls[$control->Id] = $control;
		$control->setView($this);
		
		
		//array_splice($this->Controls, $offset, 1, array($control));
	}
	

	function addAction(BaseAction $Action){
		$this->Actions[$Action->Id] = $Action;
		$Action->setView($this);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return BaseAction
	 */
	function getAction(){
		return $this->_getAction($_REQUEST[$this->Id]);
	}

	function _getAction($data){

		foreach ($this->Actions as $id=>$Action) {
			if(isset($data[$id])){
				$Action->Value = $data[$id];
				return $Action;
			}
		}

		foreach ($this->ChildViews as $v) {
			$Action = $v->_getAction($data[$v->Id]);
			if(!empty($Action)){
				return $Action;
			}
		}
		return null;
	}

	function renderControl($name, $attr=''){
		return $this->Controls[$name]->render($attr);
	}



	function PreRender(){
		if(empty($this->View)){
			$this->css['MainCSS'] = 'lib.css';
			$this->js['JQ'] = '/js2/jq.js';
			$this->js['ITTI'] = '/js2/itti.js';
			$this->js['TableJS'] = '/js2/BeListTable.js';
		}
		// Vazno e da e sled gornite redove, za da childs da mogat da gi podmeniat 
		
		foreach ($this->Controls as $c) $c->PreRender();
		
		if(!$this->RenderChildsInTabs) return ;
		
		$ViewJsId = $this->getDefaultControlJSID();

		$this->scripts[] = <<<EOD
 $(document).ready(function(){
    $("#{$ViewJsId}_tabs").tabs({ cookie: { expires: 30 } }); 
    //$("#{$ViewJsId}_tabs").tabs('select', 0);
 });
EOD;
	}
	
	function Render(){
		return $this->BuildViewTemplate(true,true,true,true,true);
	}

	function BuildTemplate($prefix, $RenderValues=false){
		throw new Exception('a');
	}
/*
	function RenderView($template=''){
		if($template) $this->Template=$template;
		if($this->Template){
			ob_start();
			//$View = $this;
			include($this->Template);
			return ob_get_clean();
		}

		return $this->BuildViewTemplate(true,true,true,true,true);

	}
*/
	function mergeHeaders(){
		foreach ($this->ChildViews as $v){
			$v->mergeHeaders();
			$this->styles = array_merge($this->styles, $v->styles);
			$this->scripts = array_merge($this->scripts, $v->scripts);
			$this->css = array_merge($this->css, $v->css);
			$this->js = array_merge($this->js, $v->js);
		}
	}
	
	function RenderHTMLHead(){

		$this->mergeHeaders();
		$data = '';
		
		if(!empty($this->css)){
			
			$data .= '<link rel="stylesheet" type="text/css"  href="'.implode('" />'."\n".'<link rel="stylesheet" type="text/css"  href="', $this->css).'" />'."\n";
		}
		
		if(!empty($this->styles)){
			$data .= '<style type="text/css">'.implode("\n", $this->styles).'</style>'."\n";
		}

		if(!empty($this->js)){
			$data .= '<script type="text/javascript" src="'.implode('"></script>'."\n".'<script type="text/javascript" src="', $this->js).'"></script>'."\n";
		}
		
		if(!empty($this->scripts)){
			$data .= '<script type="text/javascript">'.implode("\n", $this->scripts).'</script>'."\n";
		}
		
		return $data;
	}
	
	function RenderBtnBar($ExpandControls=true, $RenderValues=true, $prefix = '$this'){
		$BtnBar = '';
		foreach ($this->Actions as $id=>$action) {
			if(!$action->RenderInBtnBar) continue;
			$BtnBar .= $ExpandControls ? $action->buildTemplate("{$prefix}->Actions['{$id}']", $RenderValues) : "<?={$prefix}->Actions['{$id}']->render(); ?>";
		}
		if($BtnBar) $BtnBar = "<div class=\"ButtonBar\">$BtnBar</div>";
		return $BtnBar;
	}
	
	function RenderHeaderMessage(){
		if($this->HeaderMessage){
			return "<div class=\"HeaderMessage\">{$this->HeaderMessage}</div>";
		}
	}
	
	function RenderViewTitle(){
		if($this->Label){
			$ViewJsId = $this->FormElementId;
			$class = empty($this->View) ? 'class="MainViewTitle"' : 'class="ViewTitle"';
			
			if($this->DisplayTitleToggle)
			$toggles = <<<EOD
<div toggle="{$ViewJsId}_container">
<div class="show" style="display:none">+</div>
<div class="hide">-</div>
</div>
EOD;
			
			return <<<EOD
			<table {$class}><tr><td class="l"></td><td class="c">{$this->Label}</td><td class="r">$toggles</td></tr></table>
EOD;
		}
	}
	
	function RenderCrumbsPath(){
		if(empty($this->View)){
			if($_GET['cid']>0){
				require_once(dirname(__FILE__).'/../../lib/be/tree.php');
				$t=new CURLTree("categories");
				$Cats = <<<EOD
	{$t->get_node_path($_GET['cid'])}
EOD;
			}
			
			$call = CallManager::getCrumbsPath();
			if($Cats && $call) $call = '<br />'.$call;
			$this->CrumbsPath = $Cats.CallManager::getCrumbsPath();
		}
		
		
		
		if(!$this->CrumbsPath) return '';
		return <<<EOD
<div class="CrumbsPath">{$this->CrumbsPath}</div>
EOD;
	}

	
	function BuildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this'){
		
		if(empty($this->View)){
			$this->PreRender();
		}
		if($this->Template ){
			if($ExpandViews && !$RenderValues) return str_replace('$this', $prefix, $this->Template);
			ob_start();
			eval('?>'.$this->Template.'<?');
			return ob_get_clean();
		}
		
		if(count($this->ChildViews)<2) {
			$this->RenderChildsInTabs = false;
		}
		
		$ViewJsId = $ReplaceIds ? $this->getDefaultControlJSID() : "<?={$prefix}->getDefaultControlJSID(); ?>";
		
		if(empty($this->View)){
			$HTMLHead = $ExpandControls ? $this->RenderHTMLHead() : "<?={$prefix}->RenderHTMLHead();?>";
			$CrumbsPath = $ExpandControls ? $this->RenderCrumbsPath() : "<?={$prefix}->RenderCrumbsPath();?>";

			$HTML .= <<<EOD
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{$HTMLHead}
</head>
<body>
<form method="POST" enctype="multipart/form-data">
<table class="MainBodyTable"><tr><td>
{$CrumbsPath}
EOD;
		}




		
		
		
		$cells = array();
		$cols = $this->RenderColumnsCnt;
		$ViewsHTML = '';
		$ChildViews = array();
		$HiddenHTML = '';


		foreach ($this->Controls as $id=>$control) {
			$label = '';
			if($control instanceof BaseView){
				$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
				if($this->RenderChildsInTabs){
					$control->Label = '';
				}
				$tpl = $ExpandViews ? $control->BuildViewTemplate($ReplaceIds, $ReplaceLabels, $ExpandViews, $ExpandControls, $RenderValues, "{$prefix}->Controls['{$id}']") : "<?={$prefix}->Controls['{$id}']->render(); ?>";

				if($control->RenderStyle!='inline'){
					
					$jsId = $ReplaceIds ? $control->getDefaultControlJSID() : "<?={$prefix}->Controls['{$id}']->getDefaultControlJSID(); ?>";
				
					$ChildViews[] = array(
					'id' => $jsId,
					'title' => $label,
					'body'=>$tpl
					);
					$ViewsHTML .= <<<EOD
<tr>
	<td colspan="{$cols}">{$tpl}</td>
</tr>

EOD;
				} else {

				/* $tpl = $ExpandControls ? $control->buildHTMLTemplate($ReplaceIds, $ReplaceLabels, $ExpandViews, $ExpandControls, $RenderValues, "{$prefix}->Controls['{$id}']") : "<?={$prefix}->Controls['{$id}']->render(); ?>"; */
				$cells[] = "<label for=\"{$jsId}\">{$label}</label>";
				$cells[] = $tpl;

				}
			} else if($control instanceof ControlInputHidden ){
				$HiddenHTML .= $ExpandControls ? $control->buildTemplate("{$prefix}->Controls['{$id}']", $RenderValues) : "<?={$prefix}->Controls['{$id}']->render(); ?>";
			} else {
				$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
				$jsId = $ReplaceIds ? $control->getDefaultControlJSID() : "<?={$prefix}->Controls['{$id}']->getDefaultControlJSID(); ?>";
				$tpl = $ExpandControls ? $control->buildTemplate("{$prefix}->Controls['{$id}']", $RenderValues) : "<?={$prefix}->Controls['{$id}']->render(); ?>";
				
				$cells[] = "<label for=\"{$jsId}\">{$label}</label>";
				$cells[] = $tpl;
			}
		}
		$cells = convert_1d_to_2d_array($cells, 'Horizontal', $cols);
		//$cells = arrayToTableStyle($cells);
		$cells = arrayToTable($cells);
		
		if($this->RenderChildsInTabs){
		$ViewsHTML = <<<EOD
<tr>
	<td colspan="{$cols}">
<div id="{$ViewJsId}_tabs" class="ui-tabs">
    <ul>
EOD;
		foreach ($ChildViews as $row){
			$ViewsHTML .= <<<EOD
        <li><a href="#{$row['id']}_tab"><span>{$row['title']}</span></a></li>
EOD;
		}
		$ViewsHTML .= <<<EOD
</ul>
EOD;
		foreach ($ChildViews as $row){
			$ViewsHTML .= <<<EOD
    <div id="{$row['id']}_tab" tab="{$ViewJsId}_tabs">{$row['body']}</div>
EOD;
		}
		$ViewsHTML .= <<<EOD
</div>
</td></tr>
EOD;
}

		$ViewTitle = $ReplaceLabels ? $this->RenderViewTitle() : "<?={$prefix}->RenderViewTitle(); ?>";
		$BtnBar = $ExpandControls ? $this->RenderBtnBar($ExpandControls, $RenderValues, $prefix) : "<?={$prefix}->RenderBtnBar();?>";
		$HeaderMessage = $ExpandControls ? $this->RenderHeaderMessage() : "<?={$prefix}->RenderHeaderMessage();?>";

		$viewClass = $ViewTitle ? ' class="View"' : '';
		if($viewClass && empty($this->View)) $viewClass = ' class="MainView"';
		
		if($this->RenderChildsInTabs){
			$tableWidth = 'width="100%"';
		}
		
		$HTML .= <<<EOD
<div id="{$ViewJsId}" ViewID="{$ViewJsId}">
{$ViewTitle}
<div id="{$ViewJsId}_container" $viewClass>
{$BtnBar}
{$HiddenHTML}
{$HeaderMessage}

<table class="ViewBody" $tableWidth>
$cells
$ViewsHTML
</table>

{$BtnBar}
</div>
</div>
EOD;

		if(empty($this->View)){

			$HTML .= <<<EOD
</td></tr></table>
</form>
</body>
EOD;
		}

		return $HTML;
	}
	
	function Run(){
		$this->Init();
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$this->LoadDataFromRequest();
		}
		$Action = $this->getAction();
		if($Action) $Action->doAction();
		return $this->Render();
	}


}



class BaseCompositeControl extends BaseView {

	public $RenderStyle='inline';
	
	function getDefaultControlJSID(){
		return $this->FormElementId.self::ID_SEPARATOR.key($this->Controls);
	}
	
	function buildViewTemplateHelper($ReplaceLabels=true, $ExpandControls=true, $RenderValues=true, $prefix = '$this'){

		$Rendered = array();
		foreach ($this->Controls as $key=>$control){
			$Rendered[$key]['Control'] = $ExpandControls ? $this->Controls[$key]->BuildTemplate($prefix."->Controls['{$key}']", $RenderValues) : "<?={$prefix}->Controls['{$key}']->Render();?>";
			
			$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
			$Rendered[$key]['Label'] = "<label for=\"{$control->getDefaultControlJSID()}\">{$label}</label>";
		}

		return $Rendered;
	}
	
	function buildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this'){
		$Rendered = $this->buildViewTemplateHelper($ReplaceLabels, $ExpandControls, $RenderValues, $prefix);

		$HTML = '';
		foreach ($Rendered as $key=>$ctrl){
			$HTML .= <<<EOD
{$ctrl['Label']} {$ctrl['Control']}<br />
EOD;
		}


		return $HTML;
	}
	

}

class BaseAction extends BaseControl {
	
	public $RenderAsLink=false;
	public $RenderInBtnBar=true;
	public $Value = 1;

	function getActionJS(){
		$val = htmlspecialchars($this->Value);
		return "ITTI.doActionPost('{$this->MainView->FormElementId}','{$this->FormElementName}','{$val}')";
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		$js = $this->getActionJS();
		if($this->RenderAsLink){
			$HTML = <<<EOD
<a href="JavaScript:{$js};" {$this->Attributes}>{$this->Label}</a>
EOD;
		} else {
			$HTML = <<<EOD
<input onclick="{$js}" type="button"  value="{$this->Label}" {$this->Attributes} />
EOD;
		}
		return $HTML;
	}

	function doAction(){

	}
}


class ActionBack extends BaseAction {
	public $BackURL='';
	function doAction(){
		if(!CallManager::isReturn() && CallManager::isCall()){
			CallManager::goBack(null);
		} else {
			header("Location: {$this->BackURL}");
			exit();
		}
	}
}

class Culture {
	const Save='Save';
	const Edit='Edit';
	const Delete='Delete';
	const Back='Back';
	const Select='Select';
	const Clear='Clear';
	const EmptyStr='Empty';
	const Search='Search';
	

	static $YesNo = array(0=>'No', 1=>'Yes');

}



?>