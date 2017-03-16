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
     * Creates a random key optionally protected by the provided secret.
     *
     * @param string|null $secret The secret to authenticate key
     *
     * @return KeyInterface
     */
    public function generate($secret = null);

    /**
     * @param $key_encoded
     * @param null $secret
     *
     * @return KeyInterface
     */
    public function generateFromAscii($key_encoded, $secret = null);
}
