<?php

namespace Mes\Security\CryptoBundle\Utils;

/**
 * Class SecretGenerator
 *
 * @package Mes\Security\CryptoBundle\Utils
 */
class SecretGenerator
{
	/**
	 * Generates a random secret.
	 *
	 * @return string The randomly generated secret
	 */
	public function generateRandomSecret()
	{
		if (function_exists('openssl_random_pseudo_bytes')) {
			return hash('sha1', openssl_random_pseudo_bytes(23));
		}

		return hash('sha1', uniqid(mt_rand(), true));
	}
}
