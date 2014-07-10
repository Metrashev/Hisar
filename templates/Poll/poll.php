<?php

require_once(dirname(__FILE__).'/CBOPoll.php');

$PollPositionId = 1;

$poll = CBOPoll::getPollData($PollPositionId);

if(empty($poll)) {
	return;
}

/*
if($_POST['doPollVote']){
	header("Location: /?cid=18");
	exit();
}
*/

if($poll['hasVoted']){
	include(dirname(__FILE__).'/results.php');
} else {
	include(dirname(__FILE__).'/vote.php');
}


?>
