<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Tests\Command;

use Mes\Security\CryptoBundle\Command\SecretGeneratorCommand;
use Mes\Security\CryptoBundle\Utils\SecretGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class SecretGeneratorCommandTest.
 */
class SecretGeneratorCommandTest extends \PHPUnit_Framework_TestCase
{
	public function testExecuteGeneratesSecret40Chars()
	{
		$application = new Application();
		$application->add(new SecretGeneratorCommand(new SecretGenerator()));

		$command = $application->get('mes:crypto:generate-secret');
		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'command' => $command->getName(),
		));

		$secret = $commandTester->getDisplay();

		$this->assertTrue(ctype_print($secret), 'is printable');
		$this->assertSame(40, strlen($secret));
	}
}
