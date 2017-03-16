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
 * Class AbstractEncryption.
 */
abstract class AbstractEncryption implements EncryptionInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function encrypt($plaintext, KeyInterface $key);

    /**
     * {@inheritdoc}
     */
    abstract public function decrypt($ciphertext, KeyInterface $key);
}
