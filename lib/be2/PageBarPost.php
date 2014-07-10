<?
echo <<<EOD
<table class="PageBar" width="100%">
<tr>
<td class="1">Page&nbsp;{$data['PageBar']['current']}&nbsp;of&nbsp;{$data['PageBar']['total']}<br />{$data['PageBar']['ItemsCnt']}&nbsp;Records</td>
<td class="2" style="text-align:center;">
EOD;


if ($data['PageBar']['total']>1){
	
if($data['PageBar']['prev']){
	$pg = $data['PageBar']['current']-1;
	echo <<<EOD
<a Page="$pg">«</a>&nbsp;
EOD;
} else {
	echo <<<EOD
<b>«</b>&nbsp;
EOD;
}

foreach($data['PageBar']['pages'] as $pg=>$href){
	if($href){
		echo <<<EOD
<a Page="$pg">{$pg}</a>&nbsp;
EOD;
	} else {
		echo <<<EOD
<b>{$pg}</b>&nbsp;
EOD;
	}
}
if($data['PageBar']['next']){
	$pg = $data['PageBar']['current']+1;
	echo <<<EOD
<a Page="$pg">»</a>
EOD;
} else {
	echo "<b>»</b>";
}
echo <<<EOD
</td>
<td class="3" align="right">Go to <input type="text" value="{$data['PageBar']['current']}" size="3" onKeyPress="ITTI.BEListTable.setPageInput(event, this)" />
EOD;
}

?>
</td></tr>
</table>
