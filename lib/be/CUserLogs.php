<?php

class CUserLogs {
	
	public static $log_table="user_logs";
	
	public static function logOperation($table_name,$record_id,$operation_id) {
		$user_id=(int)Users::getUserId();
		if(!$user_id) {
			throw new Exception("UserLogs::Invalid user ID: {$user_id}");
		}
		if(!isset($GLOBALS['operation_array'][$operation_id])) {
			throw new Exception("UserLogs::Undefined operation ID: {$operation_id}");
		}
		$table=CUserLogs::$log_table;
		getdb()->execute("
			INSERT INTO	`{$table}`
			(user_id,created_date,table_name,record_id,operation_id)
			VALUES
			('{$user_id}',now(),?,?,?)
		",
			array($table_name,$record_id,$operation_id)
		);
	}
	
	public static function getLastLogRow($table_name,$record_id,$operation_id=0) {		
		if((int)$operation_id) {
			$where=" and operation_id='".((int)$operation_id)."'";
		}
		else {
			$where="";
		}
		
		
		
		return getdb()->getrow("select t1.*,name from `".(CUserLogs::$log_table)."` as t1
			inner join users on user_id=users.id
			where table_name=? and record_id=?{$where}
			
			order by t1.id desc limit 1
			",
			array($table_name,$record_id)
		);		
	}
	
	public static function renderAllRows($table_name,$record_id,$operation_id=0) {
		if((int)$operation_id) {
			
			$where=" and operation_id='".((int)$operation_id)."'";
		}
		else {
			
			$where="";
		}
		$rows=getdb()->query("select t1.*,name from `".(CUserLogs::$log_table)."` as t1 
			inner join users on t1.user_id=users.id
			where table_name=? and record_id=?{$where}			
			order by t1.id desc
			",
			array($table_name,$record_id)
		);		
		$str=<<<EOD
		<br />
		<table class="test1 list_table" cellspacing="0" cellpadding="0" border="0" style="border-top:1px solid #999;">
		<thead>
		<tr class="list_header">
		<td class="header_nor"><a>User</a></td>
		<td class="header_nor"><a>Date</a></td>
		<td class="header_nor"><a>Operation</a></td>
		</tr>
		<thead>
		<tbody>
EOD;

		$styleNames = array(0 => "tr_norRow", 1 => "tr_altRow", 2 => "tr_overRow");
		foreach ($rows as $k=>$v) {
			$cn=$styleNames[$k%2];
			$str.="
			<tr class='{$cn}' onmouseover='this.className=&quot;tr_overRow&quot;' onmouseout='this.className=&quot;{$cn}&quot;'>";
			$str.="
			<td>{$v['name']}</td>".
			"<td>{$v['created_date']}</td>".
			"<td>".$GLOBALS['operation_array'][$v['operation_id']]."</td>
			</tr>";
		}
		return $str."</tbody></table>";
	}
	
	public static function renderLastRow($table_name,$record_id,$operation_id=0) {
		$row=CUserLogs::getLastLogRow($table_name,$record_id,$operation_id);
		$h=(int)$_POST['__hd_logs'];
		$str=<<<EOD
		<a name="logs"></a>
		<input type="hidden" name="__hd_logs" id="__hd_logs" value="{$h}" />
EOD;
		if($h) {
			return $str.CUserLogs::renderAllRows($table_name,$record_id,$operation_id);
		}
		
	//	$v->setAttribute("class", $this->styleNames[$index % 2]);
	 //   $v->setAttribute("onmouseover", 'this.className="' . ($this->styleNames[2]) .'"');
	  //  $v->setAttribute("onmouseout", 'this.className="' . ($this->styleNames[$index %2]) . '"');
	
	    $styleNames = array(0 => "tr_norRow", 1 => "tr_altRow", 2 => "tr_overRow");
		
		return <<<EOD
		{$str}
		<script>
			function postlogs(obj) {
				var form=getForm(obj);
				form.action="#logs";
				document.getElementById('__hd_logs').value=1;
				form.submit();
			}
		</script>
		<br />
		<table class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
		<thead>
		<tr>
			<td colspan="3" align="right" class="listTable" style="padding:5px 5px;border-top:1px solid #999"><a href="#" onclick="postlogs(this);">See all</a>
		</tr>
		<tr class="list_header">
			<td class="header_nor"><a>Last changed on</a></td>
			<td class="header_nor"><a>User</a></td>		
			<td class="header_nor"><a>Operation</a></td>
		</tr>
		<thead>
		<tbody>				
		<tr class="tr_norRow" onmouseover='this.className="tr_overRow"' onmouseout='this.className="tr_norRow"'>
			<td>{$row['created_date']}</td>
			<td>{$row['name']}</td>
			<td>{$GLOBALS['operation_array'][$row['operation_id']]}</td>			
		</tr>
		</tbody>
		</table>
EOD;
	}
}

?>