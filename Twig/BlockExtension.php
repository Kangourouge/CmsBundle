<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use EMC\FileinputBundle\Entity\File;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use KRG\CmsBundle\Entity\Block;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\PageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Component\Templating\EngineInterface;

class BlockExtension extends \Twig_Extension
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EngineInterface */
    protected $templating;

    /** @var TemplateNameParser */
    private $nameParser;

    /** @var TemplateLocator */
    private $locator;

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $fileBlocks;

    /** @var string */
    private $twigCacheDir;

    /** @var array */
    private $content;

    /** @var \Twig_TemplateWrapper */
    private $template;

    public function __construct(EntityManagerInterface $entityManager, EngineInterface $templating, TemplateNameParser $nameParser, TemplateLocator $locator, LoggerInterface $logger, $fileBlocks, $twigCacheDir)

    {
        $this->entityManager = $entityManager;
        $this->nameParser = $nameParser;
        $this->locator = $locator;
        $this->logger = $logger;
        $this->fileBlocks = $fileBlocks;
        $this->twigCacheDir = $twigCacheDir;
        $this->content = [];
        $this->templating = $templating;
    }

    /**
     * Generate twig template
     */
    public function createFileTemplate($filename, \Closure $callback)
    {
        if (!is_dir($this->twigCacheDir)) {
            mkdir($this->twigCacheDir);
        }

        $path = sprintf('%s/%s', $this->twigCacheDir, $filename);

        if (!file_exists($path)) {
            return (bool) file_put_contents($path, call_user_func($callback)) ? $path : null;
        }

        return $path;
    }

    /**
     * Render a block by it's key
     */
    public function render(\Twig_Environment $environment, $key, $context = array())
    {
        try {
            /** @var BlockInterface $block */
            $block = $this->entityManager->getRepository(BlockInterface::class)->findOneBy(['key' => $key]);
            if ($block !== null) {
                return $this->renderBlock($environment, $block);
            }

            foreach ($this->fileBlocks as $_key => $config) {
                if ($_key !== $key) {
                    continue;
                }

                $path = $this->locator->locate($this->nameParser->parse($config['template']));
                return $this->renderContent($environment, @file_get_contents($path));
            }

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('[KRGCmsBundle] Render exception, %s', $exception->getMessage()));
        }

        return null;
    }

    /**
     * Render a block by it's key
     */
    public function renderBlock(\Twig_Environment $environment, BlockInterface $block)
    {
        if (!$block->isEnabled()) {
            return null;
        }
        return $this->renderContent($environment, $block->getContent());
    }

    /**
     * Render a block content
     */
    public function renderContent(\Twig_Environment $environment, $content)
    {
        if (!is_string($content) || strlen($content) === 0) {
            return null;
        }

        try {
            $filename = sprintf('content_%s.html.twig', sha1($content));
            $pathname = $this->createFileTemplate($filename, function() use ($content) { return $content; });
            if ($pathname !== null) {
                return $this->templating->render('KRGCmsBundle:Block:show.html.twig', ['filename' => $pathname]);
            }
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('[KRGCmsBundle] Render exception, %s', $exception->getMessage()));
        }

        return null;
    }

    /**
     * Simple block loop detect, can be improved
     */
    protected function isSafe(BlockInterface $block)
    {
        return false === (bool)strpos($block->getContent(), sprintf("block('%s')", $block->getKey()));
    }

    protected function isValidBlock(BlockInterface $block)
    {
        return $block->isEnabled() && $block->isWorking() && $this->isSafe($block);
    }

    private function getFragments() {
        $fragments = [];

        foreach ($this->fileBlocks as $key => $config) {

            try {
                $path = $this->locator->locate($this->nameParser->parse($config['template']));
            } catch (\InvalidArgumentException $exception) {
                continue;
            }

            if ($content = @file_get_contents($path)) {
                $block = new Block();
                $block->setKey($key);
//                $block->setThumbnail($config['thumbnail'] ?? null);
                $block->setEnabled(true);
                $block->setName($config['name'] ?? $key);
                $block->setContent($content);
                $fragments[$key] = $block;
            }

        }

        $blocks = $this->entityManager->getRepository(BlockInterface::class)->findAll();
        foreach($blocks as $block) {
            $fragments[$block->getKey()] = $block;
        }

        $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();
        foreach($pages as $page) {
            $fragments[$page->getKey()] = $page;
        }

        return $fragments;
    }

    public function getSnippets(\Twig_Environment $environment)
    {
        $fragments = $this->getFragments();

        $snippets = [];
        foreach ($fragments as $block) {
            $name = $block->getName();
            $thumbnail = $block->getThumbnail() ?: null;
            $snippets[] = [
                'html'      => $this->renderBlock($environment, $block),
                'thumbnail' => $this->fileBlocks[$name]['thumbnail'] ?? $thumbnail,
                'label'     => $this->fileBlocks[$name]['label'] ?? $name
            ];
        }

        return $snippets;
    }

    public function getFunctions()
    {
        return [
            'krg_block' => new \Twig_SimpleFunction('krg_block', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'krg_block_content' => new \Twig_SimpleFunction('krg_block_content', [$this, 'renderContent'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'krg_snippet_list' => new \Twig_SimpleFunction('krg_snippet_list', [$this, 'getSnippets'], [
                'needs_environment' => true
            ]),
        ];
    }
}
