<?php
require_once(dirname(__FILE__). '/libCommon.php');
$be_img_dir = BE_IMG_DIR;
$be_dir = BE_DIR;
?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">



<!--  MENU SCRIPTOVE   -->
<style type="text/css">

a, div, td,span {
	font-family : verdana, arial;
}

.menuContainer {
	border:1px solid #999999;
	background:#ffffff;
	padding:1px;
}

body,html {
	height:100%;
}

a {

	color:#000000;	
	font-size:10px;
	font-weight:normal;
	text-decoration:none;
}

.selected {
	color:#D90F0F;
}

.a_level1 {
	font-weight:bold;
}

.a_level1_sel {
	color:#D90F0F;
	font-weight:bold;
}

.a_level2_sel {
	color:#D90F0F;
}

.subContainer {
	padding:5px;
	background:#e6e6e6;
	vertical-align:top;
	width:100%;
}

.subContainer td {
	vertical-align:top;
}

.hr {
	border-top:1px solid #ffffff;
	border-bottom:1px solid #999999;
	margin:5px 0px;
}

.menuOut, img {cursor:pointer; }
.menuOver, img {cursor:pointer; }
.submenu {padding-left:15px;}

</style>

<script>

last_selected=null;

function setMenuColor(obj) {
		
	if(!obj) {
		return;
	}
	
	if(obj.className=="") {
		return;
	}

	if(obj.className.indexOf("a_level1")>-1) {
		obj.className=obj.className.indexOf("_sel")>0?"a_level1":"a_level1_sel";
		
		return;
	}
	if(obj.className.indexOf("a_level2")>-1) {
		obj.className=obj.className.indexOf("_sel")>0?"a_level2":"a_level2_sel";
		return;
	}
	
}

function setSelected(obj) {
	setMenuColor(last_selected);
	setMenuColor(obj);
	last_selected=obj;
}

function SwitchMenu(obj){   
    var el = document.getElementById(obj);    
    var ar = document.getElementById("cont").getElementsByTagName("DIV");
        if(el.style.display == "none"){
    //        for (var i=0; i<ar.length; i++){
    //            ar[i].style.display = "none";
    //        }
            el.style.display = "block";
        }else{
            el.style.display = "none";
      }     
}

function OpenMenu(obj) {
    if(document.getElementById){
        var el = document.getElementById(obj);    
        el.style.display = "block";
    }
}

function OpenImg(img,sub_menu) {
    var obj=document.getElementById(img);
    obj.src="<?=BE_IMG_DIR;?>design/minus1.png";
}

function ChangeClass(menu, newClass) { 
	return;
     if (document.getElementById) { 
         document.getElementById(menu).className = newClass;
     } 
} 

function switchImg(img,sub_menu) {
    var elm=document.getElementById(sub_menu);
    var obj=document.getElementById(img);
    if(elm.style.display=='block') {
    	
    	if(obj.src.indexOf("plus1.png")>-1) {
        	obj.src="<?=BE_IMG_DIR;?>design/minus1.png";
    	}
    	else {
    		obj.src="<?=BE_IMG_DIR;?>design/minus2.png";
    	}
    }
    else {
    	if(obj.src.indexOf("minus1.png")>-1) {
        	obj.src="<?=BE_IMG_DIR;?>design/plus1.png";
    	}
    	else {
    		obj.src="<?=BE_IMG_DIR;?>design/plus2.png";
    	}
    }
}

document.onselectstart = new Function("return false");
</script>

<!--  END MENU -->
</head>
<body style="padding:0px;margin:5px;">
<div class="menuContainer">
<table class="subContainer" style="height:95%" height="95%" cellpadding="0" cellspacing="0">
<tr><td>

<div><a onclick="setSelected(this);" class="a_level1" style="color:#D90F0F;" href='categories/' target='main'>Menu</a></div>


<?php
ob_start();
if(CUserRights::checkResourceRights('translation')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>translation/' target='main'>Преводи</a><br /><? }
if(ADVERTS_ENABLED && CUserRights::checkResourceRights('adverts')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>adverts/' target='main'>Adverts</a><br /><? }
if(POLLS_ENABLED && CUserRights::checkResourceRights('polls')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>polls/' target='main'>Polls</a><br /><? }
if(GALLERIES_ENABLED && CUserRights::checkResourceRights('gallery')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>gallery_head/' target='main'>Galleries</a><br /><? }
if(USE_OWN_USERS  && CUserRights::checkResourceRights('users')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>users/' target='main'>Users</a><br /><? }
if(USE_OWN_USERS  && CUserRights::checkResourceRights('users')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>user_group_rights/' target='main'>User Group Rights</a><br /><? }
if(COMMENTS_ENABLED && CUserRights::checkResourceRights('comments')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>comments/' target='main'>Comments</a><br /><? }
if(MEMBERS_ENABLED && CUserRights::checkResourceRights('members')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>members/' target='main'>Members</a><br /><? }

$html = ob_get_clean();

if($html) echo <<<EOD
<div class="hr"></div>
<div><img id="imgadmin" src="{$be_img_dir}design/plus1.png" onclick="SwitchMenu('subadmin');
switchImg('imgadmin','subadmin');" />
<a id="menuadmin" href="#" class="a_level1" 
onclick="setSelected(this);OpenMenu('subadmin');OpenImg('imgadmin','subadmin');return false;" 
onmouseover="ChangeClass('menuadmin','menuOver')" onmouseout="ChangeClass('menuadmin','menuOut')">Custom menu</a>
<br />
 <div class="submenu" id="subadmin" style="display:none;">
 {$html}
 </div>
</div>

EOD;
?>

<?php if(MQ_ENABLED) {
ob_start();
if(CUserRights::checkResourceRights('mail_config')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>mail_config/edit.php' target='main'>Config</a><br /><?php }
if(CUserRights::checkResourceRights('mail_groups')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>mq_mail_groups/' target='main'>Groups</a><br /><?php }
if(CUserRights::checkResourceRights('mail_heads')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>mq_mail_bulletins/' target='main'>Bulletins</a><br /><?php }
if(CUserRights::checkResourceRights('mail_heads')) {?><img src='<?=BE_IMG_DIR;?>z.gif' width='12' /><a onclick="setSelected(this);" class="a_level2" href='<?=BE_DIR;?>mail_heads/' target='main'>Mails</a><br /><?php }
$html = ob_get_clean();
if($html) echo <<<EOD
<div class="hr"></div>
<div><img id="imgsubbulletin" src="{$be_img_dir}design/plus1.png" onclick="SwitchMenu('subsubbulletin');
switchImg('imgsubbulletin','subsubbulletin');" />
<a id="menusubbulletin" href="#" class="a_level1" 
onclick="setSelected(this);OpenMenu('subsubbulletin');OpenImg('imgsubbulletin','subsubbulletin');return false;" 
onmouseover="ChangeClass('menusubbulletin','menuOver')" onmouseout="ChangeClass('menusubbulletin','menuOut')">Bulletin Settings</a>
<br />
 <div class="submenu" id="subsubbulletin" style="display:none;">
{$html}
 </div>
</div>
EOD;
}
?>


<div class="hr" ></div>
<!--  START -->
<div id="cont" style="white-space:nowrap">
<?php

function getHref($v) {
    
    $sp=str_repeat("&nbsp;", $v['level']?$v['level']-1:0);
    $value=$v['value'];
    
    
    $class = $GLOBALS['CONFIG']['FEPageTypes'][$v['type_id']]['class'];
    $dir = $GLOBALS['CONFIG'][$class]['be']['menu'];
    if(!empty($dir)) 
    {
        if(is_array($dir)) {    //zaradi custompages
        	
            $dir=$dir[$v['template_id']];
            $dir=str_replace("_#CID#_",$v['id'],$dir);
        }
        else {
            $dir .= $v['id'];   //dobavqme cid otzad
        }
        
        if(!CUserRights::checkCidRights($v['id'])) $dir='';
    }
    
    
    
    if($v['type_id']==5 && $v['attribute_cluster_id']>0 && CUserRights::checkClusterRights($v['attribute_cluster_id'], false)){
    	$dir = "products2/?cid={$v['id']}&amp;attribute_cluster_id={$v['attribute_cluster_id']}";
    }

    return $dir;
}

function drawNode($node,$table,$as_child=false) {
        $db=getdb();
        $children=$db->getassoc("select id, value, level,type_id,template_id, visible, attribute_cluster_id FROM {$table} where pid='{$node['id']}'  order by l");
        $str='';
        $img12=$as_child?2:1;
        $be_img=BE_IMG_DIR;
        if(!empty($children)) {
            $href=getHref($node);

			$node['id'] .=  $table;			
            $str.=<<<EOD
<img id="img{$node['id']}" src="{$be_img}design/plus{$img12}.png" onclick="SwitchMenu('sub{$node['id']}');switchImg('img{$node['id']}','sub{$node['id']}');" />
EOD;
if(empty($href))
$str.=<<<EOD
    <a href="#" id="menu{$node['id']}" class="a_level{$img12}" onclick="setSelected(this);SwitchMenu('sub{$node['id']}');switchImg('img{$node['id']}','sub{$node['id']}');return false;" onmouseover="ChangeClass('menu{$node['id']}','menuOver')" onmouseout="ChangeClass('menu{$node['id']}','menuOut')">{$node['value']}</a><br />
EOD;
else {
    //$href=htmlentities($href);
    $str.=<<<EOD
    <a id="menu{$node['id']}" href="{$href}" target="main" class="a_level{$img12}" onclick="setSelected(this);OpenMenu('sub{$node['id']}');OpenImg('img{$node['id']}','sub{$node['id']}');" onmouseover="ChangeClass('menu{$node['id']}','menuOver')" onmouseout="ChangeClass('menu{$node['id']}','menuOut')">{$node['value']}</a><br />    
EOD;
}
$str.=<<<EOD
<div class="submenu" id="sub{$node['id']}" style="display:none;">
EOD;
            foreach($children as $k=>$v) {
                $str.=drawNode($v,$table,true);
            }
            $str.="</div>";
        }
        else {
            $href=  getHref($node);
            if(!empty($href)) {
                $str.="<img src='".BE_IMG_DIR."z.gif' width='12' /><a class='a_level{$img12}' onclick='setSelected(this);' href='".getHref($node)."' target='main'>{$node['value']}</a><br/> ";
            }
            else {
                $str.="<img src='".BE_IMG_DIR."z.gif' width='12' /><span class='a_level{$img12}' style='color:#aaaaaa;font-size:10px;' >".$node['value']."</span><br />";
            }
        }
        return $str;
}


	$db=getdb();
	
    $menu=$db->getassoc("SELECT id, value, level, type_id,template_id, visible, attribute_cluster_id FROM categories where level=1 ORDER BY l");
	foreach($menu as $k=>$v) {
        echo drawNode($v,"categories",false);
    }
    
    //echo $str;

?>
</div>
<?php if(USE_OWN_USERS) {?>
<div class="hr"></div>
<div><a class="a_level1" href='<?=BE_DIR;?>' target='_top'>Logout</a></div>
<? } ?>
<div class="hr"></div>
<!--  END -->
</td></tr></table>
</div>
</body>
</html>