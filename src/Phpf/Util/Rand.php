<?php

namespace Phpf\Util;

class Rand {
	
	const ALPHA = 1;
	const NUM = 2;
	const PUNCT = 4;
	const WHITESPACE = 8;
	const ALNUM = 13305;
	const SALT = 13307;
	const HEX = 13309;
	const NONZERO = 13311;
	const DISTINCT = 13313;
	
	const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_NUM = '0123456789';
	const CHARS_PUNCT = '~!@#$%^&*()_+=-{}[]\'";:><,./?';
	const CHARS_WHITESPACE = ' ';
	const CHARS_HEX = '0123456789abcdef';
	const CHARS_NONZERO = '123456789';
	const CHARS_DISTINCT = '2345679ACDEFHJKLMNPRSTUVWXYZ';
	
	public static function getChars($val) {
			
		if (is_int($val)) {
			
			// presets
			if ($val > 13304) {
				switch($val) {
					case 13305:
						return static::CHARS_ALPHA.static::CHARS_NUM;
					case 13307:
						return static::CHARS_ALPHA.static::CHARS_NUM.static::CHARS_WHITESPACE.static::CHARS_PUNCT;
					case 13309:
						return static::CHARS_HEX;
					case 13311:
						return static::CHARS_NONZERO;
					case 13313:
						return static::CHARS_DISTINCT;
					default:
						trigger_error("Unknown char list ID '$val'.");
						return null;
				}
			}
			
			$chars = '';
			
			if (static::ALPHA & $val) {
				$chars .= static::ALPHA;
			}
			if (static::NUM & $val) {
				$chars .= static::NUM;
			}
			if (static::PUNCT & $val) {
				$chars .= static::CHARS_PUNCT;
			}
			if (static::WHITESPACE & $val) {
				$chars .= static::CHARS_WHITESPACE;
			}
			
			return $chars;
		} 
		
		switch ($val) {
			case 'alnum':
			default:
				return static::CHARS_ALPHA.static::CHARS_NUM;
			case 'salt':
				return static::CHARS_ALPHA.static::CHARS_NUM.static::CHARS_WHITESPACE.static::CHARS_PUNCT;
			case 'numeric':
			case 'num':
				return static::CHARS_NUM;
			case 'alpha':
				return static::CHARS_ALPHA;
			case 'hex':
			case 'hexdec':
				return static::CHARS_HEX;
			case 'nonzero':
				return static::CHARS_NONZERO;
			case 'distinct':
				return static::CHARS_DISTINCT;
			case 'punct':
			case 'punc':
				return static::CHARS_PUNCT;
			case 'complex': // all but whitespace
				return static::CHARS_ALPHA.static::CHARS_NUM.static::CHARS_PUNCT;
		}
	}
	
	/**
	* Generate a random string from one of several of character pools.
	*
	* @param int $length Length of the returned random string (default 16)
	* @param string $type The type of characters to use to generate string.
	* @return string A random string
	*/
	public static function str($length = 16, $type = self::ALNUM) {
		return static::fromCharlist($length, static::getChars($type));
	}

	public static function alpha($length) {
		return static::fromCharlist($length, static::CHARS_ALPHA);
	}

	public static function num($length) {
		return static::fromCharlist($length, static::CHARS_NUM);
	}
	
	public static function hex($length) {
		return static::fromCharlist($length, static::CHARS_HEX);
	}
	
	public static function punct($length) {
		return static::fromCharlist($length, static::CHARS_PUNCT);
	}
	
	public static function salt($length) {
		return static::fromCharlist($length, static::getChars(static::SALT));
	}
	
	public static function alnum($length) {
		return static::fromCharlist($length, static::getChars(static::ALNUM));
	}
	
	/**
	 * Generates a random string of given length out of a given character list string.
	 * 
	 * @param int $length Length of string to generate.
	 * @param string $charlist Characters to use in the generation of the string.
	 * @return string Random string of length $length using charlist.
	 */
	public static function fromCharlist($length, $charlist) {
		$num = strlen($charlist);
		$str = ''; $strlen = 0;
		while ( $strlen < $length) {
			$str .= substr($charlist, mt_rand(0, $num), 1);
			$strlen = strlen($str);
		}
		return $str;
	}
	
	/**
	 * Generates a random string with given number of bytes.
	 * If $strong = true (default), must use one of:
	 * 		openssl_random_pseudo_bytes() if PHP >= 5.3.4
	 * 		mcrypt_create_iv() if PHP >= 5.3.7
	 * 		/dev/urandom
	 */
	public static function bytes($length = 12, $strong = true) {

		if (function_exists('openssl_random_pseudo_bytes') 
			&& version_compare(PHP_VERSION, '5.3.4') >= 0 
			&& strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') 
		{
			$bytes = openssl_random_pseudo_bytes($length, $usable);
			if (true === $usable) {
				return $bytes;
			}
		}

		if (function_exists('mcrypt_create_iv') 
			&& version_compare(PHP_VERSION, '5.3.7') >= 0 
			&& strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') 
		{
			$bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
			if ($bytes !== false && $length === strlen($bytes)) {
				return $bytes;
			}
		}

		$buffer = '';
		$bl = strlen($buffer);

		if (file_exists('/dev/urandom') && is_readable('/dev/urandom')) {
			$f = fopen('/dev/urandom', 'r');
			while ( $bl < $length ) {
				$buffer .= fread($f, $length - $bl);
				$bl = strlen($buffer);
			}
			fclose($f);
		}

		if ($length === strlen($buffer)) {
			return $buffer;
		} elseif ($strong) {
			throw new \RuntimeException('Unable to generate sufficiently strong random bytes: '.'No source with sufficient entropy.');
		}

		return static::randomLib($length);
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
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	 * AND
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
	public static function randomLib($length) {

		$result = '';
		$entropy = '';
		$msec_per_round = 100;
		$bits_per_round = 2;
		$total = $length;
		$bytes = 0;
		$hash_length = 20;
		$rounds = 0;
		while ( strlen($result) < $length ) {
			$bytes = ($total > $hash_length) ? $hash_length : $total;
			$total -= $bytes;
			for ( $i = 1; $i < 3; $i++ ) {
				$t1 = microtime(true);
				$seed = mt_rand();
				for ( $j = 1; $j < 50; $j++ ) {
					$seed = sha1($seed);
				}
				$t2 = microtime(true);
				$entropy .= $t1.$t2;
			}
			$divisor = (int)(($t2 - $t1) * 1000000);
			if ($divisor == 0) {
				$divisor = 400;
			}
			$rounds = (int)($msec_per_round * 50 / $divisor);
			$iter = $bytes * (int)(ceil(8 / $bits_per_round));
			for ( $i = 0; $i < $iter; $i++ ) {
				$t1 = microtime();
				$seed = sha1(mt_rand());
				for ( $j = 0; $j < $rounds; $j++ ) {
					$seed = sha1($seed);
				}
				$t2 = microtime();
				$entropy .= $t1.$t2;
			}
			$result .= sha1($entropy, true);
		}

		return substr($result, 0, $length);
	}
	
}
