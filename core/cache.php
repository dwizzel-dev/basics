<?php
/*
@auth:	Dwizzel
@date:	17-01-2018
@info:	cache files
*/

class Cache {

	private $ext;
	
	public function __construct() {
	}

	public static function read($name) {
        if(ENABLE_CACHING){
			$file = CACHE_PATH.$name.'.json';
			if(file_exists($file)){	
				$fh = @fopen($file, 'r');
				if($fh){
					$content = @fread($fh, filesize($file));
					@fclose($fh);
					return $content;
					}
				}
			}
		return false;
	}
	
	public static function write($name, $content) {
        if(ENABLE_CACHING){
			$file = CACHE_PATH.$name.'.json';
			$fh = @fopen($file, 'a');
			if($fh){
				@fwrite($fh, $content);
				@fclose($fh);
				return true;
				}
			}
		return false;
	}

	public static function remove($name) {
        $file = CACHE_PATH.$name.'.json';
        if(file_exists($file)){	
            @unlink($file);
        }
    }	
		
}


//END SCRIPT