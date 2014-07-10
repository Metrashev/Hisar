<?php
/* Search Engine Frindly URLS */


class SEUrls {
	
	
	static function getUrlPathForCid($cid){
		static $map = null;
		
		if($map!=null)
			return $map[$cid];
		
		$db = getdb();
		$map = $db->getAssoc("SELECT id,path FROM categories ORDER BY l");
		return $map[$cid];
	}

	static function _MakeSEFriendliURLs($matches) 
	{
		static $urlsHash = array();
	
		if(array_key_exists($matches[1], $urlsHash))
			return $urlsHash[$matches[1]];
	
		$parsed = parse_url($matches[1]);
		if(!empty($parsed['host']) && ($parsed['host']!=$_SERVER['HTTP_HOST']) ){
			$urlsHash[$matches[1]] = $matches[0];
			return $matches[0];
		}
		
		$parsed['query'] = str_ireplace('&amp;', '&', $parsed['query']);
		parse_str($parsed['query'], $parsed['query']);
		
		$cid = $parsed['query']['cid'];
		
		$newUrl = self::getUrlPathForCid((int)$cid);
		
		if(empty($newUrl)){
			$urlsHash[$matches[1]] = $matches[0];
			return $matches[0];
		}
		
		unset($parsed['query']['cid']);
		if(!empty($parsed['query'])){
			$newUrl .= '?'.http_build_query($parsed['query']);
		}	
		
		if(array_key_exists('fragment', $parsed))
			$newUrl .= '#'.$parsed['fragment'];
			
		
		$newUrl = 'href="'.$newUrl.'"';
		$urlsHash[$matches[1]] = $newUrl;
	  return $newUrl;
	}
	
	static function RemakeOutput($html){
		return preg_replace_callback(
            "/href=\"([^\"]*cid=[0-9]+[^\"]*)\"/",
            array(__CLASS__, '_MakeSEFriendliURLs'),
            $html);
	}
	
	
	static function MakeCidFromPath(){
	  if($_GET['path']){
	  	$db = getdb();
		$_GET[FrontControler::CIDVarName] = $db->getOne("SELECT id FROM categories WHERE path=?", array($_GET['path']));
		if($_GET[FrontControler::CIDVarName]==0) throw new EPageNotFound();
	  }
	}
}



?>