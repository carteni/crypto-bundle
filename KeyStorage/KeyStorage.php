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
 * Class KeyStorage (InMemory storage).
 */
class KeyStorage extends AbstractKeyStorage
{
    /**
     * @var KeyInterface
     */
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(KeyInterface $key)
    {
        return $this->key = $key;
    }
}
