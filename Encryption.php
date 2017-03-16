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
    public function encrypt($plaintext, KeyInterface $key)
    {
        return BaseCrypto::encrypt($plaintext, $this->unlockKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decrypt($ciphertext, KeyInterface $key)
    {
        return BaseCrypto::decrypt($ciphertext, $this->unlockKey($key));
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
