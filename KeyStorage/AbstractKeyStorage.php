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
 * Class AbstractKeyStorage.
 */
abstract class AbstractKeyStorage implements KeyStorageInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getKey();

    /**
     * {@inheritdoc}
     */
    abstract public function setKey(KeyInterface $key);
}
