<?php

namespace KRG\CmsBundle\Twig;

use KRG\CmsBundle\Menu\MenuBuilderInterface;

/**
 * Class BreadcrumbExtension
 * @package KRG\CmsBundle\Twig
 */
class BreadcrumbExtension extends \Twig_Extension
{
    /**
     * @var MenuBuilderInterface
     */
    private $menuBuilder;

    /**
     * @var array
     */
    private $templates;

    /**
     * BreadcrumbExtension constructor.
     * @param MenuBuilderInterface $menuBuilder
     */
    public function __construct(MenuBuilderInterface $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
        $this->templates = [];
    }

    /**
     * Build blocks into a specific template
     *
     * @param \Twig_Environment $environment
     * @param $theme
     *
     * @return mixed
     */
    private function getTemplate(\Twig_Environment $environment, $theme)
    {
        if (isset($this->templates[$theme])) {
            return $this->templates[$theme];
        }
        $this->templates[$theme] = $environment->load($theme);

        return $this->templates[$theme];
    }

    /**
     * @param \Twig_Environment $environment
     * @param string $theme
     *
     * @return mixed
     */
    public function render(\Twig_Environment $environment, $key, $theme = 'KRGCmsBundle:Breadcrumb:bootstrap.html.twig')
    {
        $template = $this->getTemplate($environment, $theme);
        $nodes = $this->menuBuilder->getActiveNodes($key);

        if (!($first = reset($nodes)) || $first['url'] !== '/') {
            array_unshift($nodes, [
                'route' => null,
                'name' => 'Home',
                'url'  => '/',
                'roles' => [],
                'active' => false
            ]);
        }

        return $template->renderBlock('breadcrumb', [
            'id'    => uniqid('krg_breadcrumb_'),
            'nodes' => $nodes
        ]);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'krg_breadcrumb' => new \Twig_SimpleFunction('krg_breadcrumb', array($this, 'render'), [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ])
        ];
    }
}
