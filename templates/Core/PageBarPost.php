<?
if($data['PageBar']['total']<2) return ;

echo <<<EOD
<div class="PageBar">
<div style="float:right">стр. {$data['PageBar']['current']} от {$data['PageBar']['total']} &nbsp;</div>
<script language="JavaScript">
<!--

function PageBarClick(p){
	var form = document.getElementById('{$data['PageBar']['FormId']}');
	form.p.value = p;
	form.submit();
	return false;
}
-->
</script>
EOD;

$res = Array();

if ($data['PageBar']['total']>1){
	


foreach($data['PageBar']['pages'] as $pg=>$href){
	if($href){
		if($_SERVER['REQUEST_METHOD']=='POST'){
		$res[] = <<<EOD
<a href="{$href}" onClick="return PageBarClick($pg)">{$pg}</a>
EOD;
		} else {
			$res[] = <<<EOD
<a href="{$href}">{$pg}</a>
EOD;
		}
	}	else {
		$res[] = <<<EOD
<b>{$pg}</b>
EOD;
	}
}

echo implode("&nbsp;", $res);
}
?>
</div>