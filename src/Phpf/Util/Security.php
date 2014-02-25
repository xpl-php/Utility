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
	 * Generate a UUID
	 * 32 hexadecimal characters (a-f and 0-9) in format 8-4-4-4-12.
	 */
	public static function generateUuid(){
		return Str::formatHash(Str::rand(32, 'hexdec'));
	}
	
	/**
	 * Generates a 32-byte base64-encoded random string.
	 */
	public static function generateCrsfToken(){
	    return base64_encode(Str::rand(32, 'alnum'));
	}
	
}
