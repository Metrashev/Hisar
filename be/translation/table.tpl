<html>
	<table id="dg_translation" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>translation/edit.php?<?=FE_Utils::getBackLink();?>'>Нов</a></td>
<td in_index="2"  id='_cid' field_name="cid"  class="header_nor"><a order="cid">Категория</a></td>

<td in_index="4"  id='_translation_key' field_name="translation_key"  class="header_nor"><a order="translation_key">Ключ</a></td>
<?
foreach ($GLOBALS['CONFIG']['SiteLanguages'] as $k=>$v){
	echo <<<EOD
<td in_index="5"  id='_value_{$k}' field_name="value_{$k}"  class="header_nor"><a order="value_{$k}">{$v}</a></td>
EOD;
}
?>
<td in_index="6" id='t6' field_name="id"  class="delete" href='#'><a>Изтрий</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>translation/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Редакция</a>
		<?} else { 
			if($_GET['search']=='single') { ?>
			<a field_name="id" style='color:red' href="<?=htmlspecialchars($_GET['bkp']);?>&amp;return_key=_#VAL#_&amp;return_point=<?=$_GET['return_point'];?>">Избери</a>
		<?	} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
			<? } ?>
		<? } ?>
		</td>
			<td><ITTI field_name="cid" arrayname="TranslationCategories" ></ITTI></td>

			<td><ITTI field_name="translation_key"   ></ITTI></td>
<?
foreach ($GLOBALS['CONFIG']['SiteLanguages'] as $k=>$v){
	echo <<<EOD
			<td><ITTI field_name="value_{$k}" class="CTransTabList" userfunc="getValue" ></ITTI></td>
EOD;
}
?>
<td><a field_name="id" href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("hdDeletetranslation").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Изтрий</a></td>
</tr>
	</tbody>
	</table>
</html>