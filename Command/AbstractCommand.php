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

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AbstractCommand.
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var StyleInterface
     */
    protected $symfonyStyle;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array|null                                        $verbosityMap
     *
     * @return LoggerInterface
     */
    protected function getLogger(OutputInterface $output, array $verbosityMap = null)
    {
        if (null === $verbosityMap) {
            $verbosityMap = array(
                LogLevel::INFO => OutputInterface::VERBOSITY_DEBUG,
            );
        }

        return new ConsoleLogger($output, $verbosityMap);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return StyleInterface
     */
    protected function getStyle(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->symfonyStyle) {
            $this->symfonyStyle = new SymfonyStyle($input, $output);
        }

        return $this->symfonyStyle;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param $message
     * @param $level
     */
    protected function log(LoggerInterface $logger, $message, $level)
    {
        $logger->log($level, $message);
    }

    /**
     * @param $question
     * @param $default
     * @param string $sep
     *
     * @return string
     */
    protected function getQuestion($question, $default, $sep = ':')
    {
        $question = $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
        $question .= "\n > ";

        return $question;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $text
     * @param string $style
     */
    protected function writeSection(OutputInterface $output, $text, $style = 'bg=blue;fg=white')
    {
        /**
         * @var FormatterHelper
         */
        $formatter = $this->getHelperSet()
                          ->get('formatter');

        $output->writeln(array(
            '',
            $formatter->formatBlock($text, $style, true),
            '',
        ));
    }

    /**
     * @param $text
     * @param $style
     *
     * @return string
     */
    protected function createBlock($text, $style)
    {
        return $this->getHelper('formatter')
                    ->formatBlock($text, $style, true);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param $options
     *
     * @return string
     */
    abstract protected function writeResults(OutputInterface $output, $options);
}
