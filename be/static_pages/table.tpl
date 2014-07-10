<html>
	<table id="dg_static_pages" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  class="header_add"><a  href='<?=BE_DIR;?>static_pages/edit.php?n_cid=<?=$_GET['n_cid'];?>&amp;<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="4"  id='_iid'  class="header_nor"><a order="title">ID</a></td>
<td in_index="5"  id='_title'  class="header_nor"><a order="title">title</a></td>
<td in_index="7" id='t7'  class="header_nor"><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id"  href='<?=BE_DIR;?>static_pages/edit.php?n_cid=<?=$_GET['n_cid'];?>&amp;id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="id"   ></ITTI></td>
			<td><ITTI field_name="title"   ></ITTI></td>			
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you sure?")) {document.getElementById("hdDelete").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>