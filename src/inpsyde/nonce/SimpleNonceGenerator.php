<?php

namespace inpsyde\nonce;

class SimpleNonceGenerator implements iNonceGenerator {
    private $expiration = Nonce::TIMEOUT;
    
	public function generateNonce(): int {
		return random_int(10000, 99999);
	}
	
	public function getExpiration($time = 0): int {
	    return $time + $expiration;
	}
}
