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

use Defuse\Crypto\Exception\CryptoException as BaseCryptoException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\KeyManagerInterface;
use Mes\Security\CryptoBundle\KeyManagerWrapper;
use Mes\Security\CryptoBundle\Model\KeySecretAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class KeyManagerWrapperTest.
 */
class KeyManagerWrapperTest extends TestCase
{
    /**
     * @var KeyManagerInterface|KeySecretAwareInterface
     */
    private $wrapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $keyManager;

    public function testGenerateCreatesKey()
    {
        $this->keyManager->expects($this->once())
                         ->method('generate')
                         ->with(null)
                         ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                        ->getMock()));

        $key = $this->wrapper->generate();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testGenerateThrowsException()
    {
        try {
            $this->keyManager->expects($this->once())
                             ->method('generate')
                             ->with(null)
                             ->will($this->throwException(new EnvironmentIsBrokenException()));
        } catch (EnvironmentIsBrokenException $ex) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->generate();
    }

    public function testGenerateFromAsciiCreatesKeyFromAscii()
    {
        $key_encoded = 'key_encoded';
        $secret = 'ThisIsASecretPassword';

        $this->keyManager->expects($this->once())
                         ->method('generateFromAscii')
                         ->with($key_encoded, $secret)
                         ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                        ->getMock()));

        $key = $this->wrapper->generateFromAscii($key_encoded, $secret);

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testGenerateFromAsciiThrowsException()
    {
        $key_encoded = 'key_encoded';
        $secret = 'ThisIsASecretPassword';

        try {
            $this->keyManager->expects($this->once())
                             ->method('generateFromAscii')
                             ->with($key_encoded, $secret)
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (CryptoException $ex) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->generateFromAscii($key_encoded, $secret);
    }

    public function testGetKeyReadsKey()
    {
        $this->keyManager->expects($this->once())
                         ->method('getKey')
                         ->will($this->returnValue($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                        ->getMock()));

        $key = $this->wrapper->getKey();

        $this->assertInstanceOf('Mes\Security\CryptoBundle\Model\KeyInterface', $key);
    }

    public function testSetKeyStoresKey()
    {
        $this->keyManager->expects($this->once())
                         ->method('setKey')
                         ->with($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                     ->getMock());

        $this->wrapper->setKey($this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                    ->getMock());
    }

    public function testSetSecretStoresSecret()
    {
        $this->keyManager->expects($this->once())
                         ->method('setSecret')
                         ->with('ThisIsASecret');

        $this->wrapper->setSecret('ThisIsASecret');
    }

    public function testGetSecretReadsSecret()
    {
        $this->keyManager->expects($this->once())
                         ->method('getSecret')
                         ->will($this->returnValue('ThisIsYourSecret'));

        $secret = $this->wrapper->getSecret();

        $this->assertSame('ThisIsYourSecret', $secret);
    }

    protected function setUp()
    {
        $this->keyManager = $this->getMockBuilder('\Mes\Security\CryptoBundle\KeyManagerInterface')
                                 ->getMock();
        $this->wrapper = new KeyManagerWrapper($this->keyManager);
    }

    protected function tearDown()
    {
        $this->wrapper = null;
    }
}
