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
     * Reads the Key Secret.
     *
     * @return string
     */
    public function getSecret();

    /**
     * Store the Key Secret.
     *
     * @param $secret
     */
    public function setSecret($secret);
}
