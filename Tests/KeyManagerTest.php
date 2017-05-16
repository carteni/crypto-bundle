<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests;

use Mes\Security\CryptoBundle\KeyManager;

/**
 * Class KeyManagerTest.
 */
class KeyManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $keyStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $keyGenerator;

    /**
     * @var KeyManager
     */
    private $keyManager;

    protected function setUp()
    {
        $this->keyStorage = $this->getMockBuilder('Mes\Security\CryptoBundle\KeyStorage\KeyStorageInterface')
                                 ->getMock();

        $this->keyGenerator = $this->getMockBuilder('Mes\Security\CryptoBundle\KeyGenerator\KeyGeneratorInterface')
                                   ->getMock();

        $this->keyManager = new KeyManager($this->keyStorage, $this->keyGenerator);
    }

    protected function tearDown()
    {
        $this->keyGenerator = null;
        $this->keyStorage = null;
        $this->keyManager = null;
    }

    public function testGenerateCreatesKey()
    {
        $this->keyGenerator->expects($this->once())
                           ->method('generate')
                           ->with(null)
                           ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')->getMock()));

        $key = $this->keyManager->generate();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    public function testGeneratedFromAsciiCreatesKeyFromAscii()
    {
        $key_encoded = 'key_encoded';
        $secret = 'ThisIsASecretPassword';

        $this->keyGenerator->expects($this->once())
                           ->method('generateFromAscii')
                           ->with($key_encoded, $secret)
                           ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')->getMock()));

        $key = $this->keyManager->generateFromAscii($key_encoded, $secret);

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    public function testGetKeyReadsNotExistingKey()
    {
        $this->keyStorage->expects($this->once())
                         ->method('getKey')
                         ->will($this->returnValue(null));

        $this->keyGenerator->expects($this->once())
                           ->method('generate')
                           ->with(null)
                           ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                          ->getMock()));

        $this->keyStorage->expects($this->once())
                         ->method('setKey');

        $key = $this->keyManager->getKey();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    public function testGetKeyReadsExistingKey()
    {
        $this->keyStorage->expects($this->never())
                         ->method('setKey');

        $this->keyGenerator->expects($this->never())
                           ->method('generate');

        $this->keyStorage->expects($this->once())
                         ->method('getKey')
                         ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                        ->getMock()));

        $key = $this->keyManager->getKey();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    public function testSetKeyStoresKey()
    {
        $this->keyStorage->expects($this->once())
                         ->method('setKey')
                         ->with($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                     ->getMock());

        $this->keyManager->setKey($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                       ->getMock());
    }

    public function testSetSecretStoreSecret()
    {
        $this->keyManager->setSecret('ThisIsASecret');

        $this->assertSame('ThisIsASecret', $this->keyManager->getSecret());
    }
}
