<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Mq Mail Groups</td>
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
<col width='10%' align='right'>
<col width='40%' align='left'>
<col width='10%' align='right'>
<col width='40%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="name">Name</label></td>
<td colspan="3"><ITTI field_name='name' style="width:95%"></ITTI></td>
</tr>		
<tr>
<td><label for="name">File</label></td>
<td colspan="3"><input class="con_input" type="file" name="emails_list" /><br/>
Text (Tab delimited) *.txt
</td>
</tr>	
<? if ((int)$_GET['id']) { ?>
<tr>
<td><label for="email_column">Email column</label></td>
<td colspan="3"><ITTI field_name='email_column'></ITTI></td>
</tr>	
<tr>
<td>Templates:</td>
<td colspan="3" align="left" style="font-weight:bold;">
<?echo implode(', ',$GLOBALS['show_email_fields']);?>
</td>
</tr>
<? } ?>	

	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>