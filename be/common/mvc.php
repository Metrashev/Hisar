<?php

class MVC {
	
	public $table;	
	public $session_filter;
	/**
	 * @var CSearchControler
	 */	
	public $search_controler;
	/**
	 * @var CDataGridControler
	 */	
	public $dg;
	
	public $del_var;
	public $filter;
	public $__editTable;
	
	public $fn_Delete;	
	public $is_multiform=false;
	
	public $workpath="";
	
	public $has_search_block=true;
	public $has_data_grid=true;
	
	/**
	 * Enter description here...
	 *
	 * @var IndexTemplate
	 */
	public $templateIndex;
	
	public $errors=array();
	
	function __construct($table,$filter,$__editTable="",$del_var="") {
		$this->table=$table;
		$this->filter=$filter;
		
		$this->__editTable=empty($__editTable)?$table:$__editTable;
		$this->del_var=empty($del_var)?"hdDelete".$table:$del_var;		
	}
	
	function initSearchControler() {
		$this->search_controler=new CSearchControler($this->table,$this->workpath,$this->filter);
		$this->search_controler->is_multiform=$this->is_multiform;				
	}
	
	function initDataGrid($pagesize=25,$pagebar_id="",$unique_id="") {
		$this->dg=new CDataGridControler($this->table,$this->workpath,$pagesize,$pagebar_id,$unique_id);
	}
	
	function autoprepare($filter_post_field="in_data",$search_field="search",$clear_field="btClear") {
		if($this->has_search_block) {
			$this->initSearchControler();
			$this->search_controler->autoprepare($filter_post_field,$search_field,$clear_field);
		}
		if($this->has_data_grid) {
			$this->initDataGrid();
			$this->dg->autoprepare($this->filter,$this->has_search_block?$this->search_controler->getSearch():array());
		}
		
		$this->initTemplate();
		$this->registerDelVar();
	}
	
	function initTemplate($search_field="search") {
		$this->templateIndex=new IndexTemplate(1);
		if($this->is_multiform) {
			$this->templateIndex->clear();
		}
		$this->registerDelVar();
		$this->templateIndex->hidden['use_'.$search_field]="<input type='hidden' name='use_{$search_field}' id='use_{$search_field}' value='{$_POST['use_'.$search_field]}'/>";		
	}
	
	function registerDelVar() {
		if(empty($this->del_var)) {
			return ;
		}
		if(!isset($this->templateIndex->hidden[$this->del_var])) {
			$this->templateIndex->hidden[$this->del_var]='<input type="hidden" name="'.$this->del_var.'" id="'.$this->del_var.'" value="0"/>';
		}
	}
	
	function processDelete() {
		if (!empty($_POST[$this->del_var])) {
			if (function_exists($this->fn_Delete)) {		
				$f=$this->fn_Delete;
				$a=$f($_POST[$this->del_var]);
				if (is_array($a)&&count($a)>0) {
					$this->errors=$a;
				}
			}
			else {				
				if(!empty($this->table)) {
					$db=getdb();
					$db->execute("delete from `{$this->table}` where id='{$_POST[$this->del_var]}'");
				}
			}
		}
	}
	
	function processSelect($select_button="btSelect") {
		if(isset($_POST[$select_button])) {
			$selected_set=$this->dg->dg->getControls();
			if(is_array($selected_set['_ch_sel_'])&&!empty($selected_set['_ch_sel_'])) {
				$keys=implode(',',array_keys($selected_set['_ch_sel_']));
				header("Location: ".$_GET['bkp']."&return_point={$_GET['return_point']}&selected_keys=".$keys);
				exit;
			}
		}
	}
	
	function render($skip_back_button=false) {
		$this->templateIndex->pref_table_text=FE_Utils::renderErrors($this->errors);
		
		return $this->templateIndex->openTemplate().
			($this->has_search_block?$this->search_controler->render(isset($_GET['search'])):"").
			($this->has_data_grid?$this->dg->render($skip_back_button):"").
			$this->templateIndex->closeTemplate();
	}
	
}

?>