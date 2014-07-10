<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft" ><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Static page</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Edit</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='5%' align='right'>
<col width='95%*' align='left'>
</colgroup>
<tbody>
<tr>
<td><label for="title">Title</label></td>
<td><ITTI field_name='title' style="width:95%"></ITTI></td>
</tr>	
<tr>
<td colspan="2" align="left"><label for="body">Text</label><br />
<ITTI field_name='body' style="width:100%;height:400px;"></ITTI></td>
</tr>

<?

if($GLOBALS['parameters']['has_gallery'] && $_GET['id']) {
	echo "<tr><td colspan='2' align='left'>";
	if($GLOBALS['gallery_name']) {
		echo <<<EOD
		Текуща галерия: <b>{$GLOBALS['gallery_name']}</b>&nbsp;
		<input type='submit' name="delGalleryHead" value="Delete" />&nbsp;
EOD;
	}
	$bkp=FE_Utils::getBackLink();
	echo <<<EOD
	<input type="submit" value="Choose gallery" name="pickGallery" />
EOD;
	
}
?>
		
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
		<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='about:blank'" value="Back" />
	</td></tr>
</tbody></table>