<?php

namespace KRG\CmsBundle\Intl;

use KRG\CmsBundle\Twig\BlockExtension;
use Symfony\Bridge\Twig\Translation\TwigExtractor;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Reader\TranslationReader;

class TwigTranslationExtractor
{
    /**
     * @var BlockExtension
     */
    protected $blockExtension;

    /**
     * @var ExtractorInterface
     */
    protected $extractor;

    /**
     * @var string
     */
    protected $twigCacheDir;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * TwigTranslationExtractor constructor.
     *
     * @param TwigExtractor $extractor
     * @param string $twigCacheDir
     * @param string $defaultLocale
     */
    public function __construct(BlockExtension $blockExtension, TwigExtractor $extractor, string $twigCacheDir, string $defaultLocale)
    {
        $this->blockExtension = $blockExtension;
        $this->extractor = $extractor;
        $this->twigCacheDir = $twigCacheDir;
        $this->defaultLocale = $defaultLocale;
    }

    public function extract() {
        $reader = new TranslationReader();

        // load any existing messages from the translation files
        $extractedCatalogue = new MessageCatalogue($this->defaultLocale);
        $viewsPaths = [$this->twigCacheDir];

        $environment = new \Twig_Environment(
            new \Twig_Loader_Filesystem($viewsPaths), [
                'cache' => $this->twigCacheDir,
                'auto_reload' => true,
            ]
        );
        $environment->addExtension(new \Twig_Extensions_Extension_I18n());

        $this->blockExtension->load($environment);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->twigCacheDir),\RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $file) {
            // force compilation
            if ($file->isFile() && substr($file->getFilename(), -4) === '.twig') {
                $environment->loadTemplate($file->getFilename());
            }
        }

        $extractor = new TwigExtractor($environment);

        foreach ($viewsPaths as $path) {
            if (is_dir($path)) {
                $this->extractor->extract($path, $extractedCatalogue);
            }
        }

        $tanslations = $extractedCatalogue->all();



        return $tanslations['cms'] ?? [];
    }
}