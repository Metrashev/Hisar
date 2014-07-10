<html>
	<table id="dg_mail_items" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>mail_items/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>

<td in_index="3"  id='_subject' field_name="subject"  class="header_nor"><a order="subject">Subject</a></td>
<td in_index="4"  id='_from_email' field_name="from_email"  class="header_nor"><a order="from_email">From email</a></td>
<td in_index="5"  id='_to_email' field_name="to_email"  class="header_nor"><a order="to_email">To email</a></td>
<td in_index="6"  id='_cc' field_name="cc"  class="header_nor"><a order="cc">Cc</a></td>
<td in_index="7"  id='_bcc' field_name="bcc"  class="header_nor"><a order="bcc">Bcc</a></td>
<td in_index="9"  id='_date_to_send' field_name="date_to_send"  class="header_nor"><a order="date_to_send">Date to send</a></td>
<td in_index="10"  id='_date_sent' field_name="date_sent"  class="header_nor"><a order="date_sent">Date sent</a></td>
<td in_index="11"  id='_status_id' field_name="status_id"  class="header_nor"><a order="status_id">Status id</a></td>
<td in_index="12" id='t12' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>mail_items/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="subject"   ></ITTI></td>
			<td><ITTI field_name="from_email"   ></ITTI></td>
			<td><ITTI field_name="to_email"   ></ITTI></td>
			<td><ITTI field_name="cc"   ></ITTI></td>
			<td><ITTI field_name="bcc"   ></ITTI></td>
			<td><ITTI field_name="date_to_send"   format="%d/%m/%Y %H:%M:%s"></ITTI></td>
			<td><ITTI field_name="date_sent"   format="%d/%m/%Y %H:%i"></ITTI></td>
			<td><ITTI field_name="status_id" arrayname="mq_mail_status_array"  ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDeletemail_items").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>