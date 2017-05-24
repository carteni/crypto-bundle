<?php

use Mes\Security\CryptoBundle\Utils\SecretGenerator;

/**
 * Class SecretGeneratorTest
 */
class SecretGeneratorTest extends \PHPUnit\Framework\TestCase
{
	public function testGenerateRandomSecretGeneratesRandomString()
	{
		$this->assertTrue(ctype_print((new SecretGenerator())->generateRandomSecret()), 'is printable');
	}
}
