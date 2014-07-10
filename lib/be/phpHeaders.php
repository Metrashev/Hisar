<?php


define("isPostback",$_SERVER['REQUEST_METHOD']=='POST');

	session_start();
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: private");
    header('Content-type: text/html; charset=UTF-8');	
    


/*	function __autoload($funcName) {
		if(!$funcName)
			return;
		$f=array(
			
			'Profiler'=>'Profiler.php',
			'ErrorException'=>'../ErrorHandling.php',
			'ErrorsManager'=>'../ErrorHandling.php',
		
			'BE_Utils'=>'fe_utils.php',
			'FE_Utils'=>'fe_utils.php',
			'DB_Utils'=>'fe_utils.php',
			'pPrado'=>'fe_utils.php',
			'CGallery'=>'fe_utils.php',
			'CCommon'=>'fe_utils.php',
			'CNews'=>'fe_utils.php',
			'CStaticPage'=>'fe_utils.php',
			'Application'=>'fe_utils.php',
			'CFormatedDate'=>'fe_utils.php',
			'CPictures'=>'fe_utils.php',
			'CLanguage'=>'CLanguages.php',
			'CSearch'=>'CSearch.php',
			'SearchUtils'=>'search_utils.php',
			
			'CPageBar'=>'Controls.php',
			'Repeater'=>'ControlsEx.php',
			
			'UT_DBaseUtils'=>'UT_utils.php',
			'UT_userfunctions'=>'UT_utils.php',
			'UT_ListsProvider'=>'UT_utils.php',
			
			'FormNode'=>'FormProcessor5.php',
			'ButonNode'=>'FormProcessor5.php',
			'TextInputNode'=>'FormProcessor5.php',
			'TextAreaNode'=>'FormProcessor5.php',
			'SelectNode'=>'FormProcessor5.php',
			'CheckBoxNode'=>'FormProcessor5.php',
			'RadioButtonNode'=>'FormProcessor5.php',
			'FormProcessor'=>'FormProcessor5.php',
			
			'SearchBuilder'=>'SearchBuilder.php',
			
			'FormBuilder'=>'FormBuilder.php',
			'DomParser'=>'FormBuilder.php',
			
			'Navigation'=>'lib1.php',
			'xml'=>'lib1.php',
			'CLib'=>'lib1.php',
			'CValidation'=>'lib1.php',
			
			'COrder'=>'order.php',
			
			'TableView'=>'TableView.php',
			
			'SingleSelectTableView'=>'SingleSelectTableView.php',
			'MultiSelectTableView'=>'MultiSelectTableView.php',
			
			'CAdverts'=>'ads.php',
			
			'CURLTree'=>'tree.php',
			'CTree'=>'tree.php',
			
			'CForm_Utils'=>'table_views.php',
			

		);
		
		if(is_file(dirname(__FILE__).'/'.$f[$funcName]))
			require_once(dirname(__FILE__).'/'.$f[$funcName]);
		else
			echo "Failed to open <b>".dirname(__FILE__).'/'.$f[$funcName]."</b> called for class <b>{$funcName}</b>";
	}*/

?>