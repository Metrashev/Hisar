<?php


class CBONews extends ABOBase {
	
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'title'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'subtitle'=> array('type'=>'string', 'label'=>'Sub Title', 'size'=>255),
		'picture'=> array('type'=>'ManagedImage', 'label'=>'Picture', 'sizes'=>array('pic')),
		'due_date'=> array('type'=>'DateTime', 'label'=>'Publish Date'),
		'is_visible'=> array('type'=>'Boolean', 'label'=>'Visible'),
		'body'=> array('type'=>'HTML', 'label'=>'body', 'size'=>65535),
		'cid'=> array('type'=>'BORef', 'label'=>'Service Type', 'table'=> 'categories'),
		
		'href'=> array('calculated'=>true, 'type'=>'struct', 'label'=>''),
		//'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array(__CLASS__, 'getDefultHref', array('cid','NewsId')), 'refField'=>'id')
		
	);

	
	public $tableName = 'news_pages';
	
	
	function calculateList($data, $fieldsToCalc){
		if(empty($data)) return $data;
		
		$data = parent::calculateList($data, $fieldsToCalc);
		
		if($fieldsToCalc['href'])
			foreach ($data as &$row)
				$row['href'] = "?cid={$row['cid']}&amp;NewsId=".$row['id'];


		return $data;
	}

	
	function getFEConstrains(){
		return 'is_visible=1 AND due_date <= now()';
	}

}




class CFENewsPage implements IFEPage {
	const newsVarName = 'NewsId';
	
	function __construct($node, $request){
		$bo = new CBONews();
		if(isset($request[self::newsVarName])){
		  $id = (int)$request[self::newsVarName];
		  $row = $bo->getRow('*',"id=$id");
		  if(empty($row))
		    throw new EPageNotFound();
		
		  $this->renderer = new CFENewsFullPage($row, $node);
		} else if (isset($request['spid'])) {
		  $this->renderer = new CFEStaticPage($node, $request);
		} else {
		  $this->renderer = new CFENewsListPage($node, $request);
		}
	}

	function getBodyHTML() {
		return $this->renderer->getBodyHTML();
	}
}


class CFENewsListPage {

	function __construct($node, $request){
		$this->itemsPerPage = $node['php_data']['parameters']['ItemsPerPage'];
		$this->node = $node;
		$this->template = $GLOBALS['CONFIG']['CFENewsPage']['templates'][$node['template_id']]['fileList'];
	}

	function getBodyHTML() {
		$bo =new CBONews();
		$data = $bo->getPagedList("/?cid=".$this->node['id'], $this->itemsPerPage, 'id,title,subtitle,picture,due_date,href,cid', "cid=".(int)$this->node['id'], "due_date DESC");
		$data['node'] = $this->node;
		
		return ob_include($this->template, $data);
	}
}

class CFENewsFullPage {

	function __construct($data, $node){
		$this->data = $data;
		$this->data['node'] = $node;
		$this->template = $GLOBALS['CONFIG']['CFENewsPage']['templates'][$node['template_id']]['fileFull'];
	}
	
	function getBodyHTML() {
		return ob_include($this->template, $this->data);
	}
}


?>