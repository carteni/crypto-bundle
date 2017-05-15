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

use Defuse\Crypto\Crypto as BaseCrypto;
use Defuse\Crypto\File as BaseCryptoFile;
use Mes\Security\CryptoBundle\Model\Key;
use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Class Encryption.
 */
final class Encryption extends AbstractEncryption
{
    /**
     * {@inheritdoc}
     *
     * @throw \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptWithKey($plaintext, KeyInterface $key)
    {
        return BaseCrypto::encrypt($plaintext, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptWithKey($ciphertext, KeyInterface $key)
    {
        return BaseCrypto::decrypt($ciphertext, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key)
    {
        BaseCryptoFile::encryptFile($inputFilename, $outputFilename, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptFileWithKey($inputFilename, $outputFilename, KeyInterface $key)
    {
        BaseCryptoFile::decryptFile($inputFilename, $outputFilename, $this->unlockKey($key));
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

    /**
     * @param Key|KeyInterface $key
     *
     * @return \Defuse\Crypto\Key|\Defuse\Crypto\KeyProtectedByPassword
     */
    private function unlockKey(Key $key)
    {
        return $key->unlock()
                   ->getRawKey();
    }
}
