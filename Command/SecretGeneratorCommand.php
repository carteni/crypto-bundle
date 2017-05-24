<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\Command;

use Mes\Security\CryptoBundle\Utils\SecretGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SecretGeneratorCommand.
 */
class SecretGeneratorCommand extends AbstractCommand
{
	/**
	 * @var SecretGenerator
	 */
	private $generator;

	public function __construct(SecretGenerator $generator)
	{
		$this->generator = $generator;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this->setName('mes:crypto:generate-secret')
			 ->setDescription('Generates an authentication secret')
			 ->setHelp(<<<'EOF'
The <info>%command.name%</info> generates an authentication secret.

<info>%command.full_name%</info>
EOF
			 );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//$output->write($this->generator->generateRandomSecret());

		$this->writeResults($output, array(
			'secret' => $this->generator->generateRandomSecret(),
		));
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param $options
	 *
	 * @return string
	 */
	protected function writeResults(OutputInterface $output, $options)
	{
		$output->writeln(array(
				"\n<info>Secret</info>",
				str_repeat('=', 6),
				$options['secret'],
		));
	}
}
