<?php
namespace Mes\Security\CryptoBundle\Loader;

/**
 * Interface CryptoLoaderInterface
 *
 * @package Mes\Security\CryptoBundle\Loader
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
}
