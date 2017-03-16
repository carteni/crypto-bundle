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

use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Interface EncryptionInterface.
 */
interface EncryptionInterface
{
    /**
     * Encrypts a string with a Key.
     *
     * @param string       $plaintext
     * @param KeyInterface $key
     *
     * @return string
     */
    public function encrypt($plaintext, KeyInterface $key);

    /**
     * @param string            $ciphertext
     * @param KeyInterface|null $key
     *
     * @return string
     */
    public function decrypt($ciphertext, KeyInterface $key);
}
