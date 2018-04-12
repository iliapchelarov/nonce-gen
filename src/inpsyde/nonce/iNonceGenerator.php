<?php

namespace inpsyde\nonce;

interface iNonceGenerator {
	public function generateNonce(): int;
	
	public function getExpiration($timeCreated): int;
}
