<?php
namespace inpsyde\nonce;

use yii\test\ArrayFixture;

class NonceFixture extends ArrayFixture {

    const TEST_CODE = 1298371928;
    const TEST_KEY = 'zslmv;ldajf[voueldzn!_@_$(*&#_98275-9840vudgfhAS}PAOCNUREGFIUEWQOIHKJBDEV';
    const VALID = 'valid';
    
    public $dataFile = '@app/tests/inpsyde/nonce/data/nonce.php';
    
}