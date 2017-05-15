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

use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Mes\Security\CryptoBundle\Encryption;
use Mes\Security\CryptoBundle\EncryptionInterface;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;
use Mes\Security\CryptoBundle\KeyGenerator\KeyGeneratorInterface;
use Mes\Security\CryptoBundle\Model\KeyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class EncryptionTest.
 */
class EncryptionTest extends TestCase
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
    public function testEncryptWithKeyEncryptsPlaintext()
    {
        $key = $this->generator->generate();
        $plaintext = 'The quick brown fox jumps over the lazy dog';
        $ciphertext = $this->encryption->encryptWithKey($plaintext, $key);

        $this->assertTrue(ctype_print($ciphertext), 'is printable');

        return array(
            'ciphertext' => $ciphertext,
            'key_encoded' => $key->getEncoded(),
        );
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintext
     *
     * @param $args
     */
    public function testDecryptWithKeyDecryptsCiphertext($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded']);
        $plaintext = $this->encryption->decryptWithKey($args['ciphertext'], $key);

        $this->assertSame('The quick brown fox jumps over the lazy dog', $plaintext);
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintext
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptThrowsExceptionBecauseCiphertextIsCorrupted($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded']);
        $this->encryption->decryptWithKey($args['ciphertext'].'{FakeString}', $key);
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintext
     *
     * @expectedException \Defuse\Crypto\Exception\BadFormatException
     *
     * @param $args
     */
    public function testDecryptWithKeyThrowsExceptionBecauseKeyIsCorrupted($args)
    {
        $key = $this->generator->generateFromAscii($args['key_encoded'].'{FakeString}');
        $this->encryption->decryptWithKey($args['ciphertext'], $key);
    }

    /**
     * @return array
     */
    public function testEncryptWithKeyEncryptsPlaintextWithPassword()
    {
        $key = $this->generator->generate('ThisIsASecretPassword');

        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $key->getRawKey());
        $this->assertSame('ThisIsASecretPassword', $key->getSecret());

        $plaintext = 'The quick brown fox jumps over the lazy dog';
        $ciphertext = $this->encryption->encryptWithKey($plaintext, $key);

        $this->assertInstanceOf('\Defuse\Crypto\Key', $key->getRawKey());
        $this->assertTrue(ctype_print($ciphertext), 'is printable');
        $this->assertTrue(ctype_print($key->getEncoded()), 'is printable');

        return array(
            'ciphertext' => $ciphertext,
            'key_encoded' => $key->getEncoded(),
            'secret' => $key->getSecret(),
        );
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintextWithPassword
     *
     * @param $args
     */
    public function testDecryptWithKeyDecryptsCiphertextWithPassword($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret']);

        $this->assertInstanceOf('\Defuse\Crypto\KeyProtectedByPassword', $keyFromAscii->getRawKey());
        $this->assertSame($args['secret'], $keyFromAscii->getSecret());

        $plaintext = $this->encryption->decryptWithKey($args['ciphertext'], $keyFromAscii);

        $this->assertSame('The quick brown fox jumps over the lazy dog', $plaintext);
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptWithKeyThrowsExceptionWithCiphertextWithPasswordBecauseSecretIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret'].'{FakeString}');

        $this->encryption->decryptWithKey($args['ciphertext'], $keyFromAscii);
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\BadFormatException
     *
     * @param $args
     */
    public function testDecryptWithKeyThrowsExceptionWithCiphertextWithPasswordBecauseKeyIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'].'{FakeString}', $args['secret']);

        $this->encryption->decryptWithKey($args['ciphertext'], $keyFromAscii);
    }

    /**
     * @depends testEncryptWithKeyEncryptsPlaintextWithPassword
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptWithKeyThrowsExceptionWithCiphertextWithPasswordBecauseCiphertextIsCorrupted($args)
    {
        $keyFromAscii = $this->generator->generateFromAscii($args['key_encoded'], $args['secret']);

        $this->encryption->decryptWithKey($args['ciphertext'].'{FakeString}', $keyFromAscii);
    }

    /**
     * @return array
     */
    public function testEncryptWithKeyFileEncryptsFile()
    {
        /** @var KeyInterface $key */
        $key = $this->generator->generate('CryptoSecret');

        // Create file to encrypt.
        $tmpfname = tempnam(__DIR__, 'CRYPTO_');
        $plainContent = "Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.";
        $handle = fopen($tmpfname, 'w');
        fwrite($handle, $plainContent);
        fclose($handle);

        $filename = md5(uniqid());
        $encryptedFilename = __DIR__."/ENCRYPTED_$filename.crypto";

        $this->encryption->encryptFileWithKey($tmpfname, $encryptedFilename, $key);

        $this->assertFileExists($encryptedFilename, sprintf('%s file must exists', $encryptedFilename));
        $this->assertGreaterThan(0, (new \SplFileInfo($encryptedFilename))->getSize());

        unlink($tmpfname);

        return array(
            'key' => $key->getEncoded(),
            'secret' => $key->getSecret(),
            'encryptedFile' => $encryptedFilename,
        );
    }

    /**
     * @depends testEncryptWithKeyFileEncryptsFile
     *
     * @param $args
     */
    public function testDecryptWithKeyFileDecryptsEncryptedFile($args)
    {
        /** @var KeyInterface $key */
        $key = $this->generator->generateFromAscii($args['key'], $args['secret']);

        $tmpDecryptedFile = tempnam(__DIR__, '_CRYPTO');

        $this->encryption->decryptFileWithKey($args['encryptedFile'], $tmpDecryptedFile, $key);

        $this->assertFileExists($tmpDecryptedFile);
        $this->assertGreaterThan(0, (new \SplFileInfo($tmpDecryptedFile))->getSize());
        $this->assertContains("Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.", file_get_contents($tmpDecryptedFile));

        unlink($tmpDecryptedFile);
        unlink($args['encryptedFile']);
    }

    /**
     * @return array
     */
    public function testEncryptWithPasswordEncryptsPlaintext()
    {
        $plaintext = 'The quick brown fox jumps over the lazy dog';
        $ciphertext = $this->encryption->encryptWithPassword($plaintext, 'SuperSecretPa$$word');

        $this->assertTrue(ctype_print($ciphertext), 'is printable');

        return array(
            'ciphertext' => $ciphertext,
        );
    }

    /**
     * @depends testEncryptWithPasswordEncryptsPlaintext
     *
     * @param $args
     */
    public function testDecryptWithPasswordDecryptsCiphertext($args)
    {
        $plaintext = $this->encryption->decryptWithPassword($args['ciphertext'], 'SuperSecretPa$$word');

        $this->assertSame('The quick brown fox jumps over the lazy dog', $plaintext, sprintf("'%s' is correct.", $plaintext));
    }

    /**
     * @depends testEncryptWithPasswordEncryptsPlaintext
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     */
    public function testDecryptWithPasswordThrowsException($args)
    {
        $this->encryption->decryptWithPassword($args['ciphertext'], 'SuperSecretPa$$wordIncorrect');
    }

    /**
     * @return array
     */
    public function testEncryptFileWithPasswordEncryptsFile()
    {
        // Create file to encrypt.
        $tmpfname = tempnam(__DIR__, 'CRYPTO_');
        $plainContent = "Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.";
        $handle = fopen($tmpfname, 'w');
        fwrite($handle, $plainContent);
        fclose($handle);

        $filename = md5(uniqid());
        $encryptedFilename = __DIR__."/ENCRYPTED_$filename.crypto";

        $this->encryption->encryptFileWithPassword($tmpfname, $encryptedFilename, 'SuperSecretPa$$word');

        $this->assertFileExists($encryptedFilename, sprintf('%s file must exists', $encryptedFilename));
        $this->assertGreaterThan(0, (new \SplFileInfo($encryptedFilename))->getSize());

        unlink($tmpfname);

        return array(
            'encryptedFile' => $encryptedFilename,
        );
    }

    /**
     * @depends testEncryptFileWithPasswordEncryptsFile
     *
     * @param $args
     */
    public function testDecryptFileWithPasswordDecryptsEncryptedFile($args)
    {
        $tmpDecryptedFile = tempnam(__DIR__, '_CRYPTO');

        $this->encryption->decryptFileWithPassword($args['encryptedFile'], $tmpDecryptedFile, 'SuperSecretPa$$word');

        $this->assertFileExists($tmpDecryptedFile);
        $this->assertGreaterThan(0, (new \SplFileInfo($tmpDecryptedFile))->getSize());
        $this->assertContains("Dinanzi a me non fuor cose create se non etterne, e io etterno duro. Lasciate ogni speranza, voi ch'intrate.", file_get_contents($tmpDecryptedFile));

        unlink($tmpDecryptedFile);
    }

    /**
     * @depends testEncryptFileWithPasswordEncryptsFile
     *
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     *
     * @param $args
     *
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function testDecryptFileWithPasswordThrowsExceptionDecryptsEncryptedFile($args)
    {
        $tmpDecryptedFile = tempnam(__DIR__, '_CRYPTO');

        try {
            $this->encryption->decryptFileWithPassword($args['encryptedFile'], $tmpDecryptedFile, 'SuperSecretPa$$wordIncorrect');
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            unlink($tmpDecryptedFile);
            unlink($args['encryptedFile']);

            throw $e;
        }
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
