<?php

namespace inpsyde\nonce;

interface iNonceGenerator {
    public function generateNonce($message = null);
	
	public function getExpiration($timeCreated): int;
}
