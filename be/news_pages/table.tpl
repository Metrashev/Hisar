<html>
	<table id="dg_news_pages" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
<td in_index="1" id='_id'  class="header_add"><a href='<?=BE_DIR;?>news_pages/edit.php?cid=<?=$_GET['cid'];?>&amp;<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_title'  class="header_nor"><a order="title">Title</a></td>
<td in_index="3"  id='_subtitle'  class="header_nor"><a order="subtitle">Short description</a></td>
<td in_index="5"  id='_due_date'  class="header_nor"><a order="due_date">Date</a></td>
<td in_index="6"  id='_is_visible'  class="header_nor"><a order="is_visible">Visible</a></td>
<td in_index="11" id='t11' class="header_nor"><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" href='<?=BE_DIR;?>news_pages/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="title"   ></ITTI></td>
			<td><ITTI field_name="subtitle"   ></ITTI></td>
			<td><ITTI field_name="due_date"   format="%d/%m/%Y %H:%M"></ITTI></td>
			<td><ITTI field_name="is_visible" arrayname="YES_NO"></ITTI></td>			
<td><a field_name="id" class="delete" href='#' onclick='if(window.confirm("Are you sure?")) {document.getElementById("hdDelete").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>