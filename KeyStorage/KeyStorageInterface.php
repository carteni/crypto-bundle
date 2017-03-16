<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\KeyStorage;

use Mes\Security\CryptoBundle\Model\KeyInterface;

/**
 * Interface KeyStorageInterface.
 */
interface KeyStorageInterface
{
    /**
     * Reads a stored Key.
     * If key is null, a new Random Key without authentication secret is
     * created and stored.
     *
     * @return KeyInterface
     */
    public function getKey();

    /**
     * Stores a Key.
     *
     * @param KeyInterface $key
     */
    public function setKey(KeyInterface $key);
}
