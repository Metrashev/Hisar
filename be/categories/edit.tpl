<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=isset($GLOBALS['CONFIG']['SITE_CHARSET'])?$GLOBALS['CONFIG']['SITE_CHARSET']:'UTF-8';?>">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Edit menu node</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>	
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<col width="100">
<col width="*" >
<tbody>
<tr>
    <th align="right"><label>ID =</label></th>
    <th align='left'><?=$_REQUEST['id'];?></th>
</tr>

<tr>
    <td align="right"><label for="value">Label</label></td>
    <td><ITTI field_name="value" style='width:95%'></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="path">URL Path</label></td>
    <td><ITTI field_name="path" style='width:95%'></ITTI></td>
</tr>

<tr>
    <td align="right"><label for="meta_keywords">META Keywords</label></td>
    <td><ITTI field_name="meta_keywords" style='width:95%;height:50px;' maxlength="255"></ITTI></td>
</tr>

<tr>
    <td align="right"><label for="meta_description">META Description</label></td>
    <td><ITTI field_name="meta_description" style='width:95%;height:50px;'></ITTI></td>
</tr>

<tr>
    <td align="right"><label for="tracker_code">Tracker Code</label> (as Google Analytics)</td>
    <td><ITTI field_name="tracker_code" style='width:95%;height:50px;'></ITTI></td>
</tr>

<tr>
    <td align="right"><label for="page_title">Page Title</label></td>
    <td><ITTI field_name="page_title" style='width:95%'></ITTI>
    	<br /> Fill in if You want to be different from Label else leave it blank.
    </td>
</tr>



   
<tr>
    <td align="right"><label for="visible">Visible in Menu</label></td>
    <td><ITTI field_name="visible" ></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="is_crumb_visible">Visible in Crumbs Path</label></td>
    <td><ITTI field_name="is_crumb_visible" ></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="is_title_visible">Visible in Page Title</label></td>
    <td><ITTI field_name="is_title_visible" ></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="use_in_search">Use in search</label></td>
    <td><ITTI field_name="use_in_search" ></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="is_page_restricted">Restricted page</label></td>
    <td><ITTI field_name="is_page_restricted" ></ITTI></td>
</tr>

  <? if($GLOBALS['show_language_field']) {?>
   
<tr>
    <td align="right"><label>Language</label></td>
    <td><ITTI field_name="language_id" ></ITTI></td>
</tr>
  <? } ?>
  
<tr>
    <td align="right"><label>Skin</label></td>
    <td><ITTI field_name="skin_id" ></ITTI></td>
</tr>
  
<tr>
    <td align="right"><label>Type</label></td>
    <td><ITTI field_name="type_id" ></ITTI></td>
</tr>




<tr>
    <td align="right"><label>Template</label></td>
    <td><ITTI field_name="template_id" ></ITTI></td>
</tr>

<?php if($GLOBALS['array']['in_data']['type_id']==5) { ?>
<tr>
    <td align="right"><label>Product</label></td>
    <td><ITTI field_name="attribute_cluster_id" ></ITTI></td>
</tr>
<?php } ?>

<tr>
    <td align="right"><label>Image</label></td>
    <td><ITTI field_name="img" ></ITTI></td>
</tr>

<?=isset($GLOBALS['tree_include_file'])?"<tr><td colspan='2'>".$GLOBALS['tree_include_file']."</td></tr>":'';?>

<tr>
    <td align="center" colspan="2"><input class="submit" type="submit" name="btSave" value="Save" />&nbsp;<input type="button" class="submit" value="Back" onclick="self.location='index.php?node=<?=$_REQUEST['id'];?>';" /> &nbsp;&nbsp;<input type="submit" name="btDelete" value="Del" <?=$GLOBALS['delete_message'];?> class="delete_button" /></td>
</tr>
</tbody>
</table>
