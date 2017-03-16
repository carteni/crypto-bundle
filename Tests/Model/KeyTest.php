<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests\Model;

use Defuse\Crypto\KeyProtectedByPassword;
use Mes\Security\CryptoBundle\Model\Key;

/**
 * Class KeyTest.
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCreatesKey()
    {
        /*
         * @var \Mes\Security\CryptoBundle\Model\Key
         */
        $key = Key::create(\Defuse\Crypto\Key::createNewRandomKey(), null);
        $key_encoded = $key->getEncoded();
        $rawKey = $key->getRawKey();

        $this->assertInstanceOf(Key::class, $key);

        $this->assertTrue(ctype_print($key_encoded), 'is printable');

        $this->assertInstanceOf(\Defuse\Crypto\Key::class, $rawKey);
    }

    public function testCreateCreatesKeyWithSecret()
    {
        $secret = 'ThisIsASecretPassword';

        /*
         * @var \Mes\Security\CryptoBundle\Model\Key
         */
        $key = Key::create(KeyProtectedByPassword::createRandomPasswordProtectedKey($secret), $secret);
        $key_encoded = $key->getEncoded();
        $secret = $key->getSecret();

        $this->assertInstanceOf(\Defuse\Crypto\KeyProtectedByPassword::class, $key->getRawKey());

        $this->assertInstanceOf(Key::class, $key);

        $this->assertTrue(ctype_print($key_encoded), 'is printable');

        $this->assertSame('ThisIsASecretPassword', $secret);

        $keyUnlocked = $key->unlock();

        $this->assertInstanceOf(\Defuse\Crypto\Key::class, $keyUnlocked->getRawKey());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSetSecretThrowsException()
    {
        $secret = 'ThisIsASecretPassword';

        /*
         * @var \Mes\Security\CryptoBundle\Model\Key
         */
        $key = Key::create(KeyProtectedByPassword::createRandomPasswordProtectedKey($secret), $secret);
        $key->setSecret('NewSecret');
    }
}
