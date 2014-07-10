<?php

class SearchUtils {
	
	static function getKeyWords($keyWords,$fields) {
		if(strlen($keyWords)==1)
		$keyWords="+".$keyWords;
		$q = SearchUtils::tokenizer($keyWords);
		if($w=SearchUtils::gen_tokens_where_cond($q,$fields)) {
			return "($w)";
		}
		return '';
	}
	
	static function tokenizer($ss) {
		$retval=array();
		$i=0;
		$ct="";
		$instr=0;

		for ($j=0;$j<strlen($ss);$j++) {

			if ($instr==0) {
				if ($ct==""&&($ss[$j]=="'"||(($ss[$j]=="+"||$ss[$j]=="-")&&$ss[$j+1]=="'"))) {
					$instr=1;
				} elseif ($ct==""&&($ss[$j]=='"'||(($ss[$j]=="+"||$ss[$j]=="-")&&$ss[$j+1]=='"'))) {
					$instr=2;
				} elseif ($ss[$j]==' ') {
					if ($ct!="" && strlen($ct)>2)
					$retval[$i++]=mysql_real_escape_string($ct);
					$ct="";
				} else {
					$ct.=$ss[$j];
				}
			}

			if ($instr!=0) {
				if (!strlen($ct)) {
					if ($ss[$j]=='+'||$ss[$j]=='-') {
						$ct=$ss[$j];
						$j=$j+2;
					} else
					$j++;
				}
			}

			if ($instr==1) {
				if ($ss[$j]=="'") {
					if ($ct!="" && strlen($ct)>2)
					$retval[$i++]=mysql_real_escape_string($ct);
					$ct="";
					$instr=0;
				} else {
					$ct.=$ss[$j];
				}
			}

			if ($instr==2) {
				if ($ss[$j]=="\"") {
					if ($ct!="" && strlen($ct)>2)
					$retval[$i++]=mysql_real_escape_string($ct);
					$ct="";
					$instr=0;
				} else {
					$ct=$ct.$ss[$j];
				}
			}

		}

		if ($ct!="" && strlen($ct)>0)
		$retval[$i++] = mysql_real_escape_string($ct);

		return $retval;
	}

	static function gen_tokens_where_cond($tokens,$fields) {
		for ($i=0;$i<count($tokens);$i++) {
			for ($x=0;$x<count($fields);$x++) {
				$token=$tokens[$i];
				if ($token[0]=="+") {
					$token[0]="%";
					$token.="%";
					$SQL .= "$fields[$x] LIKE \"$token\"";
					if($x<count($fields)-1) {
						$SQL .= " OR ";
					}
				} elseif($token[0]=="-") {
					$token[0]="%";
					$token.="%";
					$SQL .= "$fields[$x] NOT LIKE \"$token\"";
					if($x<count($fields)-1){
						$SQL .= " AND ";
					}
				} else {
					$SQL .= "$fields[$x] LIKE \"%$token%\"";
					if($x<count($fields)-1) {
						$SQL .= " OR ";
					}
				}
			}
			if($i<count($tokens)-1){
				$SQL .= ") AND (";
			}
		}

		if ($SQL) return "($SQL)";
	}
}

?>