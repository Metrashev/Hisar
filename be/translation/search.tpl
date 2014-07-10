<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Преводи</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
<tr><td  class="viewList" colspan="3"><div style="height:24px;"> 				Търсене <a href="#" style="display:block;width:20px;float:right;font-size:24px;font-weight:bold;" onclick="return toggleSearchTable(this,'toggleTranslation');"><?=$_COOKIE['toggleTranslation']=="none"?"+":"-";?></a></div></td></tr>
</table>
<div id="toggleTranslation" style="display:<?=$_COOKIE['toggleTranslation'];?>">
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='20%'>
<col width='80%*'>
</colgroup>
<tbody><tr>
<td class='_tdl'><label onclick="document.getElementById('cid').focus();">Cid</label></td>
<td class='_tdr'><ITTI field_name='cid' style="width:80%;"></ITTI></td>
</tr>

<tr>
<td class='_tdl'><label for="translation_key">Ключ</label></td>
<td class='_tdr'><ITTI field_name='translation_key' style="width:80%;"></ITTI></td>
</tr>
<tr>
<td class='_tdl'><label for="keywords">Превод</label></td>
<td class='_tdr'><ITTI field_name='keywords' style="width:80%;"></ITTI></td>
</tr>
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="search" value="Търси" />&nbsp;&nbsp;&nbsp;<input type="submit" name='btClear' class="submit" value="Изчисти" />
	</td></tr>
</tbody></table>
</div>