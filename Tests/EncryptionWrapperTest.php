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
use Mes\Security\CryptoBundle\EncryptionWrapper;
use Mes\Security\CryptoBundle\Exception\CryptoException;

/**
 * Class EncryptionWrapperTest.
 */
class EncryptionWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mes\Security\CryptoBundle\EncryptionWrapper
     */
    private $wrapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $encryption;

    public function testEncryptEncryptsPlaintext()
    {
        $this->encryption->expects($this->once())
                         ->method('encrypt')
                         ->with('The quick brown fox jumps over the lazy dog', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'))
                         ->will($this->returnValue('ThisIsACipherText'));

        $ciphertext = $this->wrapper->encrypt('The quick brown fox jumps over the lazy dog', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'));

        $this->assertTrue(ctype_print($ciphertext), 'is printable');
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testEncryptEncryptsPlaintextThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('encrypt')
                             ->with('The quick brown fox jumps over the lazy dog', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'))
                             ->will($this->throwException(new EnvironmentIsBrokenException()));
        } catch (EnvironmentIsBrokenException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->encrypt('The quick brown fox jumps over the lazy dog', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'));
    }

    public function testDecryptDecryptsCiphertext()
    {
        $this->encryption->expects($this->once())
                         ->method('decrypt')
                         ->with('ThisIsACipherText', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'))
                         ->will($this->returnValue('The quick brown fox jumps over the lazy dog'));

        $decryptedText = $this->wrapper->decrypt('ThisIsACipherText', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'));

        $this->assertSame('The quick brown fox jumps over the lazy dog', $decryptedText);
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testDecryptDecryptsCiphertextThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('decrypt')
                             ->with('ThisIsACipherText', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'))
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (BaseCryptoException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->decrypt('ThisIsACipherText', $this->getMock('Mes\Security\CryptoBundle\Model\KeyInterface'));
    }

    protected function setUp()
    {
        $this->encryption = $this->getMockBuilder('Mes\Security\CryptoBundle\EncryptionInterface')
                                 ->getMock();
        $this->wrapper = new EncryptionWrapper($this->encryption);
    }

    protected function tearDown()
    {
        $this->wrapper = null;
        $this->encryption = null;
    }
}
