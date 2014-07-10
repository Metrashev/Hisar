<?php
function getgalleryControls($type='edit') {
	static $sizes=null;
	if(is_null($sizes)) {
		$sizes=array();
		if($_GET['cid']==1) {
			$s=getdb()->getone("select parameters from gallery_head where id=?",array($_GET['page_id']));
			@$s=unserialize($s);
			$sizes=array();
			
			if(empty($s)) {
				die('No home gallery!!!');
			}
			
			foreach ($s as $k=>$v) {
				if(empty($v['size'])) {
					continue;
				}
				$label=!((int)$v['width'])&&!((int)$v['height'])?$v['size']:"{$v['width']}x{$v['height']}";
				
				if($v['fit_out_window']) { $label .= '&nbsp;<span title="fit out" class="error">*</span>'; }
				
				$sizes[$k]=array((int)$v['width'], (int)$v['height'], $label, $v['fit_out_window']);
			}					
		}
		else {
			$db=getdb();
			$php_data=$db->getone("select php_data from categories where id='{$_GET['cid']}'");
			$php_data=unserialize($php_data);
			
			if(isset($php_data['parameters']['gallery'])&&is_array($php_data['parameters']['gallery'])) {
				foreach ($php_data['parameters']['gallery'] as $k=>$v) {
					if(trim($v['t'])!="") {
						$sizes[$k]=array($v['w'],$v['h'],$v['t'], false);
					}
				}			
			}
		}
		
	}
	
	$con=array(

'controls'=>array(
'img'=>array(
	'control'=>array("Label"=>"Picture","name"=>"in_data[img]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"img","userFunc"=>"","FormatString"=>"","parameters"=>array("table"=>"gallery","field"=>"img","id"=>$_GET['id'],"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
	"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'].'/','resize'=>true,'overwrite'=>true,'sizes'=>$sizes,
)),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'order_field'=>array(
	'control'=>array("Label"=>"Order","name"=>"in_data[order_field]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"order_field","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_INT,"required"=>false,),
),

'text'=>array(
	'control'=>array("Label"=>"Text","name"=>"in_data[text]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"text","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),
		'text_en'=>array(
				'control'=>array("Label"=>"Text","name"=>"in_data[text]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"text_en","userFunc"=>"","FormatString"=>""),
				'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
		)
	)
);

if($type=='search') {
    	$con['template']=array('dir'=>dirname(__FILE__).'/search.tpl');
    }
else {
	$con['template']=array('dir'=>dirname(__FILE__).'/edit.tpl');
	$col=getdb()->getcol("show columns from gallery");	
	$r_c=array();
	foreach ($col as $k=>$v) {
		if(strpos($v,"text_")===0) {
			$lng=substr($v,5);
			if($GLOBALS['CONFIG']['SiteLanguages'][$lng])
			{
				$con['controls'][$v]=array(
					'control'=>array("Label"=>"Text({$lng})","name"=>"in_data[$v]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"$v","userFunc"=>"","FormatString"=>""),
					'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,)
				);
				$r_c[$v]="Text ({$lng})";
			}
		}
	}
	if(!empty($r_c)) {
		$GLOBALS['_LANGUAGE_COLS']=$r_c;
	}	
	
}
    return $con;
}

?>