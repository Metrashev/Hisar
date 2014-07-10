<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Mq Mail Bulletins</td>
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
<col width='25%' align='right'>
<col width='25%' align='left'>
<col width='25%' align='right'>
<col width='25%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="subject">Subject</label></td>
<td><ITTI field_name='subject'></ITTI></td>
<td><label for="from_email">From email</label></td>
<td><ITTI field_name='from_email'></ITTI></td>
</tr>
<tr>
<td><label onclick="document.getElementById('mail_group_id').focus();">Group with e-mails</label></td>
<td><ITTI field_name='mail_group_id' onchange="getForm(this).submit()"></ITTI></td>
<td><label for="date_to_send">Sending date</label></td>
<td><ITTI field_name='date_to_send'></ITTI></td>
</tr>
<? if((int)$_POST['in_data']['mail_group_id']) {
	$f=getdb()->getone("select email_fields from mq_mail_groups where id=?",array((int)$_POST['in_data']['mail_group_id']));
	@$f=unserialize($f);
	$d=array();
	if(is_array($f)) {
		foreach ($f as $k=>$v) {
			$d[]=htmlspecialchars($v);			
		}
	}
	$d=implode(', ',$d);
	echo <<<EOD
	<td>Templates:</td>
<td colspan="3" align="left" style="font-weight:bold;">{$d}</td></tr>
EOD;
}
?>
<tr>
<td colspan="4" align="left"><label for="body">Text</label></td>
</tr>
<tr>
<td colspan="4" align="left"><ITTI field_name='body' style="width:100%;height:400px;"></ITTI></td>
</tr>	

<? if((int)$_GET['id']) { ?>

<tr>
<td colspan="4" align="left">Attachments
<input type="hidden" name="hd_del_attachment" id="hd_del_attachment" value="" />
</td>
</tr>
<?php
	foreach ($GLOBALS['attachments'] as $k=>$v) {
		$ext=FE_Utils::getFileExt($v['uploaded_file']);
		echo "<tr>";
		echo "<td colspan='4' align='left'>";
		echo <<<EOD
		<a href="/files/mf/mq_mail_attachments/{$k}_uploaded_file{$ext}" target="_blank">{$v['uploaded_file']}</a>&nbsp;&nbsp;
		<a href="#" onclick="if(!window.confirm('Are you shure?')) return false;document.getElementById('hd_del_attachment').value='{$k}';getForm(this).submit();return false;">Delete</a>
EOD;
		echo "</td>";
		echo "</tr>";		
	}
?>

<tr>
<td colspan='4' align='left'>
<input type="file" name="attachment" />&nbsp;<input type="submit" value="Add" name="btAddFile" />
</td>
</tr>

<? } ?>
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
			<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
			&nbsp;<input type="submit" name="btSend" value="Изпрати" class="delete_button" />
	</td></tr>
</tbody></table>