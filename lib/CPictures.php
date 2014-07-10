<?php

class CPictures {
	
	static function isAcceptableImage($image_ext) {
		return in_array($image_ext,$GLOBALS['VALID_IMAGE_EXTENSIONS']);
	}
	
	static function getImageExtension($src) {
		list($width_orig, $height_orig,$info,$attr) = getimagesize($src);
		if($info==-1)
			return '';
		switch ($info) {
			case IMG_GIF: {
				return '.gif';
			}
			case IMG_JPG:
			case IMG_JPEG: {
				return '.jpg';
			}
			case 3:
			case IMG_PNG: {
				return '.png';
			}
			
			case IMG_WBMP: {
				return '.bmp';
			}
			
			
		}
		return '';
	}
	
	static function getExtentionImageType($ext) {
		switch ($ext) {
			case '.gif': {
				return IMG_GIF;
			}
			case '.jpg': 
			{
				return IMG_JPG;
			}
			case '.jpeg': {
				return IMG_JPEG;
			}
			case '.png': {
				return IMG_PNG;
			}
			case '.bmp': {
				return IMG_WBMP;
			}
		}
		return -1;
	}
	
	static function createTumbnail($type,$src,$dst,$width=120,$height=120,$quality=100, $fit_out_window=false) {
		$info=-1;
		$attr=-1;
		list($width_orig, $height_orig,$info,$attr) = getimagesize($src);
		if($info==-1)
			return false;
		$widthScale = $width/$width_orig;
	    $heightScale = $height/$height_orig;

	    if( !$fit_out_window ) {
	    	if($widthScale < $heightScale){
	    		$dst_w = $width;
	    		$dst_h = round($height_orig*$width/$width_orig);
	    	} else {
	    		$dst_w = round($width_orig*$height/$height_orig);
	    		$dst_h = $height;
	    	}
	    }
	    else{
	    	if($widthScale < $heightScale){
	    		$dst_w = round($width_orig*$height/$height_orig);
	    		$dst_h = $height;
	    	} else {
	    		$dst_w = $width;
	    		$dst_h = round($height_orig*$width/$width_orig);
	    	}
	    }


		// Resample
		$image_p = imagecreatetruecolor($dst_w, $dst_h);
		
		switch ($info) {
			case IMG_GIF: {
				$image = imagecreatefromgif($src);
				break;
			}
			case IMG_JPG:
			case IMG_JPEG: {
				ini_set("gd.jpeg_ignore_warning", 1);
				$image = imagecreatefromjpeg($src);
				
				break;
			}
			case 3:
			case IMG_PNG: {
				$image = imagecreatefrompng($src);
				break;
			}
			
			case IMG_WBMP: {
				$image = imagecreatefromwbmp($src);
				break;
			}
			
			default: {
				$image = null;
				break;
			}
		}
		if($image==null||!$image)
			return false;
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $dst_w, $dst_h, $width_orig, $height_orig);

		$quality=(int)$quality;
		if (empty($quality)) {
			$quality=100;
		}
		//patch da resizne image sus zadaden extention
		/*$ext=FE_Utils::getFileExt($dst);
		$ext=CPictures::getExtentionImageType($ext);
		if($ext==-1) {
			$ext=info;
		}
		switch ($ext) {
		*/
		switch ($info) {
		
			case IMG_GIF: {
				imagegif($image_p,$dst);
				return '.gif';
			}
			case IMG_JPG:
			case IMG_JPEG: {
				imagejpeg($image_p,$dst, $quality);
				return '.jpg';
			}
			case 3:
			case IMG_PNG: {
				imagepng($image_p,$dst);
				return '.png';
			}
			
			case IMG_WBMP: {
				imagewbmp($image_p,$dst);
				return '.bmp';
			}
			
			default: {
				return false;
				break;
			}
		}
		return false;
	}
	
	static function resizeImage($src,$dst,$par_array,$resize=true) {
		if(!$resize) {
			@$b=copy($src,$dst);
			if(!$b) {
				return array("Cannot upload image!");
			}
			
			//chmod($dst,0777);
			return array();
		}
		list($width, $height,$info,$attr) = getimagesize($src);
		$w=0;
		$h=0;
		if($width>$par_array[0]&&$par_array[0]>0) {
			$w=$par_array[0];
		}
		if($height>$par_array[1]&&$par_array[1]>0) {
			$h=$par_array[1];
		}
		if($w==0&&$h==0) {
			//patch za da sazdawa image sus sa6tiq extention
			//CPictures::createTumbnail('',$src,$dst,$width,$height);
			
			@$b=copy($src,$dst);
			if(!$b) {
				return array("Cannot upload image!");
			}
			
			//chmod($dst,0777);
			return array();
		}
		if($w==0) {
			$w=$width;
		}
		if($h==0) {
			$h=$height;
		}
		CPictures::createTumbnail('',$src,$dst,$w,$h);
		return array();
	}
}

?>