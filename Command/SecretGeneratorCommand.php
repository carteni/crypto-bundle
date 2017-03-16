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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SecretGeneratorCommand.
 */
class SecretGeneratorCommand extends Command
{
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
        $output->write(\bin2hex(random_bytes(20)));
    }
}
