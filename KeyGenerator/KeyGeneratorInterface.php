<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\KeyGenerator;

use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Interface KeyGeneratorInterface.
 */
interface KeyGeneratorInterface
{
    /**
     * Generates a new random key and returns an instance of KeyInterface.
     * If the secret string is supplied generates a new random key that's protected by the string $secret.
     *
     * @param string|null $secret The secret string to protect the key
     *
     * @return KeyInterface An instance of KeyInterface containing a randomly-generated encryption key
     */
    public function generate($secret = null);

    /**
     * Loads an instance of KeyInterface that was saved to a string by KeyInterface::getEncoded.
     * By default, this function will remove trailing CR, LF, NUL, TAB, and SPACE characters, which are commonly appended to files when working with text editors.
     * If the secret string is supplied returns an instance of secret-protected KeyInterface.
     *
     * @param string      $key_encoded The string returned from KeyInterface::getEncoded when the original KeyInterface instance was saved
     * @param string|null $secret      The secret string to protect the key
     *
     * @return KeyInterface An instance of KeyInterface representing the same encryption key as the one that was represented by the KeyInterface instance that got saved into by a call to KeyInterface::getEncoded
     */
    public function generateFromAscii($key_encoded, $secret = null);
}
