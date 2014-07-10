<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Advertisement</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Edit</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='25%' align='right'>
<col width='75%*' align='left'>
</colgroup>
<tbody>
	
<tr>
<td><label for="advertiser">Advertiser</label></td>
<td><ITTI field_name='advertiser' style="width:90%;"></ITTI></td>
</tr>
<tr>
<td><label for="active_from_date">active from</label></td>
<td><ITTI field_name='active_from_date'></ITTI></td>
</tr>
<tr>
<td><label for="active_to_date">active to</label></td>
<td><ITTI field_name='active_to_date'></ITTI></td>
</tr>
<tr>
<td><label onclick="document.getElementById('position_id').focus();">possition</label></td>
<td><ITTI field_name='position_id' onChange="this.form.submit();"></ITTI></td>
</tr>
<tr>
<td><label onclick="document.getElementById('ad_type_id').focus();">Type</label></td>
<td><ITTI field_name='ad_type_id' onchange="this.form.submit();"></ITTI></td>
</tr>
<?if($GLOBALS['array']['in_data']['ad_type_id']==1){?>
<tr>
<td valign="top"><label for="ad_image">Picture</label></td>
<td><ITTI field_name='ad_image'></ITTI></td>
</tr>
<? } ?>
<?if($GLOBALS['array']['in_data']['ad_type_id']==2){?>
<tr>
<td valign="top"><label for="ad_file">Flash file</label></td>
<td><ITTI field_name='ad_file'></ITTI></td>
</tr>
<? } ?>
<?if($GLOBALS['array']['in_data']['ad_type_id']==3){?>
<tr>
<td valign="top"><label for="ad_text">Text Version</label></td>
<td><ITTI field_name='ad_text' style="width:90%; height:150px;"></ITTI></td>
</tr>
<? } ?>
<?if(in_array($GLOBALS['array']['in_data']['ad_type_id'], array(1,3))){?>
<tr>
<td><label for="ad_link">URL address</label></td>
<td><ITTI field_name='ad_link' style="width:90%;"></ITTI></td>
</tr>
<tr>
<td><label for="target">Open in</label></td>
<td><ITTI field_name='target'></ITTI></td>
</tr>
<? } ?>

	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>