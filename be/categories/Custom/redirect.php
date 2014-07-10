<hr />
<table cellpadding="5" cellspacing="0"" width="100%" border='0'>
<col width="100">
<col width="*" >

<?php

$opt = array('_self', '_top', '_blank' );
$opt = CLib::draw_listbox_options($opt, $data['target'], true);
?>
<tr>
    <td align="right">Url</td>
<td><input type="text" name="data[url]" value="<?=htmlspecialchars($data['url']);?>" />
</td>
    </tr>
    <tr>
    <td align="right">Target</td>
    <td><select name="data[target]"> 

<?=$opt?>
</select>
</td>
</tr>
</table>