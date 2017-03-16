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

use Mes\Security\CryptoBundle\Encryption;
use Mes\Security\CryptoBundle\EncryptionInterface;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGeneratorInterface;

/**
 * Class EncryptionTest.
 */
class EncryptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EncryptionInterface
     */
    private $encryption;

    /**
     * @var KeyGeneratorInterface
     */
    private $generator;

    /**
     * @return array
     */
    public function testEncryptEncryptsPlaintext()
    {
        $key = $this->generator->generate();
        $plaintext = 'The quick brown fox jumps over the lazy dog';
        $ciphertext = $this->encryption->encrypt($plaintext, $key);

        $this->assertTrue(ctype_print($ciphertext), 'is printable');

        return array('ciphertext' => $ciphertext, 'key_encoded' => $key->getEncoded());
    }

    /**
     * @depends testEncryptEncryptsPlaintext
     *
     * @param $args
     */
    public function testDecryptDecryptsCiphertext($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded']);
        $plaintext = $this->encryption->decrypt($args['ciphertext'], $key);

        $this->assertSame('The quick brown fox jumps over the lazy dog', $plaintext);
    }

    /**
     * @depends testEncryptEncryptsPlaintext
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionBecauseCiphertextIsCorrupted($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded']);
        $this->encryption->decrypt($args['ciphertext'].'{FakeString}', $key);
    }

    /**
     * @depends testEncryptEncryptsPlaintext
     *
     * @expectedException \Defuse\Crypto\Exception\BadFormatException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionBecauseKeyIsCorrupted($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded'].'{FakeString}');
        $this->encryption->decrypt($args['ciphertext'], $key);
    }

    /**
     * @return array
     */
    public function testEncryptEncryptsPlaintextWithPassword()
    {
        $key = $this->generator->generate('ThisIsASecretPassword');

        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $key->getRawKey());
        $this->assertSame('ThisIsASecretPassword', $key->getSecret());

        $plaintext = 'The quick brown fox jumps over the lazy dog';
        $ciphertext = $this->encryption->encrypt($plaintext, $key);

        $this->assertInstanceOf('\Defuse\Crypto\Key', $key->getRawKey());
        $this->assertTrue(ctype_print($ciphertext), 'is printable');
        $this->assertTrue(ctype_print($key->getEncoded()), 'is printable');

        return array('ciphertext' => $ciphertext, 'key_encoded' => $key->getEncoded(), 'secret' => $key->getSecret());
    }

    /**
     * @depends testEncryptEncryptsPlaintextWithPassword
     *
     * @param $args
     */
    public function testDecryptDecryptsCiphertextWithPassword($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret']);

        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $keyFromAscii->getRawKey());
        $this->assertSame($args['secret'], $keyFromAscii->getSecret());

        $plaintext = $this->encryption->decrypt($args['ciphertext'], $keyFromAscii);

        $this->assertSame('The quick brown fox jumps over the lazy dog', $plaintext);
    }

    /**
     * @depends testEncryptEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionWithCiphertextWithPasswordBecauseSecretIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret'].'{FakeString}');

        $this->encryption->decrypt($args['ciphertext'], $keyFromAscii);
    }

    /**
     * @depends testEncryptEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\BadFormatException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionWithCiphertextWithPasswordBecauseKeyIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'].'{FakeString}', $args['secret']);

        $this->encryption->decrypt($args['ciphertext'], $keyFromAscii);
    }

    /**
     * @depends testEncryptEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionWithCiphertextWithPasswordBecauseCiphertextIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret']);

        $this->encryption->decrypt($args['ciphertext'].'{FakeString}', $keyFromAscii);
    }

    protected function setUp()
    {
        $this->encryption = new Encryption();
        $this->generator = new KeyGenerator();
    }

    protected function tearDown()
    {
        $this->encryption = null;
        $this->generator = null;
    }
}
