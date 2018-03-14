<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\PageInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;

/**
 * Class BlockExtension
 * @package KRG\CmsBundle\Twig
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $fileBlocks;

    /**
     * @var array
     */
    private $content;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $cacheDirKrg;

    /**
     * @var string
     */
    private $cacheFileName;

    /**
     * @var \Twig_TemplateWrapper
     */
    private $template;

    /**
     * @var TemplateNameParser
     */
    private $nameParser;

    /**
     * @var TemplateLocator
     */
    private $locator;

    public function __construct(EntityManagerInterface $entityManager, TemplateNameParser $nameParser, TemplateLocator $locator, $fileBlocks, $cacheDir)
    {
        $this->entityManager = $entityManager;
        $this->cacheDir = $cacheDir;
        $this->cacheDirKrg = $cacheDir . KRGCmsExtension::KRG_CACHE_DIR;
        $this->cacheFileName = KRGCmsExtension::KRG_BLOCKS_FILE;
        $this->fileBlocks = $fileBlocks;
        $this->nameParser = $nameParser;
        $this->locator = $locator;
        $this->content = [];
    }

    /**
     * Build blocks into a specific template
     *
     * @param \Twig_Environment $environment
     * @return \Twig_TemplateWrapper
     */
    private function getTemplate(\Twig_Environment $environment)
    {
        if ($this->template) {
            return $this->template;
        }

        try {
            $this->createFileTemplate();
            $environment->setCache($this->cacheDirKrg);
            $environment->setLoader(new \Twig_Loader_Chain([
                $environment->getLoader(), // Preserve old loader
                new \Twig_Loader_Filesystem([$this->cacheDirKrg]) // Add KRG cache dir
            ]));

            $this->template = $environment->load($this->cacheFileName); // Load template from cache

            return $this->template;
        } catch (\Exception $exception) {
            /* log and send error */
        }

        return null;
    }

    /**
     * Generate $this->cacheFileName twig template composed of each blocks
     */
    public function createFileTemplate()
    {
        if (!is_dir($this->cacheDirKrg)) {
            mkdir($this->cacheDirKrg);
        }

        $path = sprintf('%s/%s', $this->cacheDirKrg, $this->cacheFileName);
        if (!file_exists($path)) {
            return (bool) file_put_contents($path, implode('', $this->loadBlocks()));
        }

        return false;
    }

    private function loadBlocks()
    {
        if (empty($this->content)) {
            $this->content = array_merge(
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
            $path = $this->locator->locate($this->nameParser->parse($block['template']));
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
     *
     * @param \Twig_Environment $environment
     * @param                   $key
     * @param array             $context
     * @return null|string
     * @throws \Throwable
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
        }

        return null;
    }

    /**
     * Simple block loop detect, can be improved
     *
     * @param BlockInterface $block
     * @return int
     */
    protected function isSafe(BlockInterface $block)
    {
        return false === (bool)strpos($block->getContent(), sprintf("block('%s')", $block->getKey()));
    }

    /**
     * @param BlockInterface $block
     * @return bool
     */
    protected function isValidBlock(BlockInterface $block)
    {
        return $block->isEnabled() && $block->isWorking() && $this->isSafe($block);
    }

    /**
     * @param \Twig_Environment $environment
     * @return array
     * @throws \Throwable
     */
    public function getSnippets(\Twig_Environment $environment)
    {
        if (null === ($template = $this->getTemplate($environment))) {
            return [];
        }

        $blocks = [];
        foreach ($template->getBlockNames() as $name) {
            if (false === strstr($name, 'krg_page_')) {
                $blocks[] = [
                    'html'      => $this->render($environment, $name),
                    'thumbnail' => $this->fileBlocks[$name]['thumbnail'] ?? null,
                    'label'     => $this->fileBlocks[$name]['label'] ?? $name
                ];
            }
        }

        return $blocks;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            'krg_block' => new \Twig_SimpleFunction('krg_block', array($this, 'render'), [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'krg_snippet_list' => new \Twig_SimpleFunction('krg_snippet_list', array($this, 'getSnippets'), [
                'needs_environment' => true
            ]),
        ];
    }
}
