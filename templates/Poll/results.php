<table border="0" cellspacing="0"cellpadding="0" class="pollTable">
<?php
echo <<<EOD
<caption><b>{$poll['question']}</caption>
EOD;
	

		foreach ($poll['options'] as $row)
		{
			echo <<<EOD
<tr>
<td class="td_odd">{$row['percent']}%</td><td class="td_even">{$row['option_text']}</td>
</tr>
EOD;
		}

?>
</table>
<div style="text-align: center"><a href="/?cid=13">Results</a></div>

