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
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use encryptWithKey instead
     */
    public function encrypt($plaintext, KeyInterface $key)
    {
        @trigger_error('encrypt() is deprecated since version 1.2 and will be removed in 2.0. Use encryptWithKey instead.', E_USER_DEPRECATED);

        return BaseCrypto::encrypt($plaintext, $this->unlockKey($key));
    }

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
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use decryptWithKey instead
     */
    public function decrypt($ciphertext, KeyInterface $key)
    {
        @trigger_error('decrypt() is deprecated since version 1.2 and will be removed in 2.0. Use decryptWithKey instead.', E_USER_DEPRECATED);

        return BaseCrypto::decrypt($ciphertext, $this->unlockKey($key));
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
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use encryptFileWithKey instead
     */
    public function encryptFile($inputFilename, $outputFilename, KeyInterface $key)
    {
        @trigger_error('encryptFile() is deprecated since version 1.2 and will be removed in 2.0. Use encryptFileWithKey instead.', E_USER_DEPRECATED);

        BaseCryptoFile::encryptFile($inputFilename, $outputFilename, $this->unlockKey($key));
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
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use decryptFileWithKey instead
     */
    public function decryptFile($inputFilename, $outputFilename, KeyInterface $key)
    {
        @trigger_error('decryptFile() is deprecated since version 1.2 and will be removed in 2.0. Use decryptFileWithKey instead.', E_USER_DEPRECATED);

        BaseCryptoFile::decryptFile($inputFilename, $outputFilename, $this->unlockKey($key));
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
