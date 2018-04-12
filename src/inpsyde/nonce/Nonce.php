<?php

namespace inpsyde\nonce;

use InvalidArgumentException;

/**
 * This is the model class for table "nonce".
 *
 * @property int $id
 * @property int $nonce
 * @property string $key
 * @property string expires
 */
class Nonce extends \yii\db\ActiveRecord
{
  const TIMEOUT = 3600 * 24;

  const CODE = 'nonce';
  const KEY  = 'key';
  const TIME = 'expires';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nonce';
    }

    public function __construct($code = null, $key = null, $time = null) {
        parent::__construct();

        $this->setValues($code, $key, $time);
    }
    
    public static function getModel($nonce) {
        return Nonce::findOne([self::CODE => $nonce]);
    }

    public function expectUnique() {
      if(Nonce::getModel($this->nonce) !== null) { throw new InvalidArgumentException();}
    }

    public function isExpired() {
    	return $this->expires <= time();
    }

    public function setPair($n, $k) {
        $this->setValues($n, $k);
    }

    public function setValues($n, $k, $t = null) {
        $this->nonce = $n;
        $this->key = $k;
        if ($t > 0){
            $this->expires = $t;
        }
    }

    public static function verify($nonce, $key) {
        $candidate = Nonce::getModel($nonce);
        if (!isset($candidate)) {
            return false;
        } else return (
            $candidate->key == $key && 
            !$candidate->isExpired()
            );
    }
    
    public function verifyIt() {
        return self::verify($this->nonce, $this->key);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
      return [
          [['nonce', 'key'], 'required'],
          [['nonce'], 'unique'],
          [['nonce'], 'integer'],
          [['key'], 'string', 'max' => 256],
          [['expires'], 'default', 'value' => time() + self::TIMEOUT],
          [['expires'], 'datetime', 'timestampAttribute' => 'expires'],
          ['expires', 'compare',
              'compareValue' => time(), 'operator' => '>=',
              'type' => 'number'
          ],
          ['expires', 'compare',
              'compareValue' => (time() + self::TIMEOUT), 'operator' => '<=',
              'type' => 'number'
          ],
      ];
    }


}
