<html>
	<table id="dg_polls" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a  href='<?=BE_DIR;?>polls/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_question' field_name="question"  class="header_nor"><a order="question">Question</a></td>
<td in_index="3"  id='_active_from_date' field_name="active_from_date"  class="header_nor"><a order="active_from_date">Active from</a></td>
<td in_index="4"  id='_active_to_date' field_name="active_to_date"  class="header_nor"><a order="active_to_date">to</a></td>
<td in_index="5"  id='_position' field_name="position"  class="header_nor"><a order="position">Possition</a></td>
<td in_index="6"  id='_visible' field_name="visible"  class="header_nor"><a order="visible">Visible</a></td>
<td in_index="7" id='t7' field_name="id"  class="header_nor"><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>polls/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="question"   ></ITTI></td>
			<td><ITTI field_name="active_from_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="active_to_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="position"   ></ITTI></td>
			<td><ITTI field_name="visible" arrayname="YES_NO"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDeletepolls").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>