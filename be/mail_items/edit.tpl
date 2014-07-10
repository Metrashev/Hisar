<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Mail Items</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="3">				<div>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>Edit</td>
					<td align="right">				<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" /></td>
				</tr>
			</table>
		</div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='15%' align='right'>
<col width='35%' align='left'>
<col width='15%' align='right'>
<col width='35%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="subject">Subject</label></td>
<td><ITTI field_name='subject' readonly="readonly"></ITTI></td>
<td><label for="date_to_send">Date to send</label></td>
<td><ITTI field_name='date_to_send'></ITTI></td>
</tr>
<tr>
<td><label for="from_email">From email</label></td>
<td><ITTI field_name='from_email' readonly="readonly"></ITTI></td>
<td><label for="to_email">To email</label></td>
<td><ITTI field_name='to_email' readonly="readonly"></ITTI></td>

</tr>
<tr>
<td><label for="bcc">Bcc</label></td>
<td><ITTI field_name='bcc' style="width:95%;height:50px;"></ITTI></td>
<td><label for="cc">Cc</label></td>
<td><ITTI field_name='cc' style="width:95%;height:50px;"></ITTI></td>
</tr>
	
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
		<input type="submit" class="submit" name="btSave" value="Save" />
		&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
		<br /><br /><input type="button" class="submit" onclick="self.location='view_eml.php?mail_item_id=<?=$_GET['id'];?>&amp;<?=FE_Utils::getBackLink();?>'" value="Виж" />
	</td></tr> 
	<tr>
<td colspan="2" align="right">Test to E-mail: <input type="text" name="test_email" /></td>
<td colspan="2" align="left"><input type="submit" name="btSend" value="Send" class="delete_button" /></td>
</tr>
</tbody></table>