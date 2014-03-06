<?php

namespace Phpf\Util;

class Security {
	
	protected static $hash_key = '1<Kjia6~?qxg*|!RLg<E!*TwB%yq)Fa77O:F))>%>Lp/vw-T1QF!Qm6rFWz1X3bQ';
	
	const DEFAULT_HASH_ALGO = 'sha1';
	
	public static function setHashKey($key){
		self::$hash_key = $key;
	}
	
	public static function getHashKey(){
		return self::$hash_key;
	}
	
	/**
	 * Generates a verifiable token from seed.
	 */
	public static function generateToken( $seed, $algo = self::DEFAULT_HASH_ALGO ){
		return hash_hmac($algo, $seed, self::getHashKey());
	}
	
	/**
	 * Verifies a token using seed.
	 */
	public static function verifyToken( $token, $seed, $algo = self::DEFAULT_HASH_ALGO ){
		return $token === self::generateToken($seed, $algo);
	}
	
	/**
	 * Generate a v4 UUID.
	 * That is, a random string of 32 hexadecimal characters (a-f and 0-9) 
	 * formatted with dashes at certain positions (8-4-4-4-12).
	 */
	public static function generateUuid(){
		$bytes = self::randBytes(16, false);
		$hex = bin2hex($bytes);
		return Str::formatHash($hex);
	}
	
	/**
	 * Generates a 32-char base64-encoded random string.
	 */
	public static function generateCrsfToken(){
	    return base64_encode(self::generateUuid());
	}
		
	/**
	 * Generates a random string with given number of bytes.
	 * If $strong = true (default), must use one of:
	 * 		openssl_random_pseudo_bytes() if PHP >= 5.3.4
	 * 		mcrypt_create_iv() if PHP >= 5.3.7
	 * 		/dev/urandom
	 */
	public static function randBytes( $length = 12, $strong = true ){
		
		$isWin = strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN';
		
		if ( function_exists('openssl_random_pseudo_bytes') 
			&& version_compare(PHP_VERSION, '5.3.4') >= 0 
			&& strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' 
		){
	        $bytes = openssl_random_pseudo_bytes($length, $usable);
			if (true === $usable) {
	            return $bytes;
	        }
	    }
		
		if ( function_exists('mcrypt_create_iv') 
			&& version_compare(PHP_VERSION, '5.3.7') >= 0 
			&& strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' 
		){
	        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
			if ( $bytes !== false && $length === strlen($bytes) ) {
	            return $bytes;
	        }
	    }
		
		$buffer = '';
		$bl = strlen($buffer);
	    
		if ( file_exists('/dev/urandom') && is_readable('/dev/urandom') ){
		    $f = fopen('/dev/urandom', 'r');
		    while ($bl < $length) {
		        $buffer .= fread($f, $length - $bl);
		        $bl = strlen($buffer);
		    }
		    fclose($f);
		}
		
		if ( $length === strlen($buffer) ){
			return $buffer;
		} elseif ( true === $strong ){
	        throw new \RuntimeException(
	        	'Unable to generate sufficiently strong random bytes: '
	        	. 'No source with sufficient entropy.'
			);
	    }
		
		return self::randomLib( $length );
	}
		
	/**
	* Author:
	* George Argyros <argyros.george@gmail.com>
	*
	* Copyright (c) 2012, George Argyros
	* All rights reserved.
	*
	* Redistribution and use in source and binary forms, with or without
	* modification, are permitted provided that the following conditions are met:
	* * Redistributions of source code must retain the above copyright
	* notice, this list of conditions and the following disclaimer.
	* * Redistributions in binary form must reproduce the above copyright
	* notice, this list of conditions and the following disclaimer in the
	* documentation and/or other materials provided with the distribution.
	* * Neither the name of the <organization> nor the
	* names of its contributors may be used to endorse or promote products
	* derived from this software without specific prior written permission.
	*
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	* DISCLAIMED. IN NO EVENT SHALL GEORGE ARGYROS BE LIABLE FOR ANY
	* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	*
	*/
	public static function randomLib( $length ){
		
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
	
}
