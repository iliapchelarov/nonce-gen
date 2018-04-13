<?php

namespace inpsyde\nonce;

use InvalidArgumentException;

/**
 * This is the model class for table "nonce".
 *
 * @property int $id
 * @property string $nonce
 * @property string $key
 * @property int expires
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
    
    /**
     * {@inheritDoc}
     * @see \yii\base\Model::setAttributes()
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (key_exists(Nonce::CODE, $values)) {
            $values[Nonce::CODE] = (string) $values[Nonce::CODE];
        }
        parent::setAttributes($values, $safeOnly);
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\StaticInstanceInterface::instance()
     */
    public static function instance($refresh = false)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\Arrayable::toArray()
     */
    public function toArray(array $fields = array(), array $expand = array(), $recursive = true)
    {
        // TODO Auto-generated method stub
        
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
        $this->nonce = (string) $n;
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
          [['nonce'], 'string', 'max' => 16],
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
