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

/**
 * This command deals with Nonces (numbers used once) generation and verification. Configure parameters in `config/console.php`
 *
 * This is CLI provider for the real implementation made in inpsyde\nonce\NonceController
 * 
 * @author Ilia Pchelarov <i.pchelarov@gmail.com>
 */
class InonceController extends Controller
{
    /**
     * This command generates and stores for 24 hours a unique number nonce bound to the provided message.
     * @param string $message the message to be associated with nonce.
     * @return int Nonce code
     */
    public function actionCreate($message = 'hello world')
    {
        echo $message . "\n";

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
        if (Yii::$app->hasProperty('nonceGen'))
            stdout('config OK \n');
            $this->stdout('object: ' . (Yii::$app->get('nonceGen')->generateNonce() ) . "\n");
        return 0;
    }
    
    /**
     * This command cleans the expired nonces.
     * @return int Exit code
     */
    public function actionCleanup() {
        return ExitCode::OK;
    }
}
