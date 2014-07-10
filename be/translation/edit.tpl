<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Превод</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>	
<tr><td  class="viewList" colspan="3"><div style="height:24px;"> 				<div>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>Редакция</td>
					<td align="right">				<input type="submit" class="submit" name="btSave" value="Запази" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Назад" /></td>
				</tr>
			</table>
		</div> </div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='20%'>
<col width='80%*'>
</colgroup>
<tbody><tr>
<td class='_tdl'><label onclick="document.getElementById('cid').focus();">Категория</label></td>
<td class='_tdr'><ITTI field_name='cid'></ITTI></td>
</tr>

<tr>
<td class='_tdl'><label for="translation_key">Ключ</label></td>
<td class='_tdr'><ITTI field_name='translation_key' style="width:400px;"></ITTI> (Разлика между главни и малки букви!!!)</td>
</tr>

<tr>
<td class='_tdl'><label for="is_html">HTML</label></td>
<td class='_tdr'><ITTI field_name='is_html'></ITTI></td>
</tr>
<tr>
<td class='_tdl'><label for="hints">Описание</label></td>
<td class='_tdr'><ITTI field_name='hints' style="width:100%; height:100px;"></ITTI></td>
</tr>

<?
foreach ($GLOBALS['CONFIG']['SiteLanguages'] as $k=>$v){
	$h = $GLOBALS['array']['in_data']['is_html'] ? '300':'100';
	echo <<<EOD
<tr>
<td class='_tdl'><label for="value_{$k}">{$v}</label></td>
<td class='_tdr'><ITTI field_name='value_{$k}' style="width:100%; height:{$h}px;"></ITTI></td>
</tr>
EOD;
}
?>	
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Запази" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Назад" />
	</td></tr>
</tbody></table>
