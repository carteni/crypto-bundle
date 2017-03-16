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

use Mes\Security\CryptoBundle\KeyGenerator\KeyGeneratorInterface;
use Mes\Security\CryptoBundle\KeyStorage\KeyStorageInterface;
use Mes\Security\CryptoBundle\Model\KeySecretAwareInterface;

/**
 * Interface KeyManagerInterface.
 */
interface KeyManagerInterface extends KeyStorageInterface, KeyGeneratorInterface, KeySecretAwareInterface
{
}
