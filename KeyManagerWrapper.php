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

use Defuse\Crypto\Exception\CryptoException as BaseCryptoException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Mes\Security\CryptoBundle\Exception\CryptoException;
use Mes\Security\CryptoBundle\Model\KeyInterface;
use Mes\Security\CryptoBundle\Model\KeySecretAwareInterface;

/**
 * Class KeyManagerWrapper.
 */
class KeyManagerWrapper implements KeyManagerInterface
{
    /**
     * @var KeyManagerInterface|KeySecretAwareInterface
     */
    private $keyManager;

    /**
     * KeyManagerWrapper constructor.
     *
     * @param KeyManagerInterface $keyManager
     */
    public function __construct(KeyManagerInterface $keyManager)
    {
        $this->keyManager = $keyManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function generate($secret = null)
    {
        try {
            return $this->keyManager->generate($secret);
        } catch (EnvironmentIsBrokenException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throw CryptoException
     */
    public function generateFromAscii($key_encoded, $secret = null)
    {
        try {
            return $this->keyManager->generateFromAscii($key_encoded, $secret);
        } catch (BaseCryptoException $ex) {
            throw new CryptoException($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->keyManager->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(KeyInterface $key)
    {
        $this->keyManager->setKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret()
    {
        return $this->keyManager->getSecret();
    }

    /**
     * {@inheritdoc}
     */
    public function setSecret($secret)
    {
        $this->keyManager->setSecret($secret);
    }
}
