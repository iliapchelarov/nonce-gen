<?php

namespace inpsyde\nonce;

use yii\base\BaseObject;

/**
 * @property int min
 * @property int max
 * @property int timeout
 * 
 */
class SimpleNonceGenerator extends BaseObject implements iNonceGenerator {
    private $timeout = Nonce::TIMEOUT;
    private $min = 10000;
    private $max = 99999;
    
    
    /**
     * @return string
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return number
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return number
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param string $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param number $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @param number $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    public function generateNonce($message = null): int {
		return random_int($this->min, $this->max);
	}
	
	public function getExpiration($time = 0): int {
	    return $time + $this->timeout;
	}
}
