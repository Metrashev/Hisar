<?php

class CBOGallery extends ABOBase {
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'file_name'=> array('type'=>'ImgManaged', 'label'=>'Picture', 'size'=>255),
		'content_type'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'size'=> array('type'=>'int', 'label'=>'Name', 'size'=>11, 'signed'=>false),
		'cid'=> array('type'=>'BORef', 'label'=>'Service Type', 'table'=> 'categories'),
		'page_id'=> array('type'=>'BORef', 'label'=>'Service Type', 'table'=> 'categories'),
		
		'text'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'text_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'text_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		

		'order_field'=> array('type'=>'OrdField', 'label'=>'Order'),
		
		'normal_image'=> array('type'=>'Boolean', 'label'=>'Is top'),
		'small_image'=> array('type'=>'Boolean', 'label'=>'Is top'),
		
		'extension'=> array('type'=>'string', 'label'=>'Name', 'size'=>10),
		
		'small_img_src'=> array('calculated'=>true, 'type'=>'ImgSrc',  'size'=>255),
		'normal_img_src'=> array('calculated'=>true, 'type'=>'ImgSrc', 'size'=>255),

	);
	
	public $tableName = 'gallery';
	
	
	function calculateList($data, $fieldsToCalc){
		if(empty($data)) return $data;
		
		foreach ($data as &$row){
			$row['normal_img_src']=$GLOBALS['GALLERY_IMAGES_URL_PATH']."/pic{$row['cid']}_{$row['id']}{$row['extension']}";
			$row['small_img_src']=$GLOBALS['GALLERY_IMAGES_URL_PATH']."/tmbpic{$row['cid']}_{$row['id']}_tn{$row['extension']}";						
		}
		
		
		return $data;
	}
	
	
	function modifyQueryForCalc($fieldsToCalc, CSQLQueryBuilder $qb){
		if($fieldsToCalc['small_img_src'] || $fieldsToCalc['normal_img_src']){
			$qb->fields['cid'] = 'cid';
			$qb->fields['id'] = 'id';
			$qb->fields['extension'] = 'extension';
		}
		
		if($fieldsToCalc['small_img_src']){
			$qb->fields['small_image'] = 'small_image';
		}
		
		if($fieldsToCalc['normal_img_src']){
			$qb->fields['normal_image'] = 'normal_image';
		}
	}
	
	function getSubSelect($cid, $pid){
		$a = $GLOBALS['GALLERY_IMAGES_URL_PATH']."/tmbpic";
		$a = mysql_real_escape_string($a);
		return "(SELECT concat('$a', cid, '_', id, '_tn', extension) FROM ".$this->tableName." WHERE cid=$cid AND page_id = $pid ORDER BY order_field LIMIT 1)";
	}
	
}

?>
