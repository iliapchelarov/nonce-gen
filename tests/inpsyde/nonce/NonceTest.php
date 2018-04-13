<?php

namespace inpsyde\nonce;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use yii\test\FixtureTrait;

/**
 * Nonce test case.
 */
class NonceTest extends TestCase
{
    use FixtureTrait;

    public function fixtures()
    {
        return [
            'nonces' => [
                'class' => NonceFixture::className(),
            ],
        ];
    }
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadFixtures();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    const TIMEOUT = 3;
    const MIN = 0;
    const MAX = 10;

    private function seedData() {
        return array(
            Nonce::CODE => NonceFixture::TEST_CODE,
            Nonce::KEY  => NonceFixture::TEST_KEY,
            NONCE::TIME => time(),
        );
    }

    /**
     *
     */
    public function testValidate() {
        Nonce::deleteAll();

        $nonces = $this->getFixture('nonces');

        $this->assertNotNull($nonces);
        foreach ($nonces as $n) {
            $nonce = new Nonce();
            $nonce->setAttributes($n);
            $this->assertEquals($n[NonceFixture::VALID], $nonce->validate(), implode(", ", $nonce->getErrorSummary(true)));
        }

    }

    /**
     * Test create & persist Nonce
     */
    public function testConstruct()
    {
        Nonce::deleteAll();

        $nonce = new Nonce();
        $this->assertTrue($nonce != null);
        $this->assertNotTrue($nonce->validate());
        $nonce->setAttributes($this->seedData());
        $this->assertTrue($nonce->save(true));
    }

    /**
     * @depends testConstruct
     *
     * Nonce usage process
      1. can create
      2. is unique
      3. is valid - not expired
      4. test verify correct key
      5. test not verify different key
      6. test expire
      7. test is deleted after use (usable only once) - tested in NonceControllerTest

     */
    public function testNonceProcess()
    {
      $generator = new TestNonceGenerator();
      $code = $generator->generateNonce() + 100;
      $timeout = 2;

      $nonce = new Nonce(
          $code,
          NonceFixture::TEST_KEY,
          time() + $timeout
        );
      
      $this->assertTrue($nonce->save(true), implode(", ", $nonce->getErrorSummary(true)));

      try {
        $n = new Nonce(NonceFixture::TEST_CODE);
        $n->expectUnique();
        $this->fail("not unique");
      } catch (InvalidArgumentException $e) {
          $this->assertTrue(true);
      }
      
      $this->assertTrue($nonce->expires > time(), "expires: " . $nonce->expires . ": " . time());
      $this->assertNotTrue($nonce->isExpired());
       
      $this->assertTrue($nonce->verifyIt());
      
      for ($i = 0; $i < self::MAX; $i++) {
          $r = $generator->generateNonce() + 10;
          $this->assertNotTrue(Nonce::verify($code + $r, $nonce->key));
      }
      
      sleep($timeout);
      $this->assertTrue($nonce->isExpired());
      
    }

    /**
     * @depends testConstruct
     */
    public function testNonceInitValidation() {
      $existing = 1298371928;
      $key = 'aaaaaxaaaaaaa';
      $expires = time() + self::TIMEOUT;
      $code = 1298371929;
      $n = new Nonce();

      // missing properties
      $this->assertNotTrue($n->validate());

      //missing nonce
      $n->setAttributes([
         'nonce' => null,
         'key' => $key,
         'expires' => $expires,
      ]);
      $this->assertNotTrue($n->validate());
      $this->assertArrayHasKey('nonce', $n->getErrors());
      $n->nonce = (string) $code;
      $this->assertTrue($n->validate());
      

      //missing key
      $n->setAttributes([
         'nonce' => $code,
         'key' => null,
         'expires' => $expires,
      ]);
      $this->assertNotTrue($n->validate());
      $this->assertArrayHasKey('key', $n->getErrors());
      $n->key = $key;
      $this->assertTrue($n->validate());
      

      //missing expires - should be default time()
      $n->setAttributes([
         'nonce' => $code,
         'key' => $key,
         'expires' => null,
      ]);
      $this->assertTrue($n->validate());
      $this->assertTrue($n->expires - time() - Nonce::TIMEOUT <= 1);

      // not unique
      $n->setAttributes([
         'nonce' => $existing,
         'key' => $key,
         'expires' => $expires,
      ]);
      //nonce exists
      $this->assertNotTrue($n->validate());
      $this->assertArrayHasKey('nonce', $n->getErrors());
      $n->nonce = (string) $code;
      $this->assertTrue($n->validate());
      

      $n->setAttributes([
         'nonce' => $code,
         'key' => $key,
         'expires' => 123,
      ]);
      //expires timestamp expired
      $this->assertNotTrue($n->validate());
      $this->assertArrayHasKey('expires', $n->getErrors());
      $n->expires = time();
      $this->assertTrue($n->validate());
      
    }

    static function generateCode() {
        $generator = new SimpleNonceGenerator();
        return $generator->generateNonce();
    }

    function testUseOnce() {
        $key = NonceFixture::TEST_KEY;
        $code = self::generateCode();
        
        $n = new Nonce();
        $n->setValues($code, $key);
        $this->assertTrue($n->save(true));
        $this->assertTrue($n->verifyIt());

        //same code - different key (not verified)
        $n = new Nonce();
        $n->setValues($code, $key . "a");

        $this->assertNotTrue($n->verifyIt());
        $this->assertNotTrue($n->save(true));

        //same key - different code (verifies if saved beforehand)
        $n = new Nonce();
        $n->setValues($code + 1, $key);

        $this->assertNotTrue($n->verifyIt());
        $this->assertTrue($n->save(true));
        $this->assertTrue($n->verifyIt());

        //first code is still perserved and valid
        $m = Nonce::getModel($code);
        $this->assertNotNull($m);
        $this->assertTrue($m->verifyIt());
        //after use is deleted and no longer valid
        $m->delete();
        $this->assertNotTrue($m->verifyIt());
    }

    function testNonceExpiration() {
        $this->assertTrue((new Nonce(1, "a", time() - 1))->isExpired());
        $this->assertNotTrue((new Nonce(1, "a", time() + 1))->isExpired());
    }

}

class TestNonceGenerator implements iNonceGenerator {
    public function generateNonce($message = null): int {
        $r = rand(NonceTest::MIN, NonceTest::MAX);
        return $r;
    }

    public function getExpiration($timeCreated): int
    {
        return $timeCreated + NonceTest::TIMEOUT;
    }

}
