<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("categories");
$id = (int)$_REQUEST['id'];

$in_edit_table="categories";
$in_edit_id=$id;

if($id<1) die("Invalid ID!");
//echo "ID=".$id;
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>
<link rel="stylesheet" href="<?=BE_CSS_DIR;?>lib.css">

<script>
function confDelete() {
    return true;
}
</script>

</head>

<body >
<form id='f1' method=POST enctype="multipart/form-data">
<table class="main_table" align="center">
<tr>
<td>

<?php

if ($_SERVER['REQUEST_METHOD']=='GET') {
    if (isset($_GET['id'])) {
        $db=getdb();
        $array['in_data']=$db->getRow("select * from categories where id='{$_GET['id']}'");
        @$data=unserialize($array['in_data']['php_data']);
        $data=isset($data['parameters'])?$data['parameters']:array();
    } else {
    	$data['is_title_visible'] = 1;
    }
}
else {
    $array=$_POST;
    $data=$_POST['data'];
}


$skins= array(0=>'');
foreach ($GLOBALS['CONFIG']['Skins'] as $k=>$v) {
	$skins[$k]=$v['name'];
}



$type_id=(int)$array['in_data']['type_id'];
$class=$GLOBALS['CONFIG']['FEPageTypes'][$type_id]['class'];
if(!empty($class)) {
    $templates=isset($GLOBALS['CONFIG'][$class]['templates'])?$GLOBALS['CONFIG'][$class]['templates']:array();
}

if(is_array($GLOBALS['CONFIG'][$class]['be']['delete'])) {
    if(isset($GLOBALS['CONFIG'][$class]['be']['delete']['file'])) {
        require_once(dirname(__FILE__).'/../../'.$GLOBALS['CONFIG'][$class]['be']['delete']['file']);
    }
    if(!empty($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['getMessage'])) {
        $GLOBALS['delete_message']=call_user_func($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['getMessage'],$id);
        if(empty($GLOBALS['delete_message'])) {
        	$GLOBALS['delete_message']="Are you sure?";
        }
    }
    else {
       $GLOBALS['delete_message']="Are you sure?";
    }
    if(!empty($GLOBALS['delete_message'])) {
        $GLOBALS['delete_message']="onclick='return window.confirm(\"{$GLOBALS['delete_message']}\");' ";
    }
}
else {
    $GLOBALS['delete_message']="onclick='return window.confirm(\"Are you sure?\");' ";
}

include(dirname(__FILE__)."/controls.php");

if( isset($_REQUEST['btDelete']) )
{
	$db=getdb();
	if(((int)$db->getone("select not_deletable from categories where id='{$id}'"))===0) {
	    if(!empty($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['process_delete'])) {
	        $GLOBALS['delete_message']=call_user_func($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['process_delete'],$id);
	    }
	    $Tree = new CURLTree("categories");
	    
		
		$SQL = "SELECT l, weight FROM categories WHERE id='$id'";
		$row = $db->getRow($SQL);
	
		
	    $l = (int)$row["l"];
	    $weight = (int)$row["weight"];
	    $right = $l + $weight;
		$SQL = "SELECT id FROM categories WHERE (l BETWEEN {$l} AND {$right})";
	  	$delete_ids = $db->getCol($SQL);
	  	
	  	foreach ($delete_ids as $del_id) {      
			ControlValues::deleteManagedImages($del_id,$con['controls'],false);			
	  	}
	    $Tree->delete_node($id);
	    header("Location: index.php");
	    exit;
	}
	else {
		echo "Cannot be deleted";
		echo "<br /><br />";
	}
}

function getParentLanguage($id) {
    $db=getdb();
    $row=$db->getrow("select pid,language_id from categories where id='{$id}'");
    while(empty($row['language_id'])&&$row['pid']) {
        $row=$db->getrow("select pid,language_id from categories where id='{$row['pid']}'");
        
    }
    return $row['language_id'];
}

function updateLanguage($id,$language_id) {
    $db=getdb();
    $lr=$db->getrow("select l,weight,language_id from categories where id='{$id}'");
    $old_lng=$lr['language_id'];
    if($old_lng==$language_id)
        return;
    $l=(int)$lr['l'];
    $r=$l+(int)$lr['weight'];
    $db->Execute("update categories set language_id='{$language_id}' where l>'{$l}' AND l<='{$r}'")  ;
}


$errors=array();
if(isset($_POST['btSave'])) {
	$db=getdb();
    if(!isset($_POST['in_data']['language_id'])) {
        $_POST['in_data']['language_id']=getParentLanguage($id);
    }
    else {
        updateLanguage($id,$_POST['in_data']['language_id']) ;
    }
    $wd=ControlValues::getWriteData($con,$_POST);    
    if(empty($wd['errors'])) {
    	if(!empty($wd['data']['path'])) {
    		if(strpos($wd['data']['path'],'?')!==false) {
    			$wd['errors'][]="Пътя не може да съдържа символа ?";
    		}
    		$c=(int)$db->getone("select count(*) from categories where id!=? and path=?",array($id,$wd['data']['path']));
    		if($c) {
    			$wd['errors']['path']="Посоченият път вече съществува";
    		}
    	}
    }
    if (empty($wd['errors'])) {
    	if(isset($GLOBALS['CONFIG'][$class]['be']['tree'])) {
     //   if(isset($_POST['data'])) {
            
			if(!is_array($_POST['data'])) {
				$_POST['data']=array();
			}
            $old_data=$db->getone("select php_data from categories where id='{$id}'");
            @$old_data=unserialize($old_data);
            
            $old_data['parameters']=$_POST['data'];
        
            $wd['data']['php_data']=serialize($old_data);
        }
        
        $id=ControlWriter::Write('categories',$wd['data'],$id);
        if(USE_AUDIT_LOG) {
			CUserLogs::logOperation($in_edit_table,$id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
		}
        $errors+=ControlValues::processManagedImages($id,$_FILES,$con['controls']);
        if(empty($errors)) {
	        //header("Location: index.php?node={$id}");
	        header("Location: ?id={$id}&msg=1");
	        exit;
        }
    }
    else {
        $errors=$wd['errors'];
    }
}






if(isset($GLOBALS['CONFIG'][$class]['be']['tree'])) {
    ob_start();
    $template_id=(int)$array['in_data']['template_id'];
    if(!is_array($GLOBALS['CONFIG'][$class]['be']['tree'][$template_id])) {
    	$template_id=0;
    }
    	
    foreach($GLOBALS['CONFIG'][$class]['be']['tree'][$template_id] as $filePlugin) {
    	include(dirname(__FILE__).'/../../'.$filePlugin);
    }
    
    $GLOBALS['tree_include_file']=ob_get_clean();
}

$GLOBALS['show_language_field']=$array['in_data']['level']==1;


echo "<!--   START    -->";
FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
    echo FE_Utils::renderErrors($errors);
    echo "<br />";
}
//$dg->renderEvents();
echo Master::create($con,dirname(__FILE__).'/edit.tpl',$array);

if(USE_AUDIT_LOG&&$in_edit_id) {
	echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);
}
echo "<!--   END    -->";

echo "</form></body></html>";

ob_end_flush();
?>