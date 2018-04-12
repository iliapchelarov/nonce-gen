<?php

namespace inpsyde\nonce;

class NonceController {

    private $nonceGen;

    public static function isUniqueNonce($code) {
        return (Nonce::getModel($code) === null);
    }

    public function __construct() {
        $this->setNonceGenerator(new SimpleNonceGenerator());
    }

    public function setNonceGenerator(iNonceGenerator $gen) {
        $this->nonceGen = $gen;
    }

    public function getExpirationTime($time = 0) {
        if ($time == 0 or $time == null) {
            $time = time();
        }
        return $this->nonceGen->getExpiration($time);
    }
    
    public function createUniqueCode() {
        $code = null;
        do {
            $code = $this->nonceGen->generateNonce();
        } while (!$this->isUniqueNonce($code));

        return $code;
    }

    public function createNonce($key) {
        $ncode = $this->createUniqueCode();

        $nonce = new Nonce(
          $ncode,
          $key,
          $this->getExpirationTime()
        );

        if ($nonce->save(true)) {
            return $nonce->nonce;
        } else
          throw new \InvalidArgumentException($nonce->getErrorSummary());
    }

    public function verifyNonce($nonce, $key, $keepAlive = false) {
        $record = Nonce::getModel($nonce);
        if (isset($record) && $record->verifyIt()) {
            if ($record->isExpired()) {
                $record->delete();
                return false;
            } else {
              $keepAlive or $record->delete();
              return true;
            }
        } else {
            return false;
        }
    }
    
    public function cleanup() {
        Nonce::deleteAll(Nonce::TIME . '<' . time());
    }
}
