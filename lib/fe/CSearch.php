<?php
require_once(dirname(__FILE__).'/../search_utils.php');


class CSearch {
	private $page_size;
	private $messages;
	private $tables;
  private $items;

	function __construct() {

		$this->page_size=2;

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
		'more'=>'Read more',
		'found_items'=>'items found in',
		'no_results'=>'There are no results found!'
		)
		);

		$this->tables=array(
		/*1=>array('table'=>'firms',
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
				),*/
		1=>array('table'=>'news_pages',
				'text'=>array('bg'=>'Новини','en'=>'News'),
				'search_fields'=>"news_pages.title,news_pages.subtitle,news_pages.body",
				'count_query'=>"select count(*) from news_pages where is_visible=1 and case when is_future_visible!=1 then now()>=due_date else 1 end",
				'query'=>"select id,title as title,subtitle as description, concat('/?cid=',cid,'&amp;NewsId=',id) as link from news_pages where is_visible=1 and case when is_future_visible!=1 then now()>=due_date else 1 end",
				'order'=>'order by due_date desc'
				),
		
		2=>array('table'=>'categories',
				'text'=>array('bg'=>'Меню','en'=>'Menu'),
				'search_fields'=>"value",
                //'count_query'=>"select count(*) from categories where visible=1 and skip_search=0",
				'count_query'=>"select count(*) from categories where visible=1",
                //'query'=>"select id,'Menu' as type,value as sh_text,concat('/?cid=',id) as link from categories where visible=1  and skip_search=0",
				'query'=>"select id,value as title,concat('/?cid=',id) as link from categories where visible=1",
				'order'=>''
				),
        3=>array('table'=>'static_pages',
                'text'=>array('bg'=>'Статични страници','en'=>'Static Pages'),
                'search_fields'=>"static_pages.title,body",
                //'count_query'=>"select count(*) from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1  and skip_search=0",
                'count_query'=>"select count(*) from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1  ",
                //'query'=>"select static_pages.id,'Static Page' as type,static_pages.title as sh_text,concat('/?cid=',cid,'&spid=',static_pages.id) as link from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1 and skip_search=0",
                'query'=>"select static_pages.id,static_pages.title as title,concat('/?cid=',cid,'&amp;spid=',static_pages.id) as link from static_pages inner join categories on categories.id=static_pages.cid where categories.visible=1",
                'order'=>''
                ),
        
		);
        
        $this->items=array(
            1=>array('type_name'=>"News"),
            2=>array('type_name'=>"Menu"),
            3=>array('type_name'=>"Static Pages"),
           
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

	function search($string) {
		
        $data=array();
        
        $data['items']=$this->items;
        $data['data']=array();
        
	//	$lng=CLanguage::getLanguages();
	//	$lng=$lng[$app->getLanguage()];
		$lng='en';
		if(empty($string))
			return $data;
		$db=getdb();
		$type=(int)$_GET['type'];
		if(!$type) {
			$type=1;
		}
        $p=(int)$_GET['p'];
        if($p) {    //broim ot 1
            $p--;
        }		
		$limit=" limit ".(($p)*$this->page_size).",{$this->page_size}";
		$result=array();
		
		$l=array();
		$s=urlencode($string);
		
		$str="";
		  
		foreach ($this->tables as $tk=>$tv) {
            $kw=$this->getKeyWords($string,explode(",",$tv['search_fields']));
            $kw=empty($kw)?'':'and '.$kw; 
            
           $data['items'][$tk]['href']="/?cid={$_GET['cid']}&amp;q={$s}&amp;type={$tk}";
            $data['items'][$tk]['count']=$db->getOne($tv[count_query]." {$kw}");
			
			
			
            
        	if($type==$tk) {
                $result=$db->getAssoc("{$tv['query']} {$kw} {$tv['order']} {$limit}");
				if(empty($data['items'][$tk]['count'])) {
					$type++;    //po default izbirame sledva6tata sekciq					
				}
			}
            	
		}
		
    $rc=(int)$data['items'][$type]['count'];
    $data['data']=$result;
    $data['current']=$type;
    $pb=new CFEPageBar($this->page_size,$rc);
    $data['PageBar']=$pb->getData("/?cid={$_GET['cid']}&amp;q={$s}&amp;type={$type}");
    return $data;
		
	}

	function getData() {
		return isset($_GET['q'])?$this->search($_GET['q']):$this->search($_POST['q']);
	}
}





?>