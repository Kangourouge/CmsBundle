<?php

namespace KRG\CmsBundle\Command;

use KRG\CmsBundle\Intl\TwigTranslationExtractor;
use Symfony\Bridge\Twig\Translation\TwigExtractor;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Reader\TranslationReader;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Twig\Environment;

class TranslationDumpCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'krg:translation:dump';

    /**
     * @var TwigTranslationExtractor
     */
    protected $extractor;

    /**
     * TranslationUpdateCommand constructor.
     *
     * @param TwigTranslationExtractor $extractor
     */
    public function __construct(TwigTranslationExtractor $extractor, $twigCacheDir)
    {
        parent::__construct();
        $this->extractor = $extractor;
    }

    protected function configure()
    {
        $this->setName(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(implode(PHP_EOL, $this->extractor->extract()));
    }
}