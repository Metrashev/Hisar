<form method="POST">
<?php
echo <<<EOD
<input type=hidden name=poll_id value="{$poll['id']}">
<table border="0" cellspacing="0"cellpadding="0" class="pollTable">
<caption>{$poll['question']}</caption>
EOD;
	

		foreach ($poll['options'] as $row)
		{
			echo <<<EOD
<tr>
<td class="td_odd"><input type="radio" name="poll_option_id" value="{$row['id']}"></td><td class="td_even">{$row['option_text']}</td>
</tr>
EOD;
		}


echo <<<EOD
</table>
<div style="text-align: center"><input type="submit"  value="Гласувай" name="doPollVote{$PollPositionId}" id="PollVote" class='PollSubmit' /></div>
</form>
EOD;
?>