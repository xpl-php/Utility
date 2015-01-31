<?php

namespace xpl\Utility;

class Token {
	
	protected $hmacKey;
	protected $algo;
	protected $token;
	
	public function __construct($seed, $algo = null) {
		$this->generate($seed, $algo);
	}
	
	public function generate($seed, $algo = null) {
		return $this->token = hash_hmac($this->getAlgo($algo), $seed, $this->getHmacKey());
	}
	
	public function validate($token) {
		return $token === $this->token;
	}
	
	public function getHmacKey() {
		
		if (! isset($this->hmacKey)) {
			$this->hmacKey = env('token.hash.hmac_key') ?: '&h3#I/pv#Rtoi,1n"]|1$3tq>^l(2Iu%84I/kg*J=Kk.fb@2m';
		}
		
		return $this->hmacKey;
	}
	
	public function getAlgo($algo = null) {
		
		if (! isset($this->algo)) {
			$this->algo = isset($algo) ? $algo : env('token.hash.algo') ?: 'sha224';
		}
		
		return $this->algo;
	}
	
	public function __toString() {
		return $this->token;
	}
	
}
