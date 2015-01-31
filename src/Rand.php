<?php

namespace xpl\Utility;

/**
 * Random string generator and utility.
 */
class Rand 
{
	
	/**
	 * Alphabetic charlist ID.
	 * @var int
	 */
	const ALPHA = 1;
	
	/**
	 * Numeric charlist ID.
	 * @var int
	 */
	const NUM = 2;
	
	/**
	 * Punctuation charlist ID.
	 * @var int
	 */
	const PUNCT = 4;
	
	/**
	 * Whitespace charlist ID.
	 * @var int
	 */
	const WHITESPACE = 8;
	
	/**
	 * Number '0' charlist ID.
	 * @var int
	 */
	const ZERO = 16;
	
	/**
	 * Number '1' charlist ID.
	 */
	const ONE = 32;
	
	/**
	 * Alphanumeric charlist ID.
	 * @var int
	 */
	const ALNUM = 13305;
	
	/**
	 * Salt charlist ID.
	 * @var int
	 */
	const SALT = 13307;
	
	/**
	 * Hex charlist ID.
	 * @var int
	 */
	const HEX = 13309;
	
	/**
	 * Non-zero charlist ID.
	 * @var int
	 */
	const NONZERO = 13311;
	
	/**
	 * "Distinct" charlist ID.
	 * @var int
	 */
	const DISTINCT = 13313;
	
	/**
	 * List of alphabetic characters.
	 * @var string
	 */
	const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * List of numeric characters.
	 * @var string
	 */
	const CHARS_NUM = '0123456789';
	
	/**
	 * List of punctuation characters (backticks, backslashes, and pipe characters omitted).
	 * @var string
	 */
	const CHARS_PUNCT = '~!@#$%^&*()_+=-{}[]\'";:><,./?';
	
	/**
	 * List of whitespace characters.
	 * @var string
	 */
	const CHARS_WHITESPACE = ' ';
	
	/**
	 * List of hex characters.
	 * @var string
	 */
	const CHARS_HEX = '0123456789abcdef';
	
	/**
	 * List of nonzero characters.
	 * @var string
	 */
	const CHARS_NONZERO = '123456789';
	
	/**
	 * List of "distinct" characters.
	 * @var string
	 */
	const CHARS_DISTINCT = '2345679ACDEFHJKLMNPRSTUVWXYZ';
	
	/**
	 * List of zero characters ("0").
	 * @var string
	 */
	const CHARS_ZERO = '0';
	
	/**
	 * List of one characters ("1").
	 * @var string
	 */
	const CHARS_ONE = '1';
	
	/**
	* Generate a random string from one of several of character pools.
	*
	* @param int $length Length of the returned random string (default 12)
	* @param string $type The type of characters to use to generate string (default alphanumeric).
	* @return string A random string
	*/
	public static function str($length = 12, $type = self::ALNUM) {
		return static::fromChars($length, static::getChars($type));
	}
	
	/**
	 * Generate a random string of the given length using alphabetic characters.
	 * 
	 * @param int $length Length of string to generate.
	 * @return string Random string of given length consisting of only letters.
	 */
	public static function alpha($length) {
		return static::fromChars($length, static::CHARS_ALPHA);
	}

	/**
	 * Generate a random string of the given length using numeric characters.
	 * 
	 * @param int $length Length of string to generate.
	 * @return string Random string of given length consisting of only numbers.
	 */
	public static function num($length) {
		return static::fromChars($length, static::CHARS_NUM);
	}
	
	/**
	 * Generate a random string of the given length using hexadecimal characters.
	 * 
	 * @param int $length Length of string to generate.
	 * @return string Random string of given length consisting of only hex chars.
	 */
	public static function hex($length) {
		return static::fromChars($length, static::CHARS_HEX);
	}
	
	/**
	 * Generate a random string of the given length using punctuation characters.
	 * 
	 * @param int $length Length of string to generate.
	 * @return string Random string of given length consisting of only punctuation.
	 */
	public static function punct($length) {
		return static::fromChars($length, static::CHARS_PUNCT);
	}
	
	/**
	 * Generate a random string of the given length using alpha, numeric, punctuation
	 * and whitespace characters.
	 * 
	 * @param int $length	Length of string to generate.
	 * @return string		Random string of given length consisting of letters, numbers,
	 * 						punctuation, and whitespace.
	 */
	public static function salt($length) {
		return static::fromChars($length, static::getChars(static::SALT));
	}
	
	/**
	 * Generate a random string of the given length using alphanumeric characters.
	 * 
	 * @param int $length Length of string to generate.
	 * @return string Random string of given length consisting of letters and numbers.
	 */
	public static function alnum($length) {
		return static::fromChars($length, static::getChars(static::ALNUM));
	}
	
	/**
	 * Generates a random string of given length out of a given character list string.
	 * 
	 * @param int $length Length of string to generate.
	 * @param string $charlist Characters to use in the generation of the string.
	 * @return string Random string of given length using given characters.
	 */
	public static function fromChars($length, $charlist) {
		
		$num = strlen($charlist);
		$string = ''; 
		$strlen = 0;
		
		while (strlen($string) < $length) {
			$string .= $charlist[ mt_rand(0, $num) ];
		}
		
		return $string;
	}
	
	/**
	 * Gets a string of characters from a character list ID or name.
	 * 
	 * You can use this function obtain customized character lists by
	 * using the charlist ID's in bitwise fashion like "Rand::ALPHA|Rand::PUNCT".
	 * 
	 * @param int|string $val Character list ID or name
	 * @return string Characters associated with the ID, or null if a custom unknown ID is given.
	 */
	public static function getChars($val) {
			
		if (! is_numeric($val)) {
			
			if (null === $id = static::charsId($val)) {
				trigger_error("Unknown character list ID '$val'.", E_USER_NOTICE);
				return null;
			}
		
		} else {
			$id = (int)$val;
		}
	
		// presets
		if ($id > 13304) {
			switch($id) {
				case 13305: // alnum
					return static::CHARS_ALPHA.static::CHARS_NUM;
				case 13307: // salt
					return static::CHARS_ALPHA.static::CHARS_NUM.static::CHARS_WHITESPACE.static::CHARS_PUNCT;
				case 13309: // hex
					return static::CHARS_HEX;
				case 13311: // nonzero
					return static::CHARS_NONZERO;
				case 13313: // distinct
					return static::CHARS_DISTINCT;
				default: // no idea
					trigger_error("Unknown character list ID '$val'.", E_USER_NOTICE);
					return null;
			}
		}
		
		$chars = '';
		
		if (static::ALPHA & $id) {
			$chars .= static::CHARS_ALPHA;
		}
		if (static::NUM & $id) {
			$chars .= static::CHARS_NUM;
		}
		if (static::PUNCT & $id) {
			$chars .= static::CHARS_PUNCT;
		}
		if (static::WHITESPACE & $id) {
			$chars .= static::CHARS_WHITESPACE;
		}
		if (static::ONE & $id) {
			$chars .= static::CHARS_ONE;
		}
		if (static::ZERO & $id) {
			$chars .= static::CHARS_ZERO;
		}
		
		return $chars;
	}
	
	/**
	 * Gets a character list ID by name.
	 * 
	 * Kind of like filter_id()
	 * 
	 * @param string $name Charlist name.
	 * @return int|null ID of charlist given by name, or null if invalid.
	 */
	public static function charsId($name) {
		switch (strtolower($name)) {
			case 'alnum':
				return static::ALNUM;
			case 'salt':
				return static::SALT;
			case 'numeric':
			case 'num':
				return static::NUM;
			case 'alpha':
				return static::ALPHA;
			case 'hex':
			case 'hexdec':
				return static::HEX;
			case 'nonzero':
				return static::NONZERO;
			case 'distinct':
				return static::DISTINCT;
			case 'punct':
			case 'punc':
				return static::PUNCT;
			case 'complex': // all but whitespace
				return static::ALPHA | static::NUM | static::PUNCT;
			default:
				return null;
		}
	}
	
	/**
	 * @TODO Figure out where i got this for attribution...
	 * 
	 * Generates a random string with given number of bytes.
	 * If $strong = true (default), must use one of:
	 * 		openssl_random_pseudo_bytes() if PHP >= 5.3.4
	 * 		mcrypt_create_iv() if PHP >= 5.3.7
	 * 		/dev/urandom
	 * 
	 * @param int $length Length of returned string.
	 * @param boolean $strong Whether to use a strong source. Default true.
	 * @return string Binary string of given length, or null if cannot generate.
	 * @throws RuntimeException if strong = true and a strong enough source is not available.
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

		if (is_readable('/dev/urandom')) {
			$f = fopen('/dev/urandom', 'r');
			while ( $bl < $length ) {
				$buffer .= fread($f, $length - $bl);
				$bl = strlen($buffer);
			}
			fclose($f);
		}

		if ($length === strlen($buffer)) {
			return $buffer;
		} else if ($strong) {
			throw new \RuntimeException('Unable to generate sufficiently strong random bytes: '.'No source with sufficient entropy.');
		}

		return static::randomLib($length);
	}

	/**
	 * @package RandomLib
	 * @author George Argyros <argyros.george@gmail.com>
	 * @copyright 2012 George Argyros. All rights reserved.
	 * @license 
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions are met:
	 * * Redistributions of source code must retain the above copyright
	 * notice, this list of conditions and the following disclaimer.
	 * * Redistributions in binary form must reproduce the above copyright
	 * notice, this list of conditions and the following disclaimer in the
	 * documentation and/or other materials provided with the distribution.
	 * * Neither the name of the organization nor the
	 * names of its contributors may be used to endorse or promote products
	 * derived from this software without specific prior written permission.
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
	 * @param int $length Length of returned string.
	 * @return string Random string of given length.
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
