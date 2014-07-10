<?php

class CBOTeam extends ABOBase {
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'name_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'name_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'position'=> array('type'=>'string', 'label'=>'Позиция', 'size'=>255, 'lng'=>1),
		'position_bg'=> array('type'=>'string', 'label'=>'Позиция', 'size'=>255),
		'position_en'=> array('type'=>'string', 'label'=>'Position', 'size'=>255),
		'email'=> array('type'=>'email', 'label'=>'email', 'size'=>255),
		'phone'=> array('type'=>'string', 'label'=>'phone', 'size'=>255),
		'picture'=> array('type'=>'UnmanagedFile', 'label'=>'Picture', 'size'=>255),
		'cv'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535, 'lng'=>1),
		'cv_bg'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'cv_en'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'created_date'=> array('type'=>'DateTime', 'label'=>'created date'),
		'updated_date'=> array('type'=>'DateTime', 'label'=>'updated date')
	);

	public $tableName = 'team';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>6,
		'labelS'=>'Екип',
	),
	'en' => array(
		'cid'=>18,
		'labelS'=>'Team',
	));
}

?>