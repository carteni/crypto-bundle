<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mes\Security\CryptoBundle\Utils\SecretGenerator;

/**
 * Class SecretGeneratorTest.
 */
class SecretGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testGenerateRandomSecretGeneratesRandomString()
	{
		$this->assertTrue(ctype_print((new SecretGenerator())->generateRandomSecret()), 'is printable');
	}
}
