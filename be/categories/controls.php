<?php

$con=array(
'controls'=>array(
     'value'=>
        array(
            'control'=>array('Label'=>"Value",'name'=>'in_data[value]','tagName'=>'Input','bound_field'=>'value','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,'required'=>true),
        ),
     'path'=>
        array(
            'control'=>array('Label'=>"Path",'name'=>'in_data[path]','tagName'=>'Input','bound_field'=>'path','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ), 
     'meta_keywords'=>
        array(
            'control'=>array('Label'=>"Keywords",'name'=>'in_data[meta_keywords]','tagName'=>'TextArea','bound_field'=>'meta_keywords','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ), 
     'meta_description'=>
        array(
            'control'=>array('Label'=>"Description",'name'=>'in_data[meta_description]','tagName'=>'TextArea','bound_field'=>'meta_description','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ),
     'tracker_code'=>
        array(
            'control'=>array('Label'=>"Tracker code",'name'=>'in_data[tracker_code]','tagName'=>'TextArea','bound_field'=>'tracker_code','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_TEXT,),
        ), 
     'page_title'=>
        array(
            'control'=>array('Label'=>"Page title",'name'=>'in_data[page_title]','tagName'=>'Input','bound_field'=>'page_title','userFunc'=>'','FormatString'=>'',),
            'write_data'=>array('type'=>DATA_VARCHAR,'required'=>false),
        ),
     'visible'=>
        array(
            'control'=>array('Label'=>"Visible",'name'=>'in_data[visible]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'visible','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
     'is_crumb_visible'=>
        array(
            'control'=>array('Label'=>"Crumb visible",'name'=>'in_data[is_crumb_visible]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'is_crumb_visible','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
     'is_title_visible'=>
        array(
            'control'=>array('Label'=>"title visible",'name'=>'in_data[is_title_visible]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'is_title_visible','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
     'is_page_restricted'=>
        array(
            'control'=>array('Label'=>"page restricted",'name'=>'in_data[is_page_restricted]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'is_page_restricted','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
        
     'use_in_search'=>
        array(
            'control'=>array('Label'=>"use in search",'name'=>'in_data[use_in_search]','tagName'=>'CheckBox','states'=>array('on'=>1,'off'=>0), 'bound_field'=>'use_in_search','userFunc'=>'','FormatString'=>'','attributes'=>array('type'=>'checkbox')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'language_id'=>
        array(
            'control'=>array('Label'=>"language id",'name'=>'in_data[language_id]','tagName'=>'Select','bound_field'=>'language_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$GLOBALS['CONFIG']['SiteLanguages']))),
            'write_data'=>array('type'=>DATA_VARCHAR,),
        ),
     'type_id'=>
        array(
            'control'=>array('Label'=>"type",'name'=>'in_data[type_id]','tagName'=>'Select','bound_field'=>'type_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$GLOBALS['CONFIG']['FEPageTypes'],'DataTextField'=>'name')),'attributes'=>array('onchange'=>'getForm(this).submit();')),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'skin_id'=>
        array(
            'control'=>array('Label'=>"skin",'name'=>'in_data[skin_id]','tagName'=>'Select','bound_field'=>'skin_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$skins,'DataTextField'=>'name','more'=>array('DataTextField'=>'name')))),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'template_id'=>
        array(
            'control'=>array('Label'=>"template",'name'=>'in_data[template_id]','tagName'=>'Select','bound_field'=>'template_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'arrayname','value'=>array('DataSource'=>$templates,'DataTextField'=>'name'))),
            'write_data'=>array('type'=>DATA_TINYINT,),
        ),
    'attribute_cluster_id'=>
        array(
            'control'=>array('Label'=>"Product",'name'=>'in_data[attribute_cluster_id]','tagName'=>'Select','bound_field'=>'attribute_cluster_id','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'sql','value'=>array("DataSource"=>"SELECT id,name FROM attribute_clusters ORDER BY name","addzero"=>array("key"=>0,"value"=>"","position"=>"top")))),
            'write_data'=>array('type'=>DATA_INT,),
        ),


    'img'=>array(   
     'control'=>array("Label"=>"img","name"=>"in_data[img]","isHTML"=>false,"tagName"=>"ManagedImage","bound_field"=>"img","userFunc"=>"","FormatString"=>"",
    	"parameters"=>array(
    		"table"=>"categories",
    		"field"=>"img",
    		"id"=>$_GET['id'],
    		"dir"=>$GLOBALS['MANAGED_FILE_DIR'],
    		"view_dir"=>$GLOBALS['MANAGED_FILE_DIR_IMG'],
    		'resize'=>false,'overwrite'=>false,
    		'sizes'=>array(
				'1'=>array(0,0,"view"),
			)
		)
	),
    'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),    'search_data'=>array("search_name"=>"categories.img","matchAllValue"=>"","cond"=>"like"    ),
    ),
   
    
),
    'template'=>array('dir'=>dirname(__FILE__).'/edit.tpl'),
);

?>