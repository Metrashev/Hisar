<?php
if(DEBUG_MODE){
	ErrorsManager::catchAsserts();
	ErrorsManager::catchErrors();
}

class ITTIErrorException extends Exception {}


class ErrorsManager{
	
  
  
  private function __construct(){
    if(isset($GLOBALS['CONFIG']['ErrorLevel']))
      error_reporting($GLOBALS['CONFIG']['ErrorLevel']);
  }
  
  static function getInstance(){
    static $instance=null;
    if($instance===null)
      $instance = new self();
      
    return $instance;
  }
  
  
  function AssertHandler($file, $line, $code) 
  {
    throw new ITTIErrorException("Assertion Failed: $file($line): $code");
  }
  
  
  function ErrorHandler($errno, $errstr, $errfile, $errline) 
  {
  	if(($errno & error_reporting())==0) return;

  	$msg="[$errno] $errstr (@line $errline in file $errfile).";
  	try{
	  	throw new ITTIErrorException($msg);
  	} catch (Exception $e){
  		
  		ErrorsManager::ExceptionHandler($e);
  	}
  }
  
  static function catchAsserts(){
    $obj = self::getInstance();
    assert_options(ASSERT_ACTIVE, DEBUG_MODE);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_QUIET_EVAL, 1);
    assert_options(ASSERT_CALLBACK, array($obj, 'AssertHandler'));
  }
  
  static function catchErrors(){
    $obj = self::getInstance();
    set_error_handler(array($obj, 'ErrorHandler'));
    set_exception_handler(array($obj, 'ExceptionHandler'));
  }  
  
  function ExceptionHandler(Exception $E){
    echo <<<EOD
<script>
function sb(b){
  document.getElementById(b).style.display='block';
}
function hb(b){
  document.getElementById(b).style.display='none';
}
function qb(b){
  var obj = document.getElementById(b);
  
  obj.style.display = obj.style.display=='none' ? 'block' : 'none' ;
}
</script>
<pre>
EOD;

  	if(DEBUG_MODE)
  	{
    	echo '<h1>Fatal Error</h1>';
    	echo '<p>'.$E->getMessage().'</p>';
    	if(!is_a($E, 'ITTIErrorException')) echo '<p>'.$E->getFile().'('.$E->getLine().')</p>';
  		echo '<h2>Debug Backtrace</h2>';
  		echo '';
  		error_reporting(E_ERROR);

  		$backTrace = $E->getTrace();

  		if(is_a($E, 'ITTIErrorException')){
  		  array_splice($backTrace, 0, 1, array());
  		} else {

  		}
  		
      echo self::getBackTraceStr($backTrace);
  		echo '';
  	}
  	else
  	{
  		error_log($E->getMessage());
  		echo '<h1>Internal Error</h1>';
  	}
  	exit(1);
  }


  function getBackTraceStr($backTrace){
    $blockId=1;
    $err = '';
		foreach ($backTrace as $i=>$t){
		  $div = '';
		  $err .= "#{$i} {$t['file']}({$t['line']}): {$t['class']}{$t['type']}{$t['function']}(";
		  foreach ($t['args'] as &$arg){

		    $div .= <<<EOD
<div id="d{$blockId}" style="display:none; background:#EEEEEE; padding:0px 5px 5px 5px;"><div style="text-align:right;background:#DDDDDD;"><a href="JavaScript:qb('d$blockId');">close [X]</a></div>
<pre>
EOD;
        ob_start();
        var_dump($arg);
        $div .= ob_get_clean();
        $div .= "</pre></div>";
        
		    if(is_object($arg)) $arg = "Object(".get_class($arg).")";
        $arg = "<a href=\"JavaScript:qb('d$blockId');\">$arg</a>";
        $blockId++;
		  }
		  $err .= implode(", ", $t['args']);
		  
		  $err .= ")\n$div";
		}
		return $err;
  }
}

?>