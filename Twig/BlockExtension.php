<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
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
        $this->cacheDirKrg = $cacheDir . KRGCmsExtension::KRG_CACHE_DIR;
        $this->cacheFileName = KRGCmsExtension::KRG_BLOCKS_FILE;
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
            $environment->setCache($this->cacheDir);
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
            $staticBlocks = $this->entityManager->getRepository(BlockInterface::class)->findAll();
            $formBlocks = $this->entityManager->getRepository(FilterInterface::class)->findAll();
            $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();

            $content = [];
            /* @var $block BlockInterface */
            foreach ($staticBlocks as $blockStatic) {
                if ($this->legalBlock($blockStatic)) {
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

    protected function legalBlock(BlockInterface $block)
    {
        return false === $this->hasBlockLoop($block);
    }

    /**
     * @param \Twig_Environment $environment
     * @return array|string[]
     */
    public function getBlocks(\Twig_Environment $environment)
    {
        $template = $this->getTemplate($environment);

        return $template ? $template->getBlockNames() : [];
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
