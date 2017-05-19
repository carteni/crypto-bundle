<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Loader;

/**
 * Interface CryptoLoaderInterface.
 */
interface CryptoLoaderInterface
{
    /**
     * Loads the encoded key (string of printable ASCII characters derived from KeyInterface instance).
     *
     * @return string The encoded key
     */
    public function loadKey();

    /**
     * Loads the secret string to generate the KeyInterface instance.
     *
     * @return string The secret
     */
    public function loadSecret();

    /**
     * Sets the resource from which loads the encoded key and the secret string.
     *
     * @param mixed $resource
     */
    public function setResource($resource);
}
