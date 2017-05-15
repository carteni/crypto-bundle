<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle;

use Defuse\Crypto\Exception\CryptoException as BaseCryptoException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Class EncryptionWrapper.
 */
final class EncryptionWrapper implements EncryptionInterface
{
    /**
     * @var EncryptionInterface
     */
    private $encryption;

    /**
     * EncryptionWrapper constructor.
     *
     * @param EncryptionInterface $encryption
     */
    public function __construct(EncryptionInterface $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function encryptWithKey($plaintext, KeyInterface $key)
    {
        try {
            return $this->encryption->encryptWithKey($plaintext, $key);
        } catch (EnvironmentIsBrokenException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function decryptWithKey($ciphertext, KeyInterface $key)
    {
        try {
            return $this->encryption->decryptWithKey($ciphertext, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function encryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key)
    {
        try {
            $this->encryption->encryptFileWithKey($inputFilename, $outputFilename, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function decryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key)
    {
        try {
            $this->encryption->decryptFileWithKey($inputFilename, $outputFilename, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * Encrypts a plaintext string using a secret password.
     *
     * @param string $plaintext String to encrypt
     * @param string $password  String containing the secret password used for encryption
     *
     * @return string A ciphertext string representing $plaintext encrypted with a key derived from $password
     */
    public function encryptWithPassword($plaintext, $password)
    {
        // TODO: Implement encryptWithPassword() method.
    }

    /**
     * Decrypts a ciphertext string using a secret password.
     *
     * @param string $ciphertext ciphertext to be decrypted
     * @param string $password   A string containing the secret password used for decryption
     *
     * @return string If the decryption succeeds, returns a string containing the same value as the string that was passed to encrypt() when $ciphertext was produced
     */
    public function decryptWithPassword($ciphertext, $password)
    {
        // TODO: Implement decryptWithPassword() method.
    }

    /**
     * Encrypts a file with a password.
     *
     * @param string $inputFilename  Path to a file containing the plaintext to encrypt
     * @param string $outputFilename Path to save the ciphertext file
     * @param string $password       The password used for decryption
     */
    public function encryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        // TODO: Implement encryptFileWithPassword() method.
    }

    /**
     * Decrypts a file with a password.
     *
     * @param string $inputFilename  Path to a file containing the ciphertext to decrypt
     * @param string $outputFilename Path to save the decrypted plaintext file
     * @param string $password       The password used for decryption
     */
    public function decryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        // TODO: Implement decryptFileWithPassword() method.
    }
}
