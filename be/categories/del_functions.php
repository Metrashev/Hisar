<?php

    class tree_calculations {
        static function getNodeSubitemsCount($cid) {
            $db=getdb();
            $lr=$db->getrow("select l,weight from categories where id='{$cid}'");
            $l=(int)$lr['l'];
            $r=$l+(int)$lr['weight'];
            return (int)$db->getone("select count(*) from categories where l>'{$l}' AND l<='{$r}'")  ;
        }
        
        static function getNodeSubNewsPages($cid) {
            $db=getdb();
            $lr=$db->getrow("select l,weight from categories where id='{$cid}'");
            $l=(int)$lr['l'];
            $r=$l+(int)$lr['weight'];
            $news=(int)$db->getone("select count(*) from news_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')")  ;
            return $news;
        }
        
        static function getNodeSubStaticPages($cid) {
            $db=getdb();
            $lr=$db->getrow("select l,weight from categories where id='{$cid}'");
            $l=(int)$lr['l'];
            $r=$l+(int)$lr['weight'];
            $sp=(int)$db->getone("select count(*) from static_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')")  ;
            return $sp;
        }
    }
    
    class sp_class {
       
        static function getMessage($cid) {
            $db=getdb();
            $count=(int)$db->getone("select count(*) from static_pages where cid='{$cid}'");
            $sub_items=tree_calculations::getNodeSubitemsCount($cid);            
            if($count+$sub_items) {
                $sub_sp=tree_calculations::getNodeSubStaticPages($cid);
                $sub_news=tree_calculations::getNodeSubNewsPages($cid);
                return ($count).' '." static page(s) and {$sub_items} subcategorie(s) with {$sub_sp} static pages and {$sub_news} news pages will be deleted! Continue?";
            }
            return '';
        }
        
        static function processDelete($cid) {
            $db=getdb();
            $lr=$db->getrow("select l,weight from categories where id='{$cid}'");
            $l=(int)$lr['l'];
            $r=$l+(int)$lr['weight'];
            $SQL="delete from static_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')";
            $db->Execute($SQL);
            $SQL="delete from news_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')";
            $db->Execute($SQL);
            
        }
    }
    
    class news_class {
       
        static function getMessage($cid) {
            $db=getdb();
            $count=(int)$db->getone("select count(*) from news_pages where cid='{$cid}'");
            $sub_items=tree_calculations::getNodeSubitemsCount($cid);            
            if($count+$sub_items) {
                $sub_sp=tree_calculations::getNodeSubStaticPages($cid);
                $sub_news=tree_calculations::getNodeSubNewsPages($cid);
                return ($count).' '." news page(s) and {$sub_items} subcategorie(s) with {$sub_sp} static pages and {$sub_news} news pages will be deleted! Continue?";
            }
            return '';   
        }
        
        static function processDelete($cid) {
            $db=getdb();
            $lr=$db->getrow("select l,weight from categories where id='{$cid}'");
            $l=(int)$lr['l'];
            $r=$l+(int)$lr['weight'];
            $SQL="delete from static_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')";
            $db->Execute($SQL);
            $SQL="delete from news_pages where cid in(select id from categories where l>'{$l}' AND l<='{$r}')";
            
            $db->Execute($SQL);
            
        }
    }
    
    
?>
