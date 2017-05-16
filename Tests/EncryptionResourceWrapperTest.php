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
use Mes\Security\CryptoBundle\EncryptionWrapper;
use Mes\Security\CryptoBundle\Exception\CryptoException;

/**
 * Class EncryptionResourceWrapperTest.
 */
class EncryptionResourceWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EncryptionWrapper
     */
    private $wrapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $encryption;

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

    /* ===========================================
     *
     * EncryptionInterface::EncryptResourceWithKey
     *
     * ===========================================
     */

    /**
     * @throws CryptoException
     */
    public function testEncryptResourceWithKeyEncryptsHandle()
    {
        $this->encryption->expects($this->once())
                         ->method('encryptResourceWithKey');

        $this->wrapper->encryptResourceWithKey(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), $this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                                                                            ->getMock());
    }

    /**
     * @throws CryptoException
     */
    public function testDecryptResourceWithKeyDecryptsEncryptedHandle()
    {
        $this->encryption->expects($this->once())
                         ->method('decryptResourceWithKey');

        $this->wrapper->decryptResourceWithKey(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), $this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                                                                            ->getMock());
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testEncryptResourceWithKeyThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('encryptResourceWithKey')
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (BaseCryptoException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->encryptResourceWithKey(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), $this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                                                                            ->getMock());
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testDecryptResourceWithKeyThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('decryptResourceWithKey')
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (BaseCryptoException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->decryptResourceWithKey(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), $this->getMockBuilder('Mes\Security\CryptoBundle\Model\KeyInterface')
                                                                                                            ->getMock());
    }

    /* ================================================
     *
     * EncryptionInterface::EncryptResourceWithPassword
     *
     * ================================================
     */

    /**
     * @throws CryptoException
     */
    public function testEncryptResourceWithPasswordEncryptsHandle()
    {
        $this->encryption->expects($this->once())
                         ->method('encryptResourceWithPassword');

        $this->wrapper->encryptResourceWithPassword(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), 'SecretPa$$word');
    }

    /**
     * @throws CryptoException
     */
    public function testDecryptResourceWithPasswordDecryptsEncryptedHandle()
    {
        $this->encryption->expects($this->once())
                         ->method('decryptResourceWithPassword');

        $this->wrapper->decryptResourceWithPassword(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), 'SecretPa$$word');
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testEncryptResourceWithPasswordThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('encryptResourceWithPassword')
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (BaseCryptoException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->encryptResourceWithPassword(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), 'SecretPa$$word');
    }

    /**
     * @expectedException \Mes\Security\CryptoBundle\Exception\CryptoException
     */
    public function testDecryptResourceWithPasswordThrowsException()
    {
        try {
            $this->encryption->expects($this->once())
                             ->method('decryptResourceWithPassword')
                             ->will($this->throwException(new BaseCryptoException()));
        } catch (BaseCryptoException $e) {
            $this->throwException(new CryptoException());
        }

        $this->wrapper->decryptResourceWithPassword(fopen('php://stdin', 'r'), fopen('php://memory', 'wb'), 'SecretPa$$word');
    }
}
