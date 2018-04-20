<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
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
     * Build blocks into a specific template
     */
    public function load(\Twig_Environment $environment)
    {
        try {

            $this->createFileTemplate('cms.html.twig', function(){ return implode('', $this->loadBlocks()); });

            $environment->setCache($this->twigCacheDir);

            $environment->setLoader(new \Twig_Loader_Chain([
                $environment->getLoader(), // Preserve old loader
                new \Twig_Loader_Filesystem([$this->twigCacheDir]) // Add KRG cache dir
            ]));

            $this->template = $environment->load('cms.html.twig'); // Load template from cache

            return $this->template;
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('[KRGCmsBundle] %s', $exception->getMessage()));
        }

        return null;
    }


    /**
     * Build blocks into a specific template
     */
    private function getTemplate(\Twig_Environment $environment)
    {
        if ($this->template) {
            return $this->template;
        }

        return $this->load($environment);
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

    private function loadBlocks()
    {
        if (empty($this->content)) {
            $this->content = array_merge(
                ['{% trans_default_domain "cms" %}'],
                $this->loadStaticBlocks(),
                $this->loadFilterBlocks(),
                $this->loadFileBlocks(),
                $this->loadPages()
            );
        }

        return $this->content;
    }

    protected function loadFileBlocks()
    {
        $content = [];
        foreach ($this->fileBlocks as $key => $block) {
            try {
                $path = $this->locator->locate($this->nameParser->parse($block['template']));
            } catch (\InvalidArgumentException $exception) {
                continue;
            }

            if ($fileContent = @file_get_contents($path)) {
                $content[$key] = sprintf("{%% block %s %%}%s{%% endblock %s %%}\n", $key, $fileContent, $key);
            }
        }

        return $content;
    }

    protected function loadPages()
    {
        $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();
        $content = [];

        /* @var $page PageInterface */
        foreach ($pages as $page) {
            $content[$page->getKey()] = sprintf("{%% block %s %%}<div class=\"cms-block cms-page\">
            {%% set krg_key = \"%s\" %%}
            %s</div>
            {%% endblock %s %%}\n", $page->getKey(), $page->getKey(), $page->getContent(), $page->getKey());
        }

        return $content;
    }

    protected function loadFilterBlocks()
    {
        $blocks = $this->entityManager->getRepository(FilterInterface::class)->findAll();
        $content = [];

        /* @var $filter FilterInterface */
        foreach ($blocks as $block) {
            $content[$block->getKey()] = sprintf("{%% block %s %%}<div class=\"cms-block cms-filter\">{{ render(controller('KRGCmsBundle:Filter:show', {'filter': %d})) }}</div>{%% endblock %s %%}\n", $block->getKey(), $block->getId(), $block->getKey());
        }

        return $content;
    }

    protected function loadStaticBlocks()
    {
        $blocks = $this->entityManager->getRepository(BlockInterface::class)->findAll();
        $content = [];

        /* @var $block BlockInterface */
        foreach ($blocks as $block) {
            if ($this->isValidBlock($block)) {
                $content[$block->getKey()] = sprintf("{%% block %s %%}%s{%% endblock %s %%}\n", $block->getKey(), $block->getContent(), $block->getKey());
            }
        }

        return $content;
    }

    /**
     * Render a block by it's key
     */
    public function render(\Twig_Environment $environment, $key, $context = array())
    {
        try {
            $template = $this->getTemplate($environment);

            if ($template && $template->hasBlock($key)) {
                return $template->renderBlock($key, $context);
            }

            throw new \Exception('KRG block template is null');
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('[KRGCmsBundle] Render exception, %s', $exception->getMessage()));
        }

        return null;
    }

    /**
     * Render a block content
     */
    public function renderContent(\Twig_Environment $environment, $content)
    {
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

    public function getSnippets(\Twig_Environment $environment)
    {
        if (null === ($template = $this->getTemplate($environment))) {
            return [];
        }

        $blocks = [];
        foreach ($template->getBlockNames() as $name) {
            if (false === strstr($name, 'krg_page_')) {
                $block = $this->entityManager->getRepository(BlockInterface::class)->findOneBy(['key' => $name]);
                $thumbnail = ($block) ? $block->getThumbnail() : null;
                $blocks[] = [
                    'html'      => $this->render($environment, $name),
                    'thumbnail' => $this->fileBlocks[$name]['thumbnail'] ?? $thumbnail,
                    'label'     => $this->fileBlocks[$name]['label'] ?? $name
                ];
            }
        }

        return $blocks;
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
