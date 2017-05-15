<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Model;

/**
 * Interface KeySecretAwareInterface.
 */
interface KeySecretAwareInterface
{
    /**
     * Returns the secret used to make te Key for encryption.
     *
     * @return string The
     */
    public function getSecret();

    /**
     * Sets the secret to make the Key for encryption.
     *
     * @param string $secret Key secret
     */
    public function setSecret($secret);
}
