<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\PageInterface;
use Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode;
use Twig\TwigFunction;

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
     * BlockExtension constructor.
     * @param EntityManagerInterface $entityManager
     * @param $cacheDir
     */
    public function __construct(EntityManagerInterface $entityManager, $cacheDir)
    {
        $this->entityManager = $entityManager;
        $this->cacheDir = $cacheDir;
        $this->cacheDirKrg = $cacheDir . '/krg';
        $this->cacheFileName = 'blocks.html.twig';
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

        $this->createFileTemplate();
        $environment->setCache($this->cacheDir);
        $environment->setLoader(new \Twig_Loader_Chain([
            $environment->getLoader(), // Preserve old loader
            new \Twig_Loader_Filesystem([$this->cacheDirKrg]) // Add KRG cache dir
        ]));

        $this->template = $environment->load($this->cacheFileName); // Load template from cache

        return $this->template;
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
            $staticBlocks = $this->entityManager->getRepository(BlockInterface::class)->findAll();
            $formBlocks = $this->entityManager->getRepository(FilterInterface::class)->findAll();
            $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();

            $content = [];
            /* @var $block BlockInterface */
            foreach ($staticBlocks as $blockStatic) {
                if (false === $this->hasBlockLoop($blockStatic)) {
                    $content[] = sprintf("{%% block %s %%}<div class=\"cms-block\">%s</div>{%% endblock %s %%}\n", $blockStatic->getKey(), $blockStatic->getContent(), $blockStatic->getKey());
                }
            }

            /* @var $filter FilterInterface */
            foreach ($formBlocks as $filter) {
                $content[] = sprintf("{%% block %s %%}<div class=\"cms-block cms-filter\">{{ render(controller('KRGCmsBundle:Block:form', {'filter': %d})) }}</div>{%% endblock %s %%}\n", $filter->getKey(), $filter->getId(), $filter->getKey());
            }

            /* @var $page PageInterface */
            foreach ($pages as $page) {
                $content[] = sprintf("{%% block %s %%}<div class=\"cms-block cms-page\">%s</div>{%% endblock %s %%}\n", $page->getKey(), $page->getContent(), $page->getKey());
            }

            return (bool) file_put_contents($path, implode('', $content));
        }

        return false;
    }

    /**
     * Render a block by it's key
     *
     * @param \Twig_Environment $environment
     * @param $key
     * @param array $context
     *
     * @return null|string
     */
    public function render(\Twig_Environment $environment, $key, $context = array())
    {
        $template = $this->getTemplate($environment);

        if ($template->hasBlock($key)) {
            return $template->renderBlock($key, $context);
        }

        return null;
    }

    /**
     * TODO: deport on block validator constraint
     * Simple block loop detect, can be improved
     *
     * @param BlockInterface $block
     * @return int
     */
    protected function hasBlockLoop(BlockInterface $block)
    {
        return (bool)strpos($block->getContent(), sprintf("block('%s')", $block->getKey()));
    }

    /**
     * @return \string[]
     */
    public function getBlocks(\Twig_Environment $environment){
        return $this->getTemplate($environment)->getBlockNames();
    }

    public function getFunctions()
    {
        return [
            'krg_block' => new \Twig_SimpleFunction('krg_block', array($this, 'render'), [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'krg_block_list' => new \Twig_SimpleFunction('krg_block_list', array($this, 'getBlocks'), [
                'needs_environment' => true
            ]),
        ];
    }
}
