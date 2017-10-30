<?php

namespace KRG\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use KRG\SeoBundle\Entity\MenuInterface;

/**
 * Class MenuExtension
 * @package KRG\SeoBundle\Twig
 */
class MenuExtension extends \Twig_Extension
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
    private $cacheFileName;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var \Twig_TemplateWrapper
     */
    private $template;

    /**
     * BlockExtension constructor.
     * @param EntityManagerInterface $entityManager
     * @param $cacheDir
     */
    public function __construct(EntityManagerInterface $entityManager, $cacheDir, $theme = null)
    {
        $this->entityManager = $entityManager;
        $this->cacheDir = $cacheDir;
        $this->theme = $theme;
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
        $this->template = $environment->load($this->theme); // Load template from cache
        return $this->template;
    }

    /**
     * Generate $this->cacheFileName twig template composed of each blocks
     */
    public function createFileTemplate()
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $path = sprintf('%s/seo_menu.html.twig', $this->cacheDir);
        if (!file_exists($path)) {
            /* @var $repository NestedTreeRepository */
            $repository = $this->entityManager->getRepository(MenuInterface::class);

            $nodes = $repository->getNodesHierarchy();

            $content = $this->build($nodes);

            return (bool)file_put_contents($path, implode('', $content));
        }

        return false;
    }

    private function build(array $nodes) {
        if (count($nodes) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($nodes);

        return array_merge([
            'name' => $menu->getName(),
            'title' => $menu->getTitle(),
            'url' => null,
            'children' => $this->build($menu->getChildren()->toArray())
        ], $this->build($nodes));
    }

    public function getMenu(\Twig_Environment $environment, $key)
    {
        $template = $this->getTemplate($environment);

        return $template->displayBlock('menu');
    }

    public function getFunctions()
    {
        return [
            'krg_menu' => new \Twig_SimpleFunction('krg_menu', array($this, 'getMenu'), [
                'needs_environment' => true, // Tell twig we need the environment
                'is_safe' => ['html'],
            ]),
        ];
    }
}
