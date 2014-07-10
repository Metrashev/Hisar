<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft" ><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Gallery</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Edit</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='25%' align='right'>
<col width='25%' align='left'>
<col width='25%' align='right'>
<col width='25%*' align='left'>
</colgroup>
<tbody>
<tr>
	<td class="_tdl"><label for="name">Name</label></td>
	<td class="_tdr" colspan="2"><ITTI field_name='name' style="width:100%;"></ITTI></td>
	<td>&nbsp;</td>
</tr>
<? if( $GLOBALS['CONFIG']['SiteLanguages']['en'] ) { ?>
<tr>
	<td class="_tdl"><label for="name_en">Name_EN</label></td>
	<td class="_tdr" colspan="2"><ITTI field_name='name_en' style="width:100%;"></ITTI></td>
	<td>&nbsp;</td>
</tr>
<? } ?>
<tr>
	<td colspan="4" align="left">
	<!--
	<table cellpadding="5" cellspacing="0">
		<tr>
			<td>Size</td>
			<td>Width</td>
			<td>Height</td>
		</tr>
		<tr>
			<td><ITTI field_name="size_1" /></td>
			<td><ITTI field_name="width_1" /></td>
			<td><ITTI field_name="height_1" /></td>
		</tr>
		<tr>
			<td><ITTI field_name="size_2" /></td>
			<td><ITTI field_name="width_2" /></td>
			<td><ITTI field_name="height_2" /></td>
		</tr>
		<tr>
			<td><ITTI field_name="size_3" /></td>
			<td><ITTI field_name="width_3" /></td>
			<td><ITTI field_name="height_3" /></td>
		</tr>
		<tr>
		<td colspan="3">
		<input type="text" id="ftp_dir" name="ftp_dir" value="<?=htmlspecialchars($_POST["ftp_dir"]);?>" />&nbsp;
		<input type="button" onclick="myFileBrowser('ftp_dir','','',window);" value="FTP dir" />&nbsp;
		<input type="submit" name="btLoad" value="Load" />
		
		<div style="display:none">
		<input type="hidden" id="test1" name="test1" />
		</div>
		</td>
		</tr>
	</table>
	-->
	</td>
</tr>

	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
<input class="submit" type="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>