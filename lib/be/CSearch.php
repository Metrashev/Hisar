<?php

class CSearch {
	private $App;
	private $cn;
	private $page_size;
	private $messages;
	private $tables;

	function __construct($cn,$App) {
		$this->App=$App;
		$this->cn=$cn;
	}
	
	function init() {
		$this->page_size=30;

		$this->messages=array(
		'bg'=>array(
		1=>array("title"=>"намерени в страници на членове!"),
		2=>array("title"=>"намерени в новини!"),
		3=>array("title"=>"намерени в статични страници!"),
		4=>array("title"=>"намерени в меню!"),
		'news'=>'Новини',
		'sp'=>'Статични страници',
		'menu'=>'Меню',
		'mypage'=>'Страници на членове',
		'link'=>array("more"=>"Резултати от други търсения:"),
		'more'=>'още...',
		'found_items'=>'резултата намерени в',
		'no_results'=>'Няма намерени резултати!'
		),

		'en'=>array(
		1=>array("title"=>"member pages found!"),
		2=>array("title"=>"news found!"),
		3=>array("title"=>"static pages found!"),
		4=>array("title"=>"menu items found!"),
		'news'=>'News',
		'sp'=>'Static Pages',
		'menu'=>'Menu Items',
		'mypage'=>'Member Pages',
		'link'=>array("more"=>"More search topics:"),
		'more'=>'more',
		'found_items'=>'items found in',
		'no_results'=>'There are no results found!'
		)
		);

		$this->tables=array(
		1=>array('table'=>'firms',
				'text'=>array('bg'=>'Фирми','en'=>''),
				'search_fields'=>"name,activity,discount,web_site,description,city,address,email,post_code,phone",
				'count_query'=>"select count(*) from firms left outer join addresses on firm_id=firms.id where 1",
				'query'=>"select firms.id,name as sh_text,concat('/?cid=18&id=',firms.id) as link from firms left outer join addresses on firm_id=firms.id where 1",
				'order'=>'order by name'
				),
		2=>array('table'=>'answers_questions',
				'text'=>array('bg'=>'Въпроси и отговори','en'=>''),
				'search_fields'=>"question,answer",
				'count_query'=>"select count(*) from answers_questions where 1",
				'query'=>"select id,question as sh_text,concat('/?cid=&id=',id) as link from answers_questions where 1",
				'order'=>''
				),
		3=>array('table'=>'cover_dictionary',
				'text'=>array('bg'=>'Осигурителен речник','en'=>''),
				'search_fields'=>"dic_word,description",
				'count_query'=>"select count(*) from cover_dictionary where 1",
				'query'=>"select id,dic_word as sh_text,concat('/?cid=&id=',id) as link from cover_dictionary where 1",
				'order'=>''
				),
		4=>array('table'=>'dictionary',
				'text'=>array('bg'=>'Речник','en'=>''),
				'search_fields'=>"dic_word,description",
				'count_query'=>"select count(*) from dictionary where 1",
				'query'=>"select id,dic_word as sh_text,concat('/?cid=&id=',id) as link from dictionary where 1",
				'order'=>''
				),
		5=>array('table'=>'news_pages',
				'text'=>array('bg'=>'Новини','en'=>'News'),
				'search_fields'=>"title,body",
				'count_query'=>"select count(*) from news_pages where is_visible=1 and case when is_future_visible!=1 then now()>=due_date else 1 end",
				'query'=>"select id,'News' as type,title as sh_text, concat('/?cid=',cid,'&id=',id) as link from news_pages where is_visible=1 and case when is_future_visible!=1 then now()>=due_date else 1 end",
				'order'=>'order by due_date desc'
				),
		6=>array('table'=>'static_pages',
				'text'=>array('bg'=>'Статични страници','en'=>'Static Pages'),
				'search_fields'=>"static_pages.title,body",
				'count_query'=>"select count(*) from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1  and skip_search=0",
				'query'=>"select static_pages.id,'Static Page' as type,static_pages.title as sh_text,concat('/?cid=',cid,'&spid=',static_pages.id) as link from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1 and skip_search=0",
				'order'=>''
				),		
		7=>array('table'=>'categories',
				'text'=>array('bg'=>'Меню','en'=>'Menu'),
				'search_fields'=>"value",
				'count_query'=>"select count(*) from categories where visible=1 and skip_search=0",
				'query'=>"select id,'Menu' as type,value as sh_text,concat('/?cid=',id) as link from categories where visible=1  and skip_search=0",
				'order'=>''
				),
		);
		
		
	}

	function getKeyWords($keyWords,$fields) {
		if(strlen($keyWords)==1)
		$keyWords="+".$keyWords;
		$q = SearchUtils::tokenizer($keyWords);
		if($w=SearchUtils::gen_tokens_where_cond($q,$fields)) {
			return "($w)";
		}
		return '';
	}

	function search($string,$app) {
		
		$lng=CLanguage::getLanguages();
		$lng=$lng[$app->getLanguage()];

		
		if(empty($string))
			return "<div class='message'>{$this->messages[$lng]['no_results']}</div>";
		$db=getdb();
		$type=(int)$_GET['type'];
		if(!$type) {
			$type=1;
		}
		$p=(int)$_GET['p'];		
		$limit=" limit ".(($p)*$this->page_size).",{$this->page_size}";
		$result=array();
		
		$l=array();
		$s=urlencode($string);
		
		$str='';
		
		foreach ($this->tables as $tk=>$tv) {
			$kw=$this->getKeyWords($string,explode(",",$tv['search_fields']));
			$kw=empty($kw)?'':'and '.$kw;
			if($type==$tk) {
				$result[$tk]=$db->getAssoc("{$tv['query']} {$kw} {$tv['order']} {$limit}");
				$rc=$db->getOne($tv[count_query]." {$kw}");
				if(empty($result[$tk])) {
					$type++;
					$result[$tk]=$rc;
				}
			}
			else {
				$result[$tk]=$db->getOne($tv[count_query]." {$kw}");
			}
		}
		
		foreach ($result as $k=>$v) {
			if($type==$k) {
				continue;
			}
			else {
				if($v>0) $l[$k]="<a href='/?cid={$_GET['cid']}&s_search={$s}&type=$k'>{$this->tables[$k]['text'][$lng]} ({$v})</a>";
			}
		}
		if(!is_array($result[$type])) {
			$str="<div class='message'>{$this->messages[$lng]['no_results']}</div>";
		}
		else {	
			$str="<div>{$rc} {$this->messages[$lng]['found_items']} {$this->tables[$type]['text'][$lng]}</div>";			
			if(!empty($l)) {
				$str.="<div class='header_grey'>{$this->messages[$lng]['link']['more']}</div>";
				$str.="<div>".implode(" | ",$l)."</div>";
			}
			
			$str.="<div align='center'>".CLib::draw_page_bar((($rc-1)/$this->page_size),$p,"/?type={$type}&cid={$_GET['cid']}&s_search={$s}&p=",10,0)."</div>";
			foreach ($result[$type] as $k=>$v) {
				$str.= "<b>{$v['sh_text']}</b><div align='right'><a href='{$v['link']}'>{$this->messages[$lng]['more']}</a></div><hr />";
			}
			$str.="<div align='center'>".CLib::draw_page_bar((($rc-1)/$this->page_size),$p,"/?type={$type}&cid={$_GET['cid']}&s_search={$s}&p=",10,0)."</div>";
			if(!empty($l)) {
				$str.="<div class='header_grey'>{$this->messages[$lng]['link']['more']}</div>";
				$str.="<div>".implode(" | ",$l)."</div>";
			}
		}		
		return $str;

	}

	function getBodyHTML() {
		$this->init();
		return isset($_GET['s_search'])?$this->search($_GET['s_search'],$this->App):$this->search($_POST['s_search'],$this->App);
	}
}

?>