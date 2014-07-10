<?php
class CBOGallery extends ABOBase {
	
	private $cid;
	
	public $fields = array(
		'img'=> array('type'=>'ManagedImage', 'label'=>'Picture', 'sizes'=>array(1,2,3)),
		'text'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>0, 'lngs'=>array( 'en'=>'')),
	);
	
	public $tableName = 'gallery';
	
	static function getHTML($data){
		if($data['node']['php_data']['parameters']['has_gallery'] && $data['gallery_head_id']){
			$bo = new CBOGallery();
			$data['gallery'] = $bo->getList('id,img,text',"cid=1 AND page_id=".$data['gallery_head_id'],'order_field');
			$template = $GLOBALS['CONFIG']['CFEGallery']['templates'][$data['node']['php_data']['parameters']['gallery_render_id']]['file'];
			include(dirname(__FILE__).'/../../'.$template);
		}
	}

}

class CFEGallery extends CFECustomPage {
	
	function getBodyHTML() {

		$cid = (int)$this->data['node']['id'];
		$bo = new CBOGallery();
		/*
		$param = $this->data['node']['php_data']['parameters']['gallery'];
		if($param[1]['t']!='') $bo->fields['img']['sizes'][]='1';
		if($param[2]['t']!='') $bo->fields['img']['sizes'][]='2';
		if($param[3]['t']!='') $bo->fields['img']['sizes'][]='3';
*/
		if(!empty($this->template['ItemsPerPage'])){
			$href = $_GET;
			unset($href['p']);
			$href = '/?'.http_build_query($href);
			$this->data += $bo->getPagedList($href, $this->template['ItemsPerPage'], 'id,img,text',"cid=$cid",'order_field');
			$this->data['gallery'] = $this->data['data_list'];
		} else {		
			$this->data['gallery'] = $bo->getList('id,img,text',"cid=$cid",'order_field');
		}

		return parent::getBodyHTML();
	}
}

?>