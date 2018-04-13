<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use inpsyde\nonce\iNonceGenerator;
use inpsyde\nonce\NonceController;

/**
 * This command deals with Nonces (numbers used once) generation and verification. Configure parameters in `config/console.php`
 *
 * This is CLI provider for the real implementation made in inpsyde\nonce\NonceController
 * 
 * 
 * @author Ilia Pchelarov <i.pchelarov@gmail.com>
 */

//  @property iNonceGenerator $generator
//  @property NonceController $manager


class InonceController extends Controller
{
    /**
     * @property iNonceGenerator $generator
     */
    private $generator;
    /**
     * @property NonceController $manager
     */
    private $manager;
    
    /**
     * @return iNonceGenerator
     */
    public function getGenerator()
    {
        if ($this->generator == null) {
            $this->generator = Yii::$app->get('nonceGen');
        }
        return $this->generator;
    }

    /**
     * @return NonceController
     */
    public function getManager()
    {
        if ($this->manager == null) {
            $m = new NonceController();
            $m->setNonceGenerator($this->getGenerator());
            $this->setManager($m);
        }
        return $this->manager;
    }

    /**
     * @param iNonceGenerator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param NonceController $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * This command generates and stores for 24 hours a unique number nonce bound to the provided message.
     * @param string $message the message to be associated with nonce.
     * @return int Nonce code
     */
    public function actionCreate($message = 'hello world')
    {
        $nonce = $this->getManager()->createNonce($message);
        $timeout = $this->generator->timeout;
        $this->stdout( "Created nonce: $nonce. Valid for: $timeout seconds. \n" );
        return ExitCode::OK;
    }
    
    /**
     * This command verifies that nonce is valid against the provided message. The nonce is destroyed unless keepAlive parameter is true.
     * @param int $nonce the nonce for verification
     * @param string $message the message 
     * @param boolean keepAlive the nonce for further usage
     * @return boolean verified or not
     */
    public function actionVerify($nonce, $message, $keepalive = false) {
        $result = $this->getManager()->verifyNonce($nonce, $message, $keepalive);
        $this->stdout("Nonce: $nonce for message: $message verified: " . ($result? 'yes': 'no') . "\n");
        return ExitCode::OK;
    }
    
    /**
     * This command cleans the expired nonces.
     * @return int Exit code
     */
    public function actionCleanup() {
        $this->getManager()->cleanup();
        $this->stdout("Cleanup done. \n");
        return ExitCode::OK;
    }
}
