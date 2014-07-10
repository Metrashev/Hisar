<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("gallery");

$in_edit_table="gallery_head";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);

class MyDirectoryIterator extends DirectoryIterator {
    public function GetExtension() {
        $Filename = $this->GetFilename();
        $ext=(string)FE_Utils::getFileExt($Filename);
        return strtolower($ext);        
    }
}

function browseDir($dir,$sizes,$gallery_id,$cid=1) {
	
	$cfg['allowed_extensions']=array(
		".jpg",".png",".gif"	
	);
	
	$path=dirname(__FILE__)."/../..".$dir;
	
	if(!is_dir($path)) {
		return array("Invalid path");
	}
	$im = new MyDirectoryIterator($path);
	$dst=dirname(__FILE__)."/../../files/mf/gallery/";
	$db=getdb();
	$counter=0;
	foreach ($im as $img){
		// Ако е фаил, и има подходящо разширение;
		$name=$img->getPathname();
		$ext=CPictures::getImageExtension($name);
		
		if (is_file($name) && in_array($ext,$cfg['allowed_extensions'])) { 
			$has_pic=false;
			foreach ($sizes as $k=>$v) {
				if("{$v['size']}"!=""&&"{$v['width']}"!=""&&"{$v['height']}"!="") {
					$has_pic=true;
					break;
				}				
			}
			if(!$has_pic) {
				continue;
			}
			
			$db->Execute("insert into gallery (cid,page_id,img) values(?,?,?)",
				array($cid,$gallery_id,$ext)
			);
			$gid=$db->get_id();
			$co=new COrder("gallery","order_field","cid='{$cid}' and page_id='{$gallery_id}'");
			$co->set_item_order($gid,0);
			$counter++;
			
			foreach ($sizes as $k=>$v) {
				if("{$v['size']}"!=""&&"{$v['width']}"!=""&&"{$v['height']}"!="") {
					CPictures::createTumbnail('',$name,$dst."{$gid}_img_{$k}{$ext}",(int)$v["width"],$v["height"]);
				}
			}			
		}
	}
	return array("<b>{$counter}</b> files loaded");
}


?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>
<?=BE_Utils::loadTinyMce('test1',"sp.tpl");?>
<script>
function myFileBrowser(field_name, url, type, win) {
	
	/*var cmsURL = window.location.toString();    // script URL - use an absolute path!
    if (cmsURL.indexOf("?") < 0) {
        //add the type as the only query parameter
        cmsURL = cmsURL + "?type=" + type;
    }
    else {
        //add the type as an additional query parameter
        // (PHP session ID is now included if there is one at all)
        cmsURL = cmsURL + "&type=" + type;
    }*/

	var c_url="http://"+window.location.host+"/be/tiny_mce/plugins/";
	
	tinyMCE.activeEditor.windowManager.my_params={window : win,input : field_name};
	switch(type) {
		case "image": {
			c_url+="imageManager";
		    break;
		}
		default: {
			c_url+="fileman";
			
		    break;
		}		
	} 
	
	tinyMCE.activeEditor.windowManager.open({
	        file : c_url+'/popup.php?selector=parent.opener.insertMyImage("__",',
	        title : 'File Browser',
	        width : 800,  // Your dimensions may differ - toy around with them!
	        height : 600,
	        resizable : "yes",
	        inline : "no",  // This parameter only has an effect if you use the inlinepopups plugin!
	        close_previous : "no",
	        scrollbars: 'yes'
	    }, {
	        window : win,
	        input : field_name
	    });
	
    return false;
}
</script>
</head>
<body >
<form id='f1' method=POST>
<table class="main_table" align="center">
<tr>
<td>

<?php

include(dirname(__FILE__).'/controls.php');
$con=getgallery_headControls();

$errors=array();

$errors=array();

if(isset($_POST['btLoad'])) {
	$wd=ControlValues::getWriteData($con,$_POST);	
	$ftp_dir=trim($_POST['ftp_dir']);
	if(empty($ftp_dir)) {
		$wd['errors'][]="Please, pick a folder";
	}
	if (empty($wd['errors'])) {
		$err=browseDir($_POST['ftp_dir'],$_POST['params'],$in_edit_id);
		if(is_array($err)) {
			$errors=$err;
		}
	}
	else {
		$errors=$wd['errors'];
	}
}

if(isset($_POST['btSave'])) {

	
	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	if (empty($wd['errors'])) {
		$wd['data']=array('name'=>$wd['data']['name']);
		$wd['data']['parameters']=serialize($_POST['params']);
		
		
		
/*
	$order=$wd['data']['order_field'];
	unset($wd['data']['order_field']);
	if(!$in_edit_id) {
		$order=(int)$order;
	}
*/
/*
			if(!$in_edit_id) {
				$wd['data']['created_date']=date('Y-m-d H:i:s');
				$wd['data']['created_by']=Users::getUserId();
			}
			$wd['data']['updated_date']=date('Y-m-d H:i:s');
			$wd['data']['updated_by']=Users::getUserId();
*/
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
			if(USE_AUDIT_LOG) {
				CUserLogs::logOperation($in_edit_table,$n_id,(int)$in_edit_id?OPERATION_UPDATE:OPERATION_ADD);
			}
//			$new_relation=CRelations::processRelationSave($_GET,$n_id);
//			if($new_relation===false) {
/*
				if((string)$order!="") {
	$co=new COrder($in_edit_table,"order_field");
	$co->set_item_order($n_id,$order);
}

*/

//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);

				header("Location: ".($_GET['bkp']));
				exit;		
//			}
//			if(is_array($new_relation)) {
//				$errors=$new_relation;
//			}
//			else {
//				header("Location: ".($_GET['bkp']));
//				exit;
//			}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
		@$array['params']=unserialize($array['in_data']['parameters']);
	}	
}
else {
	$array=$_POST;
}



if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);

//echo $hhh= $p->render();

//if($in_edit_id) {	
//	include(dirname(__FILE__).'/../_relations/list.php');		
//	include(dirname(__FILE__).'/../_relations/index.php');
//}

if(USE_AUDIT_LOG&&$in_edit_id) {
	echo CUserLogs::renderLastRow($in_edit_table,$in_edit_id);
}
echo "<input type='submit' />";
echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>