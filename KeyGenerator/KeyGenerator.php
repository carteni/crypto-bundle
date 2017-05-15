<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\KeyGenerator;

use Defuse\Crypto\Key as BaseKey;
use Defuse\Crypto\KeyProtectedByPassword as BaseKeyProtectedByPassword;
use Mes\Security\CryptoBundle\Model\Key;

/**
 * Class KeyGenerator.
 */
final class KeyGenerator extends AbstractKeyGenerator
{
    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function generate($secret = null)
    {
        $key = null;

        if (null !== $secret) {
            $key = $this->createRandomPasswordProtectedKey($secret);
        }

        if (null === $key) {
            $key = $this->createNewRandomKey();
        }

        return Key::create($key, $secret);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function generateFromAscii($key_encoded, $secret = null)
    {
        $key = $this->loadFromAsciiSafeString($key_encoded, $secret);

        return Key::create($key, $secret);
    }

    /**
     * @param $secret
     *
     * @return BaseKeyProtectedByPassword
     */
    private function createRandomPasswordProtectedKey($secret)
    {
        return BaseKeyProtectedByPassword::createRandomPasswordProtectedKey($secret);
    }

    /**
     * @return BaseKey
     */
    private function createNewRandomKey()
    {
        return BaseKey::createNewRandomKey();
    }

    /**
     * @param string $key_encoded A string of printable ASCII characters representing a KeyInterface instance
     * @param string $secret      The secret string to make the secret-protected Key
     *
     * @return BaseKey|BaseKeyProtectedByPassword|null
     */
    private function loadFromAsciiSafeString($key_encoded, $secret)
    {
        $key = null;

        if (null !== $secret) {
            $key = BaseKeyProtectedByPassword::loadFromAsciiSafeString($key_encoded);
        }

        if (null === $key) {
            $key = BaseKey::loadFromAsciiSafeString($key_encoded);
        }

        return $key;
    }
}
