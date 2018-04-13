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
    
    public function createUniqueCode($key = null) {
        $code = null;
        $i = 0;
        do {
            if ($i > 20 && $key != null) {$key .= rand(11, 99);}
            
            $code = $this->nonceGen->generateNonce($key);
            
        } while (!$this->isUniqueNonce($code) && $i++ < 100);

        return $code;
    }

    public function createNonce($key) {
        $ncode = (string) $this->createUniqueCode($key);

        $nonce = new Nonce(
          $ncode,
          $key,
          $this->getExpirationTime()
        );

        if ($nonce->save(true)) {
            return $nonce->nonce;
        } else
          throw new \InvalidArgumentException(implode(", ", $nonce->getErrorSummary(true)));
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
