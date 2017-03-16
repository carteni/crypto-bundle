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
 * Class AbstractKey.
 */
abstract class AbstractKey implements KeyInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getEncoded();

    /**
     * {@inheritdoc}
     */
    abstract public function getSecret();

    /**
     * {@inheritdoc}
     */
    abstract public function setSecret($secret);
}
