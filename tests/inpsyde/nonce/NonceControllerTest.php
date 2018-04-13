<?php
namespace inpsyde\nonce;

use PHPUnit\Framework\TestCase;

/**
 * Nonce test case.
 */
class NonceControllerTest extends TestCase
{
    const CODE = 1098173049;
    const KEY_A = 'bag pack gin fonic';
    const KEY_B = 'web back zin monic';

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp()
    {
        Nonce::deleteAll();
    }

    public function testExpiration() {
        $controller = new NonceController();
        $controller->setNonceGenerator(new TestNonceCGenerator());
        
        $timeout = 2;
        $t = time();
        $expTime = $controller->getExpirationTime($t - TestNonceCGenerator::TIMEOUT + $timeout);
        $this->assertEquals($expTime, $t + $timeout);

        $test = new Nonce(1, "a", $expTime);
        $this->assertNotTrue($test->isExpired(), "expiration $expTime : " . $t);
        $this->assertTrue($test->save(true), implode(", ", $test->getErrorSummary(true)));
        $this->assertEquals($test->expires, $expTime);
        $this->assertTrue($test->verifyIt());
        
        sleep($timeout);
        $this->assertTrue($test->isExpired(), "expiration $expTime : $t, " . time());
        $this->assertNotTrue($test->verifyIt());

        Nonce::deleteAll([Nonce::CODE => 1]);
        $test = new Nonce(1, "a", time()-1);
        $this->assertNotTrue($test->save(true));
        $this->assertTrue($test->isExpired());
    }

    public function testUnique() {

        $controller = new NonceController();
        $controller->setNonceGenerator(new TestNonceCGenerator());
        $codes = [];
        $limit = NonceTest::MAX;
        while (count($codes) < $limit) {
            $code = $controller->createUniqueCode();
            $this->assertNotTrue(key_exists("code:$code", $codes), "is unique new code: " . $code . " from: [" . implode(", ", $codes) . "]");
            $nonce = new Nonce($code, "x");
            $this->assertTrue($nonce->save(true));
            array_push($codes, "code:$code");
        }
    }

    public function testGeneration() {

        $controller = new NonceController();
        $controller->setNonceGenerator(new TestNonceCGenerator(10));
        $codes = [];

        $codea = $controller->createNonce(self::KEY_A);
//         array_push($codes, $codea);

        $noncea = Nonce::getModel($codea);
        $this->assertNotNull($noncea);
        $this->assertEquals($codea, $noncea->nonce);
        $this->assertEquals(self::KEY_A, $noncea->key);

        $this->assertNotTrue($noncea->isExpired());
        $this->assertTrue($noncea->verifyIt());
        $this->assertTrue(Nonce::verify($codea, self::KEY_A));

        $codeadiff = $controller->createNonce(self::KEY_A);
//         array_push($codes, $codeadiff);

        $this->assertNotEquals($codeadiff, $codea);
        $this->assertTrue(Nonce::verify($codeadiff, self::KEY_A));

        $codeb = $controller->createNonce(self::KEY_B);
//         array_push($codes, $codeb);
        
        $nonceb = Nonce::getModel($codeb);
        $this->assertNotNull($nonceb);
        $this->assertEquals($codeb, $nonceb->nonce);
        $this->assertEquals(self::KEY_B, $nonceb->key);
        $this->assertTrue($nonceb->verifyIt());

        $this->assertTrue($codeb != $codea);
        $this->assertTrue($codeb != $codeadiff);
        $this->assertTrue(self::KEY_B != self::KEY_A);

        $this->assertNotTrue(Nonce::verify($codeb, self::KEY_A));
        $this->assertNotTrue(Nonce::verify($codea, self::KEY_B));

        $this->assertTrue(Nonce::verify($codeb, self::KEY_B));
        $this->assertTrue(Nonce::verify($codea, self::KEY_A));
    }
    
//     private static function verify($code, $key) {
//         return Nonce::verify($code, $key);
//     }

    public function testVerification() {
        $controller = new NonceController();
        $controller->setNonceGenerator(new TestNonceCGenerator());
        $codea = 1;
        $codeb = 2;
        
        $key = "abc";
        $code = $controller->createNonce($key);
        for ($i = TestNonceCGenerator::MIN; $i <= TestNonceCGenerator::MAX; $i++) {
            $this->assertNotTrue($i != $code && $controller->verifyNonce($i, $key));
        }
        $this->assertTrue($controller->verifyNonce($code, $key));
        
        //deleted!
        $this->assertTrue(NonceController::isUniqueNonce($code));
        
        $test = new Nonce($code, $key, time() + 1);
        $this->assertTrue($test->save(true));
        $this->assertTrue($controller->verifyNonce($code, $key));
        sleep(1);
        //expired!
        $this->assertNotTrue($controller->verifyNonce($code, $key));
    }
}

class TestNonceCGenerator implements iNonceGenerator {

  const MAX = 10;
  const MIN = 0;
  const TIMEOUT = 2;
  
  public function __construct($max = self::MAX, $min = self::MIN) {
    $this->max = $max;
    $this->min = $min;
  }
  public function generateNonce($message = null): int {
        $r = rand($this->min, $this->max);
        return $r;
    }

    public function getExpiration($timeCreated): int {
        return $timeCreated + self::TIMEOUT;
    }
}
