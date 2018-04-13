<?php
namespace inpsyde\nonce;

use yii\base\BaseObject;

/**
 * This is a wp-based generation of nonce codes.
 * @author iliap
 *
 */
class WPNonceGenerator extends BaseObject implements iNonceGenerator
{
    private $timeout = 3600 * 24;
    private $salt = 'LEAKED_NONCE_KEYLEAKED_NONCE_SECRET';
    
    /**
     * @return number
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param number $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getExpiration($timeCreated): int
    {
        return $timeCreated + $this->timeout;
    }

    public function generateNonce($message = null)
    {
        return substr( hash_hmac('md5', wp_nonce_tick() . $message, $this->salt), -12, 10 );
    }
}

function wp_nonce_tick() {
    return (int) (time() / 3600); //ticks each hour...
}

