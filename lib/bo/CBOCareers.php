<?php

class CBOCareers extends ABOBase {
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'name_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'name_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'picture'=> array('type'=>'ImgFile', 'label'=>'Picture', 'size'=>255),
		'description'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535, 'lng'=>1),
		'description_bg'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'description_en'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535)
	);

	public $tableName = 'careers';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>8,
		'labelP'=>'Кариери',
	),
	'en' => array(
		'cid'=>24,
		'labelP'=>'Careers',
	));
}

?>