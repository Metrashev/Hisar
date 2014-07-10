<?php
require_once(dirname(__FILE__).'/CBOPoll.php');

$bo = new CBOPoll();

$data = $bo->getPagedList('/?cid='.$_GET['cid'], 1, 'question, active_from_date, active_to_date, options', '', 'active_from_date' );


foreach ($data['data_list'] as $ck=>$poll) {

	echo <<<EOD
	<table width="100%" cellpadding="5" cellspacing="0">
	<tr>
		<td class="title" colspan="2" >{$poll['question']}</td>
	</tr>
	<tr>
		<td colspan="2">{$poll['active_from_date']['date']} - {$poll['active_to_date']['date']}</td>
	</tr>
EOD;

	foreach ($poll['options'] as $option) {
		$result=number_format($option['percent'],0);
		echo <<<EOD
	<tr><td>{$option['option_text']} - {$option['percent']}%</td><td><div style="width:{$result}px; height:13px; background:#999999;"></div></td></tr>
EOD;
	}
	
	echo "</table>";


	
}
include(dirname(__FILE__)."/../Core/PageBar.php");

?>