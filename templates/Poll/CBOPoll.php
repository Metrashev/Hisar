<?php

class CBOPoll extends ABOBase {
	
	private $cid;
	
	public $fields = array(
		'question'=> array('type'=>'string', 'lng'=>0, 'lngs'=>array( 'en'=>'')),
		'active_from_date' => array('type' =>'Date'),
		'active_to_date' => array('type' =>'Date'),
		'options' => array('calculated'=>true),
	);
	
	public $tableName = 'polls';
	
	function getFEConstrains(){
		return 'visible=1 AND active_from_date <= now()';
	}
	
	function calculateList($data, $fieldsToCalc){
		if(empty($data)) return $data;

		foreach ($data as &$row){
			$this->_setPollOptions($row);
		}
		
		
		return $data;
	}
	
	function modifyQueryForCalc($fieldsToCalc, CSQLQueryBuilder $qb){

		if($fieldsToCalc['options']){
			$qb->fields['id'] = 'id';
		}

	}
	
	function updatePollVotes($poll_id, $poll_option_id) {
		$this->db->Execute("UPDATE polls_options SET num_votes=num_votes+1 WHERE id='$poll_option_id' AND poll_id='$poll_id'");

		$cookie_name = "voted[{$poll_id}]";
		$_COOKIE['voted'][$poll_id] = 1;
		setCookie($cookie_name,"1",time()+3600*24*365,"/");
	}
	
	private function _setPollOptions(&$poll){
		$options=$this->db->getAll("SELECT id, option_text, num_votes FROM polls_options WHERE poll_id='{$poll['id']}'");
		$total = 0;
		foreach ($options as $row){
			$total += $row['num_votes'];
		}
		
		$poll['total_votes'] = $total;
		if($total==0) $total = 1;
		$poll['CookieName'] = "voted[{$poll['id']}";
		
		foreach ($options as &$row){
			$row['percent'] = sprintf('%.2f', $row['num_votes'] / $total * 100);
			$row['option_text'] = htmlspecialchars($row['option_text'], ENT_QUOTES);
		}
		$poll['options'] = $options;
		return $options;
	}

	static function getPollData($pos){
		$bo = new self();
		
		if($_POST['doPollVote'.$pos] && $_POST['poll_id'] && $_POST['poll_option_id'] ){
			$poll_id = (int)$_POST['poll_id'];
			$poll_option_id = (int)$_POST['poll_option_id'];

			$poll = $bo->getRow('id, question, options', "id=$poll_id AND active_to_date>now()");
			if(!empty($poll) && empty($_COOKIE['voted'][$poll_id]) ) {
				$bo->updatePollVotes($poll_id, $poll_option_id);
			}
			
		} else {
			$poll = $bo->getRow('id, question, options', "position=$pos AND active_to_date>now()", 'RAND()');
		}
		
		if(!empty($poll)) $poll['hasVoted'] = !empty($_COOKIE['voted'][$poll['id']]);
		return $poll;
	}
	
}

?>