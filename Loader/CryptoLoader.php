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
 * Class CryptoLoader.
 */
class CryptoLoader
{
    private $resource;

    private $config;

    /**
     * KeyLoader constructor.
     *
     * @param $resource
     *
     * @throw \RuntimeException|\InvalidArgumentException
     */
    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->config = array();

        $conditionSatisfied = (is_file($resource) && is_readable($resource)) && 'crypto' === pathinfo($this->resource, PATHINFO_EXTENSION);

        if ($conditionSatisfied) {
            $result = parse_ini_file($this->resource, true);

            if (false === $result || array() === $result) {
                throw new \RuntimeException(sprintf('The "%s" file is not correctly formatted. ini format is expected.', $this->resource));
            }

            $this->config = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('The "%s" file is not valid.', $this->resource));
        }
    }

    /**
     * @return string
     *
     * @throw \UnexpectedValueException
     */
    public function loadKey()
    {
        try {
            return $this->config['crypto']['key'];
        } catch (\Exception $ex) {
            throw new \UnexpectedValueException('The configuration value "key" is missing.');
        }
    }

    /**
     * @return string
     *
     * @throw \UnexpectedValueException
     */
    public function loadSecret()
    {
        try {
            return $this->config['crypto']['secret'];
        } catch (\Exception $ex) {
            throw new \UnexpectedValueException('The configuration value "secret" is missing.');
        }
    }
}
