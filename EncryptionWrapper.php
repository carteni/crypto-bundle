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
     * @throws CryptoException
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use encryptWithKey instead
     */
    public function encrypt($plaintext, KeyInterface $key)
    {
        @trigger_error('encrypt() is deprecated since version 1.2 and will be removed in 2.0. Use encryptWithKey instead.', E_USER_DEPRECATED);

        try {
            return $this->encryption->encrypt($plaintext, $key);
        } catch (EnvironmentIsBrokenException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
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
     * @throws CryptoException
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use decryptWithKey instead
     */
    public function decrypt($ciphertext, KeyInterface $key)
    {
        @trigger_error('decrypt() is deprecated since version 1.2 and will be removed in 2.0. Use decryptWithKey instead.', E_USER_DEPRECATED);

        try {
            return $this->encryption->decrypt($ciphertext, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
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
     * @throws CryptoException
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use encryptFileWithKey instead
     */
    public function encryptFile($inputFilename, $outputFilename, KeyInterface $key)
    {
        @trigger_error('encryptFile() is deprecated since version 1.2 and will be removed in 2.0. Use encryptFileWithKey instead.', E_USER_DEPRECATED);

        try {
            $this->encryption->encryptFile($inputFilename, $outputFilename, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
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
     * @throws CryptoException
     *
     * @deprecated since version 1.2, to be removed in 2.0. Use decryptFileWithKey instead
     */
    public function decryptFile($inputFilename, $outputFilename, KeyInterface $key)
    {
        @trigger_error('decryptFile() is deprecated since version 1.2 and will be removed in 2.0. Use decryptFileWithKey instead.', E_USER_DEPRECATED);

        try {
            $this->encryption->decryptFile($inputFilename, $outputFilename, $key);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
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
     * {@inheritdoc}
     *
     * @throws CryptoException
     */
    public function encryptWithPassword($plaintext, $password)
    {
        try {
            return $this->encryption->encryptWithPassword($plaintext, $password);
        } catch (EnvironmentIsBrokenException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
     */
    public function decryptWithPassword($ciphertext, $password)
    {
        try {
            return $this->encryption->decryptWithPassword($ciphertext, $password);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
     */
    public function encryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        try {
            return $this->encryption->encryptFileWithPassword($inputFilename, $outputFilename, $password);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws CryptoException
     */
    public function decryptFileWithPassword($inputFilename, $outputFilename, $password)
    {
        try {
            return $this->encryption->decryptFileWithPassword($inputFilename, $outputFilename, $password);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }
}
