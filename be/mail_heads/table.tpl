<html>
	<table id="dg_mail_heads" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><? /*<a href='<?=BE_DIR;?>mail_heads/edit.php?<?=FE_Utils::getBackLink();?>'>New</a> */?></td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="name">Име</a></td>
<td in_index="4"  id='_is_approved' field_name="is_approved"  class="header_nor"><a order="is_approved">Approved</a></td>
<td in_index="5"  id='_start_date' field_name="start_date"  class="header_nor"><a order="start_date">Sending initiation</a></td>
<td in_index="6"  id='_emails_count' field_name="emails_count"  class="header_nor"><a order="emails_count">Count</a></td>
<td in_index="7"  id='_sent_emails' field_name="sent_emails"  class="header_nor"><a order="sent_emails">Sended</a></td>
<td in_index="8"  id='_delete_after_sent' field_name="delete_after_sent"  class="header_nor"><a order="delete_after_sent">Delete after sending</a></td>
<td in_index="9"  id='_status_id' field_name="status_id"  class="header_nor"><a order="status_id">Status</a></td>
<td in_index="10"  id='_created_date' field_name="created_date"  class="header_nor"><a order="created_date">Created date</a></td>
<td in_index="11" id='t11' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td nowrap="nowrap">
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>mail_heads/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a> |
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>mail_items/index.php?mail_head_id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>List</a>
		<?} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="name"   ></ITTI></td>
			<td><ITTI field_name="is_approved" arrayname="YES_NO"  ></ITTI></td>
			<td><ITTI field_name="start_date"   format="%d/%m/%Y %H:%M:%s"></ITTI></td>
			<td><ITTI field_name="emails_count"   ></ITTI></td>
			<td><ITTI field_name="sent_emails"   ></ITTI></td>
			<td><ITTI field_name="delete_after_sent" arrayname="YES_NO"  ></ITTI></td>
			<td><ITTI field_name="status_id" arrayname="mq_mail_status_array"></ITTI></td>
			<td><ITTI field_name="created_date"   format="%d/%m/%Y %H:%i"></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDeletemail_heads").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>