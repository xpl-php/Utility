<?php

namespace Phpf\Util;

class Str {
		
	/**
	 * Do nothing to ASCII chars.
	 */
	const ESC_NONE = 0;
	
	/**
	 * Strip low and high ASCII chars.
	 */
	const ESC_STRIP = 1;
	
	/**
	 * Encode low and high ASCII chars.
	 */
	const ESC_ENCODE = 2;
	
	/**
	 * Escape a string using fairly aggressive rules.
	 * Strips all tags and converts to html entities.
	 * 
	 * @param string $string The string to sanitize.
	 * @param bool $encode Whether to encode or strip high & low ASCII chars. (default: false = strip)
	 * @return string Sanitized string.
	 */
	public static function esc( $string, $flag = self::ESC_STRIP ){
		
		$str = htmlentities( strip_tags($string), ENT_COMPAT, 'UTF-8' );
		
		preg_replace( '/[\x00-\x08\x0B-\x1F]/', '', $str );
		
		$str = str_replace( array('javascript:', 'document.write'), '', $str );
		
		switch( $flag ){
			
			case self::ESC_STRIP:
			default:
				$flags = FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;
				break;
			
			case self::ESC_ENCODE:
				$flags = FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH;
				break;
			
			case self::ESC_NONE:
				$flags = FILTER_FLAG_NONE;
				break;
		}
			
		return filter_var($str, FILTER_SANITIZE_STRING, $flags);
	}
			
	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	public static function escSqlLike( $text ) {
		return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
	}
		
	/**
	 * Strips non-alphanumeric characters from a string.
	 * Add characters to $extras to preserve those as well.
	 * Extra chars should be escaped for use in preg_*() functions.
	 */
	public static function escAlnum( $str, array $extras = null ){
		
		$pattern = '/[^a-zA-Z0-9 ';
		
		if ( ! empty($extras) ){
			$pattern .= implode('', $extras);
		}
		
		$pattern .= ']/';
		
		return preg_replace($pattern, '', $str);
	}
	
	/**
	 * Formats a string by injecting non-numeric characters into 
	 * the string in the positions they appear in the template.
	 *
	 * @param string $string The string to format
	 * @param string $template String format to apply
	 * @return string Formatted string.
	 */
	public static function format( $string, $template ){
		
		$result = ''; $fpos = 0; $spos = 0;
		
		while ( (strlen($template) - 1) >= $fpos ){
			
			if ( ctype_alnum( substr($template, $fpos, 1) ) ){
				$result .= substr( $string, $spos, 1 );
				$spos++;
			} else {
				$result .= substr( $template, $fpos, 1 );
			}
			
			$fpos++;
		}
		
		return $result;	
	}
	
	/**
	 * Formats a phone number based on string length.
	 */
	public static function formatPhone( $phone ){
			
		// remove any pre-existing formatting characters
		$string = str_replace(array('(',')','+','-',' '), '', $phone);
		
		switch( strlen($string) ){
			case 7:
				$tmpl = '000-0000';
				break;
			case 10:
				$tmpl = '(000) 000-0000';
				break;
			case 11:
				$tmpl = '+0 (000) 000-0000';
				break;
			case 12:
				$tmpl = '+00 00 0000 0000';
				break;
		}
		
		return self::format($string, $tmpl);
	}
	
	/**
	 * Formats a hash/digest based on string length.
	 */
	public static function formatHash( $hash ){
		
		// remove any pre-existing formatting characters
		$string = str_replace(array('(',')','+','-',' '), '', $hash);
		
		switch( strlen($string) ){
			case 16:
				$tmpl = '00000000-0000-0000';
				break;
			case 24:
				$tmpl = '00000000-0000-0000-00000000';
				break;
			case 32:
				$tmpl = '00000000-0000-0000-0000-000000000000';
				break;
			case 40:
				$tmpl = '00000000-0000-0000-00000000-0000-0000-00000000';
				break;
			case 48:
				$tmpl = '00000000-0000-00000000-0000-0000-00000000-0000-00000000';
				break;
		}
		
		return self::format($string, $tmpl);
	}
		
	/**
	* Generate a random string from one of several of character pools.
	*
	* @param int $length Length of the returned random string (default 16)
	* @param string $type The type of characters to use to generate string.
	* @return string A random string
	*/
	function rand( $length = 16, $pool_type = 'alnum' ){
		$str = '';
		
		switch ( $pool_type ) {
			case 'alnum':
			case 'alphanumeric':
			default:
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'complex':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
				break;
			case 'salt':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_[]{}<>~`+=,.;:/?';
				break;
			case 'hexdec':
			case 'hexadecimal':
				$pool = '0123456789abcdef';
				break;
			case 'numeric':
			case 'num':
				$pool = '0123456789';
				break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'nonzero':
				$pool = '123456789';
				break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
				break;
			case 'allchars':
				$pool = '~!@#$%^&*()_-+=[]{};:,<.>/?';
				break;
			case 'chars':
				$pool = '!@#$%^&*()';
				break;
			case 'extrachars':
			case 'chars2':
				$pool = '-_ []{}<>~`+=,.;:/?';
				break;
		}
		
		for ( $i=0; $i < $length; $i++ ){
			$str .= substr( $pool, mt_rand(0, strlen($pool) - 1), 1 );
		}
		
		return $str;	
	}
		
	/**
	 * Generates a random string with given number of bytes.
	 * If $strong = true (default), must use one of:
	 * 		openssl_random_pseudo_bytes() PHP >= 5.3.4
	 * 		mcrypt_create_iv() PHP >= 5.3.7
	 * 		/dev/urandom
	 * 		mt_rand()
	 */
	public static function randBytes( $length = 12, $strong = true ){
	
		if ( function_exists('openssl_random_pseudo_bytes') && version_compare(PHP_VERSION, '5.3.4') >= 0 ){
	        $bytes = openssl_random_pseudo_bytes($length, $usable);
			if (true === $usable) {
	            return $bytes;
	        }
	    }
		
		if ( function_exists('mcrypt_create_iv') && version_compare(PHP_VERSION, '5.3.7') >= 0 ){
	        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
			if ( $bytes !== false && $length === strlen($bytes) ) {
	            return $bytes;
	        }
	    }
		
		$buffer = '';
		$bl = strlen($buffer);
	    
		if ( @is_readable('/dev/urandom') ){
		    $f = fopen('/dev/urandom', 'r');
		    while ($bl < $length) {
		        $buffer .= fread($f, $length - $bl);
		        $bl = strlen($buffer);
		    }
		    fclose($f);
		} else {
			for ($i = 0; $i < $length; $i++) {
				if ($i < $bl) {
		            $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
		        } else {
		            $buffer .= chr(mt_rand(0, 255));
		        }
		    }
		}
		
		if ( $length === strlen($buffer) ){
			return $buffer;
		} elseif ( true === $strong ){
	        throw new \RuntimeException('Unable to generate sufficiently strong random bytes - no source with sufficient entropy.');
	    }
		
		return self::randBytesWeak( $length );
	}
	
	/**
	 * A less secure fallback for randBytes()
	 */
	public static function randBytesWeak( $length ){
		
		$result = '';
	    $entropy = '';
	    $msec_per_round = 100;
	    $bits_per_round = 2;
	    $total = $length;
	    $bytes = 0;
	    $hash_length = 20;
	    $rounds = 0;
	    while (strlen($result) < $length) {
	        $bytes = ($total > $hash_length)? $hash_length : $total;
	        $total -= $bytes;
	        for ($i=1; $i < 3; $i++) {
	            $t1 = microtime(true);
	            $seed = mt_rand();
	            for ($j=1; $j < 50; $j++) {
	                $seed = sha1($seed);
	            }
	            $t2 = microtime(true);
	            $entropy .= $t1 . $t2;
	        }
	        $divisor = (int) (($t2 - $t1) * 1000000);
	        if ($divisor == 0) {
	            $divisor = 400;
	        }
	        $rounds = (int) ($msec_per_round * 50 / $divisor);
	        $iter = $bytes * (int) (ceil(8 / $bits_per_round));
	        for ($i = 0; $i < $iter; $i ++)
	        {
	            $t1 = microtime();
	            $seed = sha1(mt_rand());
	            for ($j = 0; $j < $rounds; $j++)
	            {
	               $seed = sha1($seed);
	            }
	            $t2 = microtime();
	            $entropy .= $t1 . $t2;
	        }
	        $result .= sha1($entropy, true);
	    }

	    return substr($result, 0, $length);
	}
		
	/**
	 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
	 * e.g.
	 * <code>
	 * $str = 'myCamelClass'
	 * $str = pearclass($str); // now 'My_Camel_Class'
	 * </code>
	 */
	public static function pearClass( $str ){
		$strWithSpaces = self::escAlnum( trim(preg_replace('/[A-Z]/', ' $0', $str)) );
		return str_replace(' ', '_', ucwords($strWithSpaces));
	}
	
	/**
	 * Converts a string to "snake_case"
	 */
	public static function snakeCase( $str ){
		return strtolower( self::pearClass($str) );
	}
	
	/**
	 * Converts a string to "StudlyCaps"
	 */
	public static function studlyCaps( $str ){
		return str_replace(' ', '', ucwords( trim(preg_replace('/[^a-zA-Z]/', ' ', $str)) ));
	}
	
	/**
	 * Converts a string to "camelCase"
	 */
	public static function camelCase( $str ){
		return lcfirst( self::studlyCaps($str) );
	}
}
