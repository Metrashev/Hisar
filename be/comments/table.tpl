<html>
	<table id="dg_comments" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"> </td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="article_id">Article</a></td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="name">Person</a></td>
<td in_index="3"  id='_email' field_name="email"  class="header_nor"><a order="email">Email</a></td>
<td in_index="10"  id='_created_date' field_name="created_date"  class="header_nor"><a order="created_date">Date</a></td>
<td in_index="12"  id='_is_visible' field_name="is_visible"  class="header_nor"><a order="is_visible">Visible</a></td>
<td in_index="13" id='t13' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>comments/edit.php?id=_#VAL#_&amp;article_id=<?=$_GET['article_id'];?>&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { 
			if($_GET['search']=='single') { ?>
			<a field_name="id" style='color:red' href="<?=htmlspecialchars($_GET['bkp']);?>&amp;return_key=_#VAL#_&amp;return_point=<?=$_GET['return_point'];?>">Select</a>
		<?	} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
			<? } ?>
		<? } ?>
		</td>
			<td><ITTI field_name="article_id" sql="select title from static_pages where id='_#VAL#_'" ></ITTI></td>
			<td><ITTI field_name="name"   ></ITTI></td>
			<td><ITTI field_name="email"   ></ITTI></td>
			
			<td><ITTI field_name="created_date"   format="%d/%m/%Y %H:%i"></ITTI></td>
			<td><ITTI field_name="is_visible" arrayname="YES_NO"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("hdDeletecomments").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Del</a></td>
</tr>
	</tbody>
	</table>
</html>