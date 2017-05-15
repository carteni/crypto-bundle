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

use Defuse\Crypto\Key as BaseKey;
use Defuse\Crypto\KeyProtectedByPassword as BaseKeyProtectedByPassword;

/**
 * Class Key.
 */
final class Key extends AbstractKey
{
    /**
     * @var BaseKey|BaseKeyProtectedByPassword
     */
    private $rawKey;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $keyRawEncoded;

    /**
     * Key constructor.
     *
     * @param BaseKey|BaseKeyProtectedByPassword $rawKey
     * @param $secret string The secret to make the Key for encryption
     */
    private function __construct($rawKey, $secret)
    {
        $this->rawKey = $rawKey;
        $this->secret = $secret;
        $this->keyRawEncoded = $rawKey->saveToAsciiSafeString();
    }

    /**
     * @internal
     *
     * @param BaseKey|BaseKeyProtectedByPassword $rawKey
     * @param string                             $secret The secret to make the Key for encryption
     *
     * @return KeyInterface
     */
    public static function create($rawKey, $secret)
    {
        return new self($rawKey, $secret);
    }

    /**
     * {@inheritdoc}
     */
    public function getEncoded()
    {
        return $this->keyRawEncoded;
    }

    /**
     * @internal
     *
     * @return BaseKey|BaseKeyProtectedByPassword
     */
    public function getRawKey()
    {
        return $this->rawKey;
    }

    /**
     * @internal
     *
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function unlock()
    {
        if (!($this->getRawKey() instanceof BaseKeyProtectedByPassword)) {
            return $this;
        }

        // rawKey is locked and become instance of \Defuse\Crypto\Key.
        $this->rawKey = $this->getRawKey()
                             ->unlockKey($this->getSecret());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     */
    public function setSecret($secret)
    {
        throw new \BadMethodCallException('Setting secret on a frozen Key is not allowed.');
    }
}
