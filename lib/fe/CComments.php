<?php

class CComments {
	static function saveComment($id,$cid,$send_mail=false) {
		$comment_errors=array();
		
		mb_internal_encoding("utf-8");
		
		$en_percent=50;
		
		if(isset($_POST['btComment'])) {
			
			if(empty($_POST['comments']['name'])) {
				$comment_errors['c_name']='Име е задължително поле';
			}
			
			
			
			//if(empty($_POST['comments']['email'])) {
			//	$comment_errors['c_email']='E-mail е задължително поле';
			//}
			if(empty($_POST['comments']['comment'])) {
				$comment_errors['c_comment']='Коментар е задължително поле';
			}
			else {
				$count_en=0;
				for($i=0;$i<mb_strlen($_POST['comments']['comment']);$i++) {
					$ch=mb_substr($_POST['comments']['comment'],$i,1);
					$ch=mb_strtolower($ch);
					if($ch>='a'&&$ch<='z') {
						$count_en++;
					}
				}
				if($count_en&&mb_strlen($_POST['comments']['comment'])) {
					$p=$count_en/mb_strlen($_POST['comments']['comment'])*100;
					if($p>$en_percent) {
						$comment_errors["en_count"]="Писането на кирилица е задължително";
					}
				}
			}
			if(!CAntiSpam::checkCode($_POST['spam_code'])) {
				$comment_errors['smap_code']='Невалиден антиспам код';
			}
			if(empty($comment_errors)) {
				$db=getdb();
				$db->execute("insert into comments(name,email,address,comment,created_date,updated_date,article_id,cid,is_visible)
					values(?,?,?,?,now(),now(),?,?,1)
				",array((string)$_POST['comments']['name'],(string)$_POST['comments']['email'],(string)$_POST['comments']['address'],(string)$_POST['comments']['comment'],(int)$id,(int)$cid));
				
				if($send_mail) {
					$data=array();
					$data['subject']="New Comment added";
					$data['from']=$_POST['comments']['email'];
					$data['to']=EMAIL;
					//$data['to']="zapryan@itti.com";
					$data['return_path']=EMAIL;
					//$title=$db->getone("select title from news_pages where id='{$id}'");
					$data['body']="New Comment added for <a href='http://{$_SERVER['HTTP_HOST']}/?cid={$_GET['cid']}&amp;id={$id}'>http://{$_SERVER['HTTP_HOST']}/?cid={$_GET['cid']}&amp;id={$id}</a>:<br />
					Име:{$_POST['comments']['name']}<br />
					E-mail:{$_POST['comments']['email']}<br />
					Град:{$_POST['comments']['address']}<br />
					Коментар:<br />".nl2br($_POST['comments']['comment']);
					
					FE_Utils::send_mail($data);
				}
				
				$_POST['comments']=array();
				
				
			}
		}
		return $comment_errors;
	}
	
	static function renderBox($id,$cid) {
		$g=$_GET;
		$g['show_comments']=1;
		$g=http_build_query($g);
		$db=getdb();
		$count=$db->getone("select count(*) from comments where article_id=? and  cid=? and is_visible=1",array($id,$cid));
		if($count) {
			$a=<<<EOD
			<a href="/?{$g}#ViewComments">Виж всички коментари ({$count})</a> &#187;<br /><br />
EOD;
		}
else {
	$a="";
}
		return <<<EOD
<br /><br />
<div class='commentsBox'>
<a name="comments" ></a>
<div class='title2' style='padding:5px 10px; background:#F1F1E0'>Коментари</div>
<div class="a_link" style="text-align:left; padding:5px 10px;">
{$a}
			<a href="/?{$g}#add">Добави коментар</a>
</div>
</div>

EOD;
	}
	
	static function renderComments($id,$cid,$use_anti_spam=true) {
		if(!isset($_GET['show_comments'])) {
			return CComments::renderBox($id,$cid); 
		}
		if($use_anti_spam) {
			require_once(dirname(__FILE__).'/CAntiSpam.php');
			
		}
		$comment_errors=CComments::saveComment($id,$cid);
		if($use_anti_spam) {
			session_start();
			$_SESSION[CAntiSpam::$session_var]=CAntiSpam::generateCode();
		}
		
		$db=getdb();
		
		$p=(int)$_GET['p'];
		$pagesize=10;
		$p*=$pagesize;
		
		$total=(int)$db->getone("select count(*) from comments where article_id=? and cid=? and is_visible=1",array($id,$cid));
		
		if($total>$pagesize) {
			$pb=new CPageBar("pb",$total,$pagesize,10,'p',"GET");
			$pb->show_goto=false;
			$pb->show_page_count=false;
			$pb->show_prev_next=false;
			$pb->show_total_items=false;
			$g=$_GET;
			unset($g['p']);
			$g=http_build_query($g);
			$pb->href="/?{$g}&amp;";
		}
		
		
		$str='';
		$str= "
		<br />
		<br />
		<form method='post' action='#errors'><div class='title2' style='padding:5px 10px; background:#F1F1E0'>Коментари</div>
		<div style=\"height:5px;background:url(/i/comment_shadow.png) repeat-x;\">&nbsp;</div>
		<script>
		function findForm(elem) {
		  form_obj = elem;
		  while (form_obj.tagName!='FORM') {
		    form_obj = form_obj.parentNode;
		    if (!form_obj) {
		       return 0;
		    }
		  }
		  return form_obj;
		}
		
		function regenCode(elem){
			var f = findForm(elem);
			f.action = '#add';
			f.submit();
			return false;
		}
		</script>
		";
		$comments=$db->getAssoc("select *,date_format(created_date,'%d/%m/%Y') as c_date from comments where article_id=? and cid=? and is_visible=1  ORDER BY id DESC limit {$p},{$pagesize}",array($id,$cid));
		$str_cm=array();
		
		$counter=0;
		if(is_array($comments)&&!empty($comments)) {
			$str_cm[]= "<a name='ViewComments'></a>";
			foreach ($comments as $k=>$v) {
//				if($counter>2&&!isset($_GET['all_comments'])) {
//					break;
//				}
				$v['address']=empty($v['address'])?'':'Град:'.htmlspecialchars($v['address'])."<br />";
				$str_cm[]="<div style=\"padding:5px;color:#3A6289;font-weight:bold;\">".htmlspecialchars($v['name'])." - {$v['c_date']}</div><div style='padding:5px;'>".nl2br(htmlspecialchars($v['comment']))."</div><hr />";
				$counter++;
			}
			if(!empty($pb)) $str_cm[]="<div>".$pb->render()."</div>";
		}
//		if(count($comments)>3&&!isset($_GET['all_comments'])) {
//			$g=$_GET;
//			$g['all_comments']=1;
//			$g=http_build_query($g);
//			$str_cm[]="<div align='right'><a href='/?{$g}'>виж всички коментари (".count($comments).")</а></div>";
//		}
		
		$str_cm[] = "<a name='add'></a>".CBackLinkCounter::getInputHidden();
		

		if(!empty($comment_errors)) {
			$comment_errors="<a name='errors'></a><div style='padding:5px;color:#cc0000'>".implode("<br />",$comment_errors)."<br /></div>";
		} else {
			$comment_errors = '';
		}
		
		$str_cm[]="
		<a name='#errors'></a>
		<div class='title1' style='padding:5px ; background:#F1F1E0'>Добави коментар</div>
		<div style=\"height:5px;background:url(/i/comment_shadow.png) repeat-x;\">&nbsp;</div>
		<table width='100%' cellpadding='0' cellspacing='0'>
		<col width='50'>
		<col width='*'>
		<tr>
			<td colspan='2'>
				<div class='ReqNote'>Полетата, отбелязани с <span class='required'>*</span>, са задължителни.</div>	
				Моля, използвайте кирилица!<br />
Мнения, които нарушават добрия тон, ще бъдат изтривани от администратора.
{$articlesText1}
<!--
<div style='padding:5px 0px;color:#3A6289; font-weight:bold;'>Вашето мнение ще бъде публикувано на сайта на NG след одобрение от модератор.</div>
-->
			</td>
		</tr>
		<tr>
			<td colspan='2'>

				$comment_errors
			</td>
		</tr>		

		<tr>
			<td>Име<span class='required'>*</span></td>
			<td><input name='comments[name]' value='".htmlspecialchars($_POST['comments']['name'])."' /></td>
		</tr>
		<tr>
			<td>E-mail</td>
			<td><input name='comments[email]' value='".htmlspecialchars($_POST['comments']['email'])."' /></td>
		</tr>
		
		<tr valign='middle'>
			<td>Антиспам код<span class='required'>*</span></td>
			<td><input type='text' id='spam_code' name='spam_code' size='4' length='4' />&nbsp;<img src='/image_test.php' align='middle'/><br /><a style=\"color:#cc0000;\" href=\"#\" onclick='regenCode(this);return false;'>генерирай нов код</a></td>
		</tr>	

		<tr valign='top'>
			<td>Коментар<span class='required'>*</span></td>
			<td>
				<textarea rows='5' style='width:95%;' name='comments[comment]'>".htmlspecialchars($_POST['comments']['comment'])."</textarea>
			</td>
		</tr>


		<tr>
			<td></td>
			<td  style='padding:15px; padding-left:5px;'>
				<input type='submit' name='btComment' value='Изпрати'  class='button' />
			</td>
		</tr>	
		</table></form>
		";
		
		//$str_cm="<tr><td>".implode("</td></tr><tr><td>",$str_cm)."</td></tr>";
		//$str.= "<table class=\"comments_table\" cellpadding='0' width='100%' cellspacing='0'>{$str_cm}</table>";
		
		$str_cm="<tr><td>".implode("",$str_cm)."</td></tr>";
		$str.= "<table class=\"comments_table\" cellpadding='0' width='100%' cellspacing='0'>{$str_cm}</table>";
		return $str;
	}
}

?>