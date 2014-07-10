<?php

class CAntiSpam {

	public static $session_var="antispam_code";
	
	public static function generateCode($length=5) {
		$digits=range(49,57); // ot 1-9
		$letters=range(65,90); // Capital Latin Letters
		//$letters=array_merge($letters,range(97,122));
		//$letters=$digits;
		
		$code="";
		for($i=0;$i<$length;$i++) {
			if(mt_rand(0,1)==0) {
				$ord=$digits[mt_rand(0,count($digits)-1)];
			}
			else {
				$ord=$letters[mt_rand(0,count($letters)-1)];
			}
			$code.=chr($ord);
		}
		return $code;
		
	}
	
	public static function checkCode($input_value) {
		$input_value = strtoupper($input_value);
		session_start();
		if(!isset($_SESSION[CAntiSpam::$session_var])) {
			return false;
		}
		return $input_value==$_SESSION[CAntiSpam::$session_var];
	}
	
	public static function renderImage() {
		$fonts=array(
			'times.ttf',
			'arial.ttf',
			'Xfiles.ttf',
		);
		
	
	
		session_start();
		$__width=250;
		$__height=40;		
		$im = @imagecreatetruecolor($__width, $__height)
		      or die("Cannot Initialize new GD image stream");
		if(isset($_SESSION[CAntiSpam::$session_var])) {
			$code=$_SESSION[CAntiSpam::$session_var];
			
			foreach ($fonts as $k=>$v){
				$fonts[$k] = realpath(dirname(__FILE__).'/fonts/'.$v);
				if(empty($fonts[$k])) unset($fonts[$k]);
			}
			
			$fonts_count = count($fonts)-1;
			

			
			$font_id=mt_rand(0,count($fonts)-1);
			$font=dirname(__FILE__).'/fonts/'.$fonts[$font_id];
			
			//imagefill($im,0,0,mt_rand(0,16581375));
			
			//$color=imagecolorallocate($im,mt_rand(80,255), mt_rand(80,255), mt_rand(80,255));
			$color=imagecolorallocate($im,255, 255,255);
			imagefill($im,0,0,$color);
			
			$color=imagecolorallocate($im,mt_rand(80,150), mt_rand(80,150), mt_rand(80,150));
			imagefilter($im,IMG_FILTER_BRIGHTNESS,50);
			
			//$color = imagecolorallocate($im, 233, 14, 91);
			
			$x = mt_rand(10, 60);
			
			$colors=array();
			for($i=0; $i<strlen($code); $i++){
				//$colors[$i]=imagecolorallocate($im,mt_rand(0,128), mt_rand(0,128), mt_rand(0,128));
				$colors[$i]=$color;
			}
			
				
			
			
			
			for($i=0; $i<strlen($code); $i++){
				
				$color=$colors[$i];
				$size = mt_rand(20, 25);
				$angle = 0;
				
				//$angle = mt_rand(-60, 60);
				$angle = mt_rand(-35, 35);
				
			
				
				$y = 30;
				
				
				//$char = $code{$i};
				$char = substr($code, $i, 1);
				$font = $fonts[mt_rand(0,$fonts_count)];
				$tmp = imagettfbbox ( $size, $angle, $font, $char);
				if($angle>=0){
					$x2 = -$tmp[6];	
				} else {
					$x2 = -$tmp[0];	
				}
			
				imagettftext($im, $size, $angle, $x+$x2, $y, $color, $font, $char);
			
				if($angle>=0){
					$x = $x + ($tmp[2]-$tmp[6]);	
				} else {
					$x = $x + ($tmp[4]-$tmp[0]);	
				}
				


				
				$x += mt_rand(5, 15);
			}
			
			for($j=0;$j<400;$j++) {
					imagesetpixel($im,mt_rand(0,$__width),mt_rand(0,$__height),$colors[$i]);
			}
			
			for($i=0; $i<8; $i++){
				imageline($im, mt_rand(0,$__width), mt_rand(0,$__height), mt_rand(0,$__width), mt_rand(0,$__height), $color);
			}
			
			
			imagefilter($im,IMG_FILTER_GAUSSIAN_BLUR);
			
		}
		
		header ("Content-type: image/png");
		header('Cache-control: no-cache, no-store');
		imagepng($im);
		imagedestroy($im);
		
		exit;
	}

}

?>