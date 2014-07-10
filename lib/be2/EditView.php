<?php

class ActionSave extends BaseAction {
	
	public $SaveData;
	public $Errors;
	//public $PHPCode;

	function doAction(){
		$editView = $this->View;
		$cluster = $this->View->Cluster;

		$this->SaveData = $editView->getValue();

		$this->Errors = $this->validate($this->SaveData);
		
		if(empty($this->Errors)){
			$master_id = (int)$_GET['master_id'];
			$master_attribute_cluster_id = (int)$_GET['master_ac_id'];
			
			if($master_id){
				$this->SaveData[$cluster->MainGroupId]['master_id'] = $master_id;
				$this->SaveData[$cluster->MainGroupId]['master_attribute_cluster_id'] = $master_attribute_cluster_id;
			}
			
			$cid = (int)$_GET['cid'];
			
			if($cid && !$this->View->product_id){
				/* Za da imame $_GET['cid'] Triabva da sme doshli ot meniuto ot liavo. Taka shte pravim samo za clusteri koito sa samo v 1 node ot vseki ezik.*/
				$this->SaveData[$cluster->MainGroupId]['cids'] = getdb()->GetOne("SELECT GROUP_CONCAT(id) FROM categories WHERE attribute_cluster_id={$cluster->AttributeClusterId}");
				//$data[$cluster->MainGroupId]['cids'] = $cid;
			}
			
			$this->OnBeforeDBSave(); // Moze da promeni $this->Errors & $this->SaveData
		}
		
		
		
		if(empty($this->Errors)){

			$id = $cluster->DBSave($this->SaveData, $this->View->product_id);
			
			$_SESSION['WARNINGS'] = <<<EOD
<div class="OKMessage">Record successfully saved!</div>
EOD;

			// TODO Ami ako sa 2 EditViews na edno miasto i imame insert ???
			$_GET['EditId'] = $id;
			header('Location: ?'.http_build_query($_GET,null,'&'));
			exit();

		
			if(CallManager::isCall()){
				CallManager::goBack();
			}
			
		} else {
			$this->View->HeaderMessage = "<div class='ErrorMessage'>".implode('<br />', $this->Errors)."</div>";
		}
	}
	
	function OnBeforeDBSave(){
		/*
		if($this->PHPCode){
			$data = &$this->SaveData;
			$errors = &$this->Errors;
			eval($this->PHPCode);
		}
		*/
	}
	

	function validate(&$data){
		$errors = array();
		$cluster = $this->View->Cluster;
		foreach ($cluster as $gid=>$group){
			
			if($group->IsTable){
				$OrderFields = array();
				$MaxOrderNum = count($data[$gid]) - 1;
				foreach ($data[$gid] as $sub_id=>$null){

					if($sub_id==='z' && isEmptyArray($null)) {
						unset($data[$gid][$sub_id]);
						continue;
					}
					
					foreach ($group as $aid=>$attribute){
						if(!$attribute->PHPData['BEEditVisible'] || $attribute->PHPData['BEEditReadOnly']) continue;
						if($gid==21 && $aid==71){
							
						}
						$tmp = $attribute->Validate($data[$gid][$sub_id][$aid]);
						if(!empty($tmp)){
							$tmp = implode('<br />', $tmp);
							$control = $this->View->ChildViews[$gid]->ChildViews[$sub_id]->Controls[$aid];
							
							$JSId = $control->getDefaultControlJSID();
							$control->isValid = false;
							$control->isEmpty = $attribute->isEmpty($data[$gid][$sub_id][$aid]);
							$tmp = <<<EOD
	<a href="JavaScript:ITTI.focusControl('$JSId')">$tmp</a>
EOD;
							$errors[] = $tmp;
						}
					}
					if($sub_id==='z'){
						$tmp = $data[$gid][$sub_id];
						unset($data[$gid][$sub_id]);
						$data[$gid][] = $tmp;
					}
					
					if(!empty($null['ord']) && !preg_match('/^[0-9]+(\.[0-9]*)?$/', $null['ord'])){
						$errors[] = <<<EOD
	<a href="JavaScript:ITTI.focusControl('{$this->View->ChildViews[$gid]->ChildViews[$sub_id]->Controls['ord']->getDefaultControlJSID()}')">Invalid Int value for Order</a>
EOD;
						continue;
					}
					
					if($null['ord']>$MaxOrderNum){
						
						$errors[] = <<<EOD
	<a href="JavaScript:ITTI.focusControl('{$this->View->ChildViews[$gid]->ChildViews[$sub_id]->Controls['ord']->getDefaultControlJSID()}')">Invalid value for Order max value is {$MaxOrderNum} </a>
EOD;
						continue;
					}
					
					if(empty($null['ord'])) continue; 
					
					$OrderFields[$sub_id]=(int)$null['ord'];
					
				}

				if(count($OrderFields)<>count(array_unique($OrderFields))){
					$errors[] = <<<EOD
	<a href="JavaScript:ITTI.focusControl('{$this->View->ChildViews[$gid]->ChildViews[0]->Controls['ord']->getDefaultControlJSID()}')">Duplicated Order Positions</a>
EOD;
				}

			} else {
				foreach ($group as $aid=>$attribute){
					if(!$attribute->PHPData['BEEditVisible'] || $attribute->PHPData['BEEditReadOnly']) continue;
					$tmp = $attribute->Validate($data[$gid][$aid]);
					if(!empty($tmp)){
						$tmp = implode('<br />', $tmp);
						$control = $this->View->ChildViews[$gid]->Controls[$aid];
						$JSId = $control->getDefaultControlJSID();
						$control->isValid = false;
						$control->isEmpty = $attribute->isEmpty($data[$gid][$aid]);
						$tmp = <<<EOD
<a href="JavaScript:ITTI.focusControl('$JSId')">$tmp</a>
EOD;
						$errors[] = $tmp;
					}
				}
			}
		}
		return $errors;
	}
}

class ActionDelSubId extends BaseAction {
	public $Cluster;
	public $product_id;
	public $group_id;
	public $sub_id;
	
	function doAction(){
		$this->Cluster->OnlyById[$this->group_id]->DBDeleteSubId($this->product_id, $this->sub_id);
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
}

class ActionDelelete extends BaseAction {
	public $Cluster;
	
	function doAction(){
		$this->Cluster->DBDelete($this->View->product_id);
		if($this->View->Actions['back']){
			$this->View->Actions['back']->doAction();
		} else {
			header("Location: ./");
			exit();
		}

	}
}

class TableRepView extends BaseView {
	
	public $TemplateView = null;
	public $RowsCnt = 1;

	function Render(){
		$ReplaceIds=true; $ReplaceLabels=true; $ExpandViews=true; $ExpandControls=true; $RenderValues=true; $prefix = '$View';
		$ViewJsId = $ReplaceIds ? $this->getDefaultControlJSID() : "<?={$prefix}->getDefaultControlJSID(); ?>";

		$BtnBar = $this->RenderBtnBar($ExpandControls,$RenderValues,$prefix);

		$ViewLabel = $ReplaceLabels ? $this->getLabel() : "<?={$prefix}->getLabel(); ?>";

		$ViewsHTML = '';
		$HiddenHTML = '';

		$header = array();
		foreach ($this->TemplateView->Controls as $id=>$control) {
			$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->TemplateView->Controls['{$id}']->getLabel(); ?>";
			$header[] = $label;
		}
		foreach ($this->TemplateView->Actions as $id=>$control) {
			$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->TemplateView->Controls['{$id}']->getLabel(); ?>";
			$header[] = $label;
		}
		
		$table = array();
		$table[0] = $header;

		$sub_id = 0;
		foreach ($this->ChildViews as $View) {
			$cells = array();
			foreach ($View->Controls as $id=>$control) {
				if($control instanceof BaseView){
					$tpl = $ExpandViews ? $control->BuildViewTemplate($ReplaceIds, $ReplaceLabels, $ExpandViews, $ExpandControls, $RenderValues, "{$prefix}->Controls['{$id}']") : "<?={$prefix}->Controls['{$id}']->render(); ?>";
	
	
					$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
					$jsId = $ReplaceIds ? $control->getDefaultControlJSID() : "<?={$prefix}->Controls['{$id}']->getDefaultControlJSID(); ?>";
					$cells[] = $tpl;
	
	
				} else if($control instanceof ControlInputHidden ){
					$HiddenHTML .= $ExpandControls ? $control->buildTemplate("{$prefix}->Controls['{$id}']", $RenderValues) : "<?={$prefix}->Controls['{$id}']->render(); ?>";
				} else {
					$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
					$jsId = $ReplaceIds ? $control->getDefaultControlJSID() : "<?={$prefix}->Controls['{$id}']->getDefaultControlJSID(); ?>";
					$tpl = $ExpandControls ? $control->buildTemplate("{$prefix}->Controls['{$id}']", $RenderValues) : "<?={$prefix}->Controls['{$id}']->render(); ?>";
					
					$cells[] = $tpl;
				}
			}
			foreach ($View->Actions as $id=>$control) {
				$control->Value = $sub_id;
				
					$label = $ReplaceLabels ? $control->getLabel() : "<?={$prefix}->Controls['{$id}']->getLabel(); ?>";
					$jsId = $ReplaceIds ? $control->getDefaultControlJSID() : "<?={$prefix}->Controls['{$id}']->getDefaultControlJSID(); ?>";
					$tpl = $ExpandControls ? $control->buildTemplate("{$prefix}->Controls['{$id}']", $RenderValues) : "<?={$prefix}->Controls['{$id}']->render(); ?>";
					
					$cells[] = $tpl;
			}
			
			$table[$View->FormElementId] = $cells;
			$sub_id++;
		}
		/*
		// Veche niama nuzda zashtoto Prepravih BaseAction::getActionJS() da vzima $this->MainView->FormElementId vmesto $this->View->FormElementId
		foreach (array_keys($table) as $id){
			$HTML .= "<div id='{$id}'></div>";  // Tova sa conteineri za actions ot vsichki child views
		}
		*/
		//$table = arrayToTableStyle($table);
		$table = arrayToTable($table);
		
		

		
		$HTML .= <<<EOD
<div id="{$ViewJsId}" ViewID="{$ViewJsId}" class="View">
<div class="ViewTitle">{$ViewLabel}</div>
{$BtnBar}
$HiddenHTML
{$this->HeaderMessage}
<table>
$table
</table>
{$BtnBar}
</div>
EOD;

		if(empty($this->View)){

			$HTML .= <<<EOD
</form>
</body>
EOD;
		}

		return $HTML;
	}
	
	function BuildViewTemplate($ReplaceIds=false, $ReplaceLabels=false, $ExpandViews=false, $ExpandControls=false, $RenderValues=False, $prefix = '$this'){
		return $RenderValues ? $this->Render() : "<?={$prefix}->render();?>"; 
	}
}

class EditView extends BaseView {
	/**
	 * Enter description here...
	 *
	 * @var AttributeCluster
	 */
	public $Cluster;
	public $ActionDelelete;
	public $ActionSave;
	public $ActionSaveCN='ActionSave';
	
	public $product_id;
	
	function __construct($Id, $Label, AttributeCluster $Cluster){
		$this->Cluster = $Cluster;
		parent::__construct($Id, $Label);
	}
	
	
	function BuildChildControls(){
		$this->areChildControlsCreated = true; //Veri important!!!

		$this->RenderChildsInTabs = true;
		
		if($this->ActionSaveCN){
			$ActionSaveCN = $this->ActionSaveCN;
			$this->ActionSave = new $ActionSaveCN('save', Culture::Save);
		} else {
			$this->ActionSave = new ActionSave('save', Culture::Save);
		}
		
		$this->addAction($this->ActionSave);
		
		
		
		if(CallManager::isCall()){
			$this->addAction(new ActionBack('back', Culture::Back));
		} else if($_GET['bkp']) {

			$this->addAction(new ActionBack('back', Culture::Back));
			$this->Actions['back']->BackURL = urldecode($_GET['bkp']);
		}
		
		
		$this->ActionDelelete = new ActionDelelete('del', 'Delete');
		$this->ActionDelelete->Cluster = $this->Cluster;
		$this->addAction($this->ActionDelelete);
	
		$this->buildEditForm();
		
		$this->HeaderMessage = $_SESSION['WARNINGS'];
		$_SESSION['WARNINGS'] = '';
	}
	
	
	function LoadDataFromModel($id){
		$id = (int)$id;
		$this->product_id = $id;
/*
		$this->Cluster->MainGroup['id']->addWhere("value=$id");
		$data = $this->Cluster->getAllProducts(1);

		foreach ($data as $data); // Malko magichen ekvivalent na reser zaradi ProductsArray extends ArrayIterator 
*/
		$data = $this->Cluster->getProductById($id);
		if(count($data)==0){
			
			$data = $this->Cluster;
		}
		foreach ($data as $gid=>$group){

			if($group->IsTable) {
				$Template = $this->ChildViews[$gid]->TemplateView;
				foreach ($group->Table as $sub_id=>$tmp){
					$subView = clone $Template;
					$subView->Id = $sub_id;
					$subView->Label = "Row ".$sub_id;
					
					$subView->Actions['delRow']->sub_id = $sub_id;
					$subView->Actions['delRow']->product_id = $id;
					$subView->Actions['delRow']->group_id = $gid;
					$subView->Actions['delRow']->Cluster = $this->Cluster;

					$this->ChildViews[$gid]->addControl($subView);
					$subView->Init();
					
					foreach ($tmp->OnlyById as $aid=>$attribute) {
						if(!$attribute->PHPData['BEEditVisible']) continue;
						$val = $attribute->getBEEditValue();
						$subView->Controls[$aid]->setValue($val);
					}
				}
				$subView = clone $Template;
				
				unset($subView->Controls['ord']);
				unset($subView->Actions['delRow']);
				$this->ChildViews[$gid]->addControl($subView);
				$subView->Init();
			} else {
				foreach ($group as $aid=>$attribute){
					if(!$attribute->PHPData['BEEditVisible']) continue;
					$val = $attribute->getBEEditValue();
					$this->ChildViews[$gid]->Controls[$aid]->setValue($val);
				}
			}
		}
		

		if($id) $this->buildChildListViews($id);
		
		if(count($this->ChildViews)==1){
			$this->ChildViews[key($this->ChildViews)]->Label = '';
		}
	}
	
	
	function buildChildListViews($id){
		$row = $this->Cluster->ClusterRow;
		if($row['product_group_ids']){
			$row = explode(',',$row['product_group_ids']);

			foreach ($row as $acId){
				$lv = new ListView($this->Id.'L'.$acId, 'Child List', AttributeCluster::byId($acId));

				//$lv->WithSearchView = false;
				$lv->Cluster->MainGroup['master_id']->AddWhere("value=$id");
				$lv->Cluster->MainGroup['master_attribute_cluster_id']->AddWhere("value={$this->Cluster->AttributeClusterId}");
				$this->addControl($lv);
				$lv->Init();
				$lv->TableView->EditLinkParams['master_id'] = $id;
				$lv->TableView->EditLinkParams['master_ac_id'] = $this->Cluster->AttributeClusterId;

				unset($lv->TableView->EditLinkParams['cid']);
				
				unset($lv->Actions['back']);
			}
		}
	}
	
	function buildEditForm(){
		$row = $this->Cluster->ClusterRow;
		$this->Label = $row['name']." - ".Culture::Edit ;
		if($row['template_cols']){
			$this->RenderColumnsCnt = $row['template_cols'];
		}
		if($row['template']){
			$this->Template = $row['template'];
		}


		$cluster=$this->Cluster;
		foreach ($cluster->OnlyById as $gid=>$group){
			
			if($group->IsTable) {
				$view = new TableRepView($gid, $group->Name);
				
				$subView = new BaseView('z', $group->Name." [z]");
				foreach ($group->OnlyById as $aid=>$attribute) {
				
					if($attribute->PHPData['BEEditVisible']){
						$ctr = $attribute->getEditControl();
						$ctr->setReadOnly($attribute->PHPData['BEEditReadOnly']);
						if(!$attribute->PHPData['BEEditReadOnly'] && $attribute->PHPData['BEEditRequired']){
							$ctr->Required = true;
						}
						
						$subView->addControl($ctr);	
					}
				}
				if(empty($subView->Controls)) continue;
				$subView->addControl(new ControlTextInput('ord','Order', 'size="3"'));
				$subView->addAction(new ActionDelSubId('delRow','Del'));
				
				
				$view->TemplateView = $subView;
				//$view->addControl($subView);

				
			} else {
				$view = new BaseView($gid, $group->Name);
				foreach ($group->OnlyById as $aid=>$attribute) {
					
					if($attribute->PHPData['BEEditVisible']){
						$ctr = $attribute->getEditControl();
						$ctr->setReadOnly($attribute->PHPData['BEEditReadOnly']);
						$view->addControl($ctr);
	
					}
				}
				if(empty($view->Controls)) continue;
				//$row = getdb()->getRow("SELECT php_code, template, template_cols FROM attribute_groups WHERE id=?",array($gid));
				$row = $group->GroupRow;
				if($row['template']){
					$view->Template = $row['template'];
				}
				if($row['template_cols']){
					$view->RenderColumnsCnt = $row['template_cols'];
				} else {
					$view->RenderColumnsCnt = $this->RenderColumnsCnt;
				}
			}
			$this->addControl($view);
		}
		

			
		if($cid = (int)$_GET['cid']){
			/* Za da imame $_GET['cid'] Triabva da sme doshli ot meniuto ot liavo. Taka shte pravim samo za clusteri koito sa samo v 1 node ot vseki ezik.*/
			$cidAid = $cluster->MainGroup['cids']->Id;
			$this->ChildViews[$cluster->MainGroupId]->Controls[$cidAid]->setReadOnly(true);
		}
	}
	
	
	function Run($in_edit_id){
		$this->Init();
		
		$this->LoadDataFromModel($in_edit_id);
		
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$this->LoadDataFromRequest();
		}
	
		$Action = $this->getAction();
		if($Action) $Action->doAction();
		
	
		return $this->Render();
	}
	

}

?>