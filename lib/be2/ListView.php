<?php

class ActionSearch extends BaseAction {

	function doAction(){
		$v = $this->View->getValue();
		$_SESSION[$this->View->FormElementId]['search'] = $v;
		$this->View->View->IsNewSearch = true;
	}
}

class ActionClear extends BaseAction {
	
	function doAction(){
		$_SESSION[$this->View->FormElementId]['search'] = array();
		$this->View->setPostValue(array());
		$this->View->View->IsNewSearch = true;
	}
}


class ActionEdit extends BaseAction {

	public $Url;

	function doAction(){
//		echo "Select";
		if(!CallManager::isReturn()){
			CallManager::goToUrl($this->Url.$this->Value,array(),$this->MainView->Label);
		}
	}
}

class ActionOrder extends BaseAction {
	public $RenderAsLink=false;
	public $RenderInBtnBar=false;
	
	function doAction(){
		$OrderedFields = $this->getOrderFields();
		$val = $this->Value;
		$add = substr($val, 0, 1);
		if($add=='+'){
			$add = true;
			$val = substr($val, 1);
		} else {
			$add = false;
		}

		if($add && $OrderedFields[$val]) {
			unset($OrderedFields[$val]);
		} else {
			$order = $OrderedFields[$val]=='ASC' ? 'DESC' : 'ASC';
			$OrderedFields[$val] = $order;
		}
		$this->setOrderFields($OrderedFields);
	}
	
	function applyOrderFields(){
		$OrderedFields = $this->getOrderFields();
		foreach ($OrderedFields as $k=>$order){
			list($gid, $aid) = split('_', $k);
			$field = $this->View->Cluster->OnlyById[$gid]->OnlyById[$aid];
			if($field) {
				$this->View->Cluster->addOrder($field, $order);
			} else {
				unset($OrderedFields[$k]);
			}
		}

		$this->setOrderFields($OrderedFields);
	}
	
	function getOrderFields(){
		$OrderedFields = $_SESSION[$this->View->FormElementId]['order'];
		if(!is_array($OrderedFields)) $OrderedFields= array();
		return $OrderedFields;
	}
	
	function setOrderFields($OrderedFields){
		$_SESSION[$this->View->FormElementId]['order'] = $OrderedFields;
	}
}



class ActionSelect extends BaseAction {

	public $RenderAsLink=true;
	public $RenderInBtnBar=false;
	

	function doAction(){
		CallManager::goBack($this->Value);
	}
}

class ActionMultiSelect extends BaseAction {
	
	public $RenderAsLink=false;
	public $RenderInBtnBar=true;
	
	public $SelectedIds=array();

	
	function doAction(){
		$this->process();
		CallManager::goBack(implode(',',$this->SelectedIds));
	}
	
	function getIdsCtrl(){
		$ids2 = implode(',',$this->SelectedIds);
		return <<<EOD
<input type="hidden" name="{$this->FormElementId}_all" value="{$ids2}" />
EOD;
	}
	
	function getCtrlForId($id){
		$checked = in_array($id, $this->SelectedIds) ? 'checked="true"' : '';
		$val = $checked ? 1 : 0;
		return <<<EOD


<input type="checkbox" value="1" {$checked} onClick="document.getElementById('{$this->FormElementId}_{$id}').value = this.checked ? this.value : ''"/>
<input type="hidden" id="{$this->FormElementId}_{$id}" name="{$this->FormElementId}[{$id}]" value="{$val}"/>

EOD;
	}
	
	function process(){
		if($_SERVER['REQUEST_METHOD']=='GET'){
			CallManager::setReturnParams(CallManager::$callParams);
			$this->SelectedIds = explode(',',CallManager::$callParams);
			
			return ;
		} else {
			$all = $_REQUEST[$this->FormElementId.'_all'];
			$this->SelectedIds = empty($all) ? array() : explode(',', $all);
		}
		
		$new = $_REQUEST[$this->FormElementId];
		
		foreach ($new as $id=>$v){
			if($v) {
				if(!in_array($id, $this->SelectedIds)) $this->SelectedIds[]=$id;
			} else {
				$pos = array_search($id, $this->SelectedIds);
				if($pos!==false) unset($this->SelectedIds[$pos]);
			}
		}
	}
}

class ActionExport extends BaseAction {
	
	public $ListBox = null;
	
	function __clone(){
		$this->ListBox = clone $this->ListBox;
	}

	function Init(){
		parent::Init();
		$this->ListBox = new ControlSelect($this->FormElementId.'_sel', 'Format','doubled="true"');
		$this->ListBox->Options = array(1=>'Excel 97', 2=>'Excel 2007', 3=>'XML', 4=>'HTML', 5=>'TXT', 6=>'CSV');
		//$this->ListBox->setView($this->View);
	}
	
	
	function setView(IView $View){
		parent::setView($View);
		//if($this->ListBox) $this->ListBox->setView($View);

	}

	function doAction(){
		$model = $this->View->Cluster;
		$model->Export($_POST[$this->FormElementId.'_sel']);
		
	}
	
	function BuildTemplate($prefix, $RenderValues=false){
		$HTML = parent::BuildTemplate($prefix, $RenderValues);
		$h = $this->ListBox->Render();
		return $HTML.$h;
	}
}


class TableView extends BaseView {
	public $Cluster;
	
	public $OrderAction;
	public $SelectAction;
	public $MultiSelectAction;
	//public $EditAction;
	public $EditLink='?';
	public $EditLinkParams=array();
	
	public $ItemsPerPage = 20;
	
	
	function __construct($Id, $Label='', AttributeCluster $Cluster){
		parent::__construct($Id, $Label);
		$this->Cluster = $Cluster;
		$this->EditLink = "index.php?";
		$this->EditLinkParams['attribute_cluster_id'] = $this->Cluster->AttributeClusterId;
		$this->EditLinkParams['cid'] = $_GET['cid']; 
		$this->EditLinkParams['bkp'] = urlencode($_SERVER['REQUEST_URI']); 

	}
	
	function BuildChildControls(){
		if(empty($this->OrderAction)) $this->OrderAction = new ActionOrder('ord', 'Order');
		$this->addAction($this->OrderAction);
		
		if(CallManager::isCall() && $_GET['search']=='single'){
			if(empty($this->SelectAction)) $this->SelectAction = new ActionSelect('select', Culture::Select);
			$this->addAction($this->SelectAction);
			$this->SelectAction->Value = '_#ID#_';
			CallManager::setReturnParams(CallManager::$callParams);
		}
		
		if(CallManager::isCall() && $_GET['search']=='multiple'){
			if(empty($this->MultiSelectAction)) $this->MultiSelectAction = new ActionMultiSelect('select', Culture::Select);
			$this->addAction($this->MultiSelectAction);
		}
		
		if(CallManager::isCall()){
			$this->addAction(new ActionBack('back', Culture::Back));
		}
		
		$this->addAction(new ActionExport('Export','Export'));
		/*
		$this->EditAction = new ActionEdit('e',Culture::Edit);
		$this->EditAction->getActionJS()
		*/
	}
	
	
	function getData(){
		

		
		$count = $this->Cluster->getProductsCnt();
		
		$pbVarName = $this->FormElementId.BaseView::ID_SEPARATOR.'page';
		if(!isset($_REQUEST[$pbVarName])){
			$_REQUEST[$pbVarName] = $_SESSION[$this->FormElementId]['p'];
		}
		
		if($this->View->IsNewSearch){
			$_REQUEST[$pbVarName] = 1;
		}
		

		$pb = new CFEPageBar($this->ItemsPerPage, $count, $this->FormElementId.BaseView::ID_SEPARATOR.'page');
		$i = ($pb->CurrentPage - 1)*$this->ItemsPerPage;
		if($i>=$count) {
			$i = 0;
			$pb->CurrentPage = 1;
		}
		$_SESSION[$this->FormElementId]['p'] = $pb->CurrentPage;

		$data = array();
		$data['PageBar'] = $pb->getData('#');
		$data['PageBar']['ItemsCnt'] = $count;
		$data['items'] = $count ? $this->Cluster->getAllProducts($this->ItemsPerPage,$i) : array();

		return $data;
	}

	
	function BuildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$View'){
		if($this->Actions['select']){
			$sel = $this->Actions['select']->Render();
		}
		
		
		if($this->MultiSelectAction){
			$cbAction = $this->MultiSelectAction;
			$cbAction->process();
		}
		

		$this->OrderAction->applyOrderFields();
		
		$cluster = $this->Cluster;
$header = array();

$be_dir = BE_DIR;
$editLink1 = $this->EditLink;
$editLink2 = http_build_query($this->EditLinkParams);

$header[0] = "<td class='new'><a href='{$editLink1}EditId=0&amp;{$editLink2}'>New</a></td>";


$ordered = array();
foreach ($cluster->order as $k=>$attribute){
	$key = $attribute->Group->Id.'_'.$attribute->Id;
	$ordered[$key]=array('ord'=>$attribute->order, 'pos'=>$k+1);
}

foreach ($cluster as $gid=>$group){
	foreach ($group as $aid=>$attribute){
		if(!$attribute->PHPData['BEListVisible']) {
			$attribute->Visible = false;
			continue;
		}
		
		
		$key = $gid.'_'.$aid;
		
		if(!$attribute->PHPData['BEListOrderable']){
			$header[$key] = "<td>{$attribute->LabelPrefix}</td>";
			continue;
		}
		
		if($ordered[$key]) {
			$style = " class='{$ordered[$key]['ord']}'";
			$pos = "<sup>{$ordered[$key]['pos']}</sup>";
		} else {
			$style = '';
			$pos = '';
		}
		
		
		$header[$key] = "<td order=\"{$key}\" {$style}>{$attribute->LabelPrefix}{$pos}</td>";
	}
}

$headerStr = implode('', $header);

$data = $this->getData();

$table = array();

$ids = array();

foreach ($data['items'] as $id=>$ac){
	$ids[$id]=$id;
	$row = array();
	$addRows = array();
	$s = str_replace('_#ID#_', $id, $sel);
	
	if($cbAction){
		$s = $cbAction->getCtrlForId($id);
	}
	
	$row[]="$s <a href='{$editLink1}EditId={$id}&amp;{$editLink2}'>Edit</a>";

	
	
	foreach ($ac as $gid=>$group){
		if($group->IsTable) {
			unset($tmpRow);
			foreach ($group->Table as $sub_id=>$SubGroup){
				if(is_null($tmpRow)){
					$tmpRow =&$row;
				} else {
					$tmpRow =&$addRows[$sub_id];
				}
				
				foreach ($SubGroup as $aid=>$attribute){
					$tmpRow[$gid.'_'.$aid] = $attribute->getBEListValue();
				}
			}
		} else {
			foreach ($group as $aid=>$attribute){
				$row[$gid.'_'.$aid] = $attribute->getBEListValue();
			}
		}
	}
	$table[$id] = $row;
	foreach ($addRows as $sub_id=>$row) $table[$id.'_'.$sub_id] = $row;
}

$BtnBar = $this->RenderBtnBar($ExpandControls, $RenderValues, $prefix);

ob_start();

	

?>
<div id="<?=$this->FormElementId?>"  ViewID="<?=$this->FormElementId?>">
<input type="hidden" id="<?=$this->FormElementId.BaseView::ID_SEPARATOR.'page'?>" name="<?=$this->FormElementId.BaseView::ID_SEPARATOR.'page'?>" value="<?=$_REQUEST[$this->FormElementId.BaseView::ID_SEPARATOR.'page']?>" />

<? include(dirname(__FILE__)."/PageBarPost.php");

	if($cbAction){
		echo $cbAction->getIdsCtrl();
	}
echo $BtnBar;
?>
<table class="list_table" ViewID="<?=$this->FormElementId?>" OrderActionName="<?=$this->OrderAction->FormElementName?>">
	<thead>
	<tr>
	<?=$headerStr?>
	</tr>
	</thead>
	<tbody>
<?php
$i=0;
foreach ($table as $row){
	$i++;
	$row_style=$i%2?"even":"odd";
	/*
	echo <<<EOD
			<tr class="{$row_style}" onmouseover="this.className='over'" onmouseout="this.className='{$row_style}'">
EOD;
*/
	echo <<<EOD
			<tr>
EOD;

	foreach ($header as $k=>$v)
		echo "<td>{$row[$k]}</td>";
	echo "</tr>";
}
?>
	</tbody>
</table>
<?
echo $BtnBar;

include(dirname(__FILE__)."/PageBarPost.php"); ?>
</div>
<?php
return ob_get_clean();
	}
}



class SearchView extends BaseView {
	
	public $Cluster;
	
	
	function __construct($Id, $Label, AttributeCluster $Cluster){
		$this->Cluster = $Cluster;
		parent::__construct($Id, $Label);
	}
	
	function BuildChildControls(){
		$this->addAction(new ActionSearch('s', 'Search'));
		$this->addAction(new ActionClear('c', 'Clear'));
		$this->buildSearchForm();
	}
	
	
	function buildSearchForm(){
		$cluster = $this->Cluster;
		$row = $cluster->ClusterRow;
		$this->View->Label = $row['name'];
		if($row['search_template_cols']){
			$this->RenderColumnsCnt = $row['search_template_cols'];
		}
		if($row['search_template']){
			$this->Template = $row['search_template'];
		}
		
		

		foreach ($cluster->OnlyById as $gid=>$group){
			$controls = array();
			foreach ($group->OnlyById as $aid=>$attribute) {
				if($attribute->PHPData['BEListSearchable']){
					$control = $attribute->getSearchControl();
					$controls[] = $control;
				}
			}
			if(empty($controls)) continue;
			
			$view = new BaseView($gid, $group->Name);
			foreach ($controls as $control) $view->addControl($control);
			$this->addControl($view);
			
			//$row = getdb()->getRow("SELECT search_template, search_template_cols FROM attribute_groups WHERE id=?",array($gid));
			$row = $group->GroupRow;
			if($row['search_template']){
				$view->Template = $row['search_template'];
			}
			
			if($row['search_template_cols']){
				$view->RenderColumnsCnt = $row['search_template_cols'];
			} else {
				$view->RenderColumnsCnt = $this->RenderColumnsCnt;
			}
			
			$view->DisplayTitleToggle = true;
		}
		
		if(count($this->ChildViews)==1){
			$this->ChildViews[key($this->ChildViews)]->Label = '';
		}
		
		$this->DisplayTitleToggle = true;
	}
	
	
	function applySearch(){
		$v = $_SESSION[$this->FormElementId]['search'];
		if(!is_array($v)) return ;
			
		if($_SERVER['REQUEST_METHOD']=='GET'){
			$this->setValue($v);
		}
		
		foreach ($v as $gid=>$group){
			if(is_array($group) && $this->Cluster->OnlyById[$gid]){
			foreach ($group as $aid=>$value){
				$field = $this->Cluster->OnlyById[$gid]->OnlyById[$aid];
				if($field) {
					$field->setSearchVal($value);
				}
			}
			}
		}
	}
	
}


class ListView extends BaseView {
	
	public $WithSearchView = true;
	/**
	 * Enter description here...
	 *
	 * @var SearchView
	 */
	public $SearchView;
	/**
	 * Enter description here...
	 *
	 * @var TableView
	 */
	public $TableView;

	
	
	public $Cluster;
	
	function __construct($Id, $Label, AttributeCluster $Cluster){
		$this->Cluster = $Cluster;
		parent::__construct($Id, $Label);
	}
	
	function BuildChildControls(){
		if($this->WithSearchView){
			if(empty($this->SearchView)) {
				$this->SearchView = new SearchView('s', 'Search', $this->Cluster);
			}
			//$this->SearchView->Cluster = $this->Cluster;
			$this->addControl($this->SearchView);
			//$this->SearchView->buildSearchForm($this->Cluster);
		}
		
		
		if(empty($this->TableView)) $this->TableView =  new TableView('t', 'Table View', $this->Cluster);
		$this->addControl($this->TableView);
		
		if(CallManager::isCall()){
			$this->addAction(new ActionBack('back', Culture::Back));
		} else if($_GET['bkp']) {
			$this->addAction(new ActionBack('back', Culture::Back));
			$this->Actions['back']->BackURL = urldecode($_GET['bkp']);
		}
	}

	function PreRender(){
		if($this->WithSearchView) $this->SearchView->applySearch();
		parent::PreRender();
	}
	
}




?>