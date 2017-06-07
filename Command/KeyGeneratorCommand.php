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

use Mes\Security\CryptoBundle\KeyGenerator\KeyGenerator;
use Mes\Security\CryptoBundle\Utils\SecretGenerator;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class KeyGeneratorCommand.
 */
class KeyGeneratorCommand extends AbstractCommand
{
    private $secret;
    private $wantsToSaveSecret;

    /**
     * @var QuestionHelper
     */
    private $helper;

    /**
     * @var SecretGenerator
     */
    private $generator;

    /**
     * KeyGeneratorCommand constructor.
     *
     * @param SecretGenerator $generator
     */
    public function __construct(SecretGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->helper = $this->getHelperSet()
                             ->get('question');

        $this->symfonyStyle = $this->getStyle($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mes:crypto:generate-key')
             ->setDefinition($this->createDefinition())
             ->setDescription('Generates an encoded key with or without authentication secret')
             ->setHelp(<<<'EOF'
The <info>%command.name%</info> generates an encoded key with or without authentication secret and optionally it saves the printable key and the secret in a ini format .crypto file.

<info>%command.full_name%</info>

or

<info>%command.full_name%</info> --dir /path/to/dir/key.crypto
EOF
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getOption('dir');

        $this->writeSection($output, 'Welcome to the Key Generator');

        $question = new ConfirmationQuestion($this->getQuestion('Do you want to generate a key with an authentication secret?', 'yes'), true);
        $wantsToGenerateSecret = $this->helper->ask($input, $output, $question);

        if ($wantsToGenerateSecret) {
            $random = $this->generator->generateRandomSecret();
            $this->symfonyStyle->newLine();
            $question = new Question($this->getQuestion('Insert your authentication secret or use this one randomly generated', $random), $random);
            $question->setValidator(function ($secret) {
                if (!ctype_print($secret)) {
                    throw new \RuntimeException(sprintf('The authentication secret is not printable', $secret));
                }

                return $secret;
            });
            $this->secret = $this->helper->ask($input, $output, $question);

            if (null !== $dir) {
                $this->symfonyStyle->newLine();
                $question = new ConfirmationQuestion($this->getQuestion(sprintf('Do you want to save this authentication secret in <comment>%s</comment> as well?', $dir), 'yes'), true);
                $this->wantsToSaveSecret = $this->helper->ask($input, $output, $question);
            }
        }

        // Summary
        $output->writeln(array(
            '',
            $this->createBlock('Summary before key generation', 'bg=blue;fg-white'),
            '',
            $this->secret ? sprintf('You are going to generate a key with <info>%s</info> authentication secret %s', $this->secret, $dir ? "in <info>$dir</info>" : '') : sprintf('You are going to generate a key without authentication secret %s', $dir ? "in <info>$dir</info>" : ''),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getLogger($output);
        $dir = $input->getOption('dir');
        $secret = $this->secret;

        // Conditions
        $secretIsExternalCondition = (true === $this->wantsToSaveSecret);

        if ($input->isInteractive()) {
            $this->symfonyStyle->newLine();
            $question = new ConfirmationQuestion($this->getQuestion('Do you confirm key generation?', 'yes'), true);
            if (!($this->helper->ask($input, $output, $question))) {
                $this->symfonyStyle->newLine();
                $this->symfonyStyle->error('Command aborted');

                return 1;
            }
        }

        if ($dir) {
            $this->log($logger, "The encoded key will be saved in {$dir}\n", LogLevel::INFO);
        }

        if ($secret) {
            $this->log($logger, "The encoded key will be generated with secret: {$secret}\n", LogLevel::INFO);
        }

        $keyGenerator = new KeyGenerator();

        $this->writeSection($output, 'Generating key'.($secret ? ' with authentication secret' : '').($dir ? " in $dir" : ''));

        $encodedKey = $keyGenerator->generate($secret)
                                   ->getEncoded();

        $this->log($logger, "The encoded key has been generated with the following sequence:\n{$encodedKey}\n", LogLevel::INFO);

        if (null !== $dir) {
            $secretLine = null;
            if ($secretIsExternalCondition) {
                $secretLine = <<<EOT
secret = $secret
EOT;
            }

            $f = new Filesystem();
            $filename = basename($dir);
            $f->dumpFile($dir, <<<EOT
; $filename
[crypto]
key = $encodedKey
$secretLine
EOT
            );

            $this->log($logger, "The encoded key saved in {$dir}\n", LogLevel::INFO);
        }

        $this->writeResults($output, array(
            'key' => $encodedKey,
            'dir' => $dir,
            'secret' => $secret,
        ));

        $this->log($logger, 'The key generation process has been completed.', LogLevel::INFO);

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function writeResults(OutputInterface $output, $options)
    {
        $this->writeSection($output, 'Summary after key generation');

        $output->writeln(array(
            '<info>Key</info>',
            str_repeat('=', 3),
            $options['key'],
            '',
            '<info>Directory</info>',
            str_repeat('=', 9),
            $options['dir'] ?: "No directory defined\n",
            '<info>Secret</info>',
            str_repeat('=', 6),
            $options['secret'] ?: '',
        ));

        if (!empty($options['dir']) && $output->isDebug()) {

            /** @var DebugFormatterHelper */
            $dh = $this->getHelperSet()
                       ->get('debug_formatter');

            $process = new Process("ls -la {$options['dir']} | grep \".crypto\"");

            $output->writeln(array(
                '',
                $dh->start(spl_object_hash($process), "Find {$options['dir']}", 'START'),
            ));

            $process->run(function ($type, $buffer) use ($process, $output, $dh) {
                $output->writeln($dh->progress(spl_object_hash($process), $buffer, Process::ERR === $type));
            });

            $output->writeln(array(
                $dh->stop(spl_object_hash($process), 'Everything is OK!', $process->isSuccessful()),
            ));
        }
    }

    /**
     * @return InputDefinition
     */
    protected function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption('dir', 'd', InputOption::VALUE_REQUIRED, 'The path to the file which stores the encoded key'),
        ));
    }
}
