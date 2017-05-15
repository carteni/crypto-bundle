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
 * Interface KeyInterface.
 */
interface KeyInterface extends KeySecretAwareInterface
{
    /**
     * Encodes the KeyInterface instance into a string of printable ASCII characters.
     *
     * @return string A string of printable ASCII characters representing this KeyInterface instance
     */
    public function getEncoded();
}
