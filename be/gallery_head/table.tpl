<html>
	<table id="dg_gallery_head" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>gallery_head/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="name">Name</a></td>
<td in_index="2"  id='_name_en' field_name="name_en"  class="header_nor"><a order="name_en">Name En</a></td>
<td in_index="3" id='t3' field_name="id"  class="header_nor"><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['select'])) { ?>
			<a field_name="id"  href='<?=BE_DIR;?>gallery_head/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
			|
			<a field_name="id" href="<?=BE_DIR;?>gallery/?cid=1&amp;page_id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>">Pictures</a>
		<?} else { ?>
			<a href="<?=htmlspecialchars($_GET['bkp']);?>&amp;return_point=<?=$_GET['return_point'];?>&amp;result=_#VAL#_" field_name="id">Select</a>
		<? } ?>
		</td>
			<td><ITTI field_name="name"   ></ITTI></td>
			<td><ITTI field_name="name_en"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDeletegallery_head").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>