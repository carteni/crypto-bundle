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
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
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
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptWithPassword($plaintext, $password)
    {
        return BaseCrypto::encryptWithPassword($plaintext, $password);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptWithPassword($ciphertext, $password)
    {
        return BaseCrypto::decryptWithPassword($ciphertext, $password);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        BaseCryptoFile::encryptFileWithPassword($inputFilename, $outputFilename, $password);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        BaseCryptoFile::decryptFileWithPassword($inputFilename, $outputFilename, $password);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptResourceWithKey($inputHandle, $outputHandle, KeyInterface $key)
    {
        BaseCryptoFile::encryptResource($inputHandle, $outputHandle, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptResourceWithKey($inputHandle, $outputHandle, KeyInterface $key)
    {
        BaseCryptoFile::decryptResource($inputHandle, $outputHandle, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptResourceWithPassword($inputHandle, $outputHandle, $password)
    {
        BaseCryptoFile::encryptResourceWithPassword($inputHandle, $outputHandle, $password);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptResourceWithPassword($inputHandle, $outputHandle, $password)
    {
        BaseCryptoFile::decryptResourceWithPassword($inputHandle, $outputHandle, $password);
    }

    /**
     * @param Key|KeyInterface $key The KeyInterface instance to unlock used for Defuse encryption system
     *
     * @return \Defuse\Crypto\Key|\Defuse\Crypto\KeyProtectedByPassword The Defuse key
     */
    private function unlockKey(Key $key)
    {
        return $key->unlock()
                   ->getRawKey();
    }
}
