<?php

namespace KRG\CmsBundle\Twig;

use KRG\CmsBundle\Breadcrumb\BreadcrumbBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BreadcrumbExtension extends \Twig_Extension
{
    /** @var BreadcrumbBuilderInterface */
    protected $breadcrumbBuilder;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var array */
    protected $templates;

    public function __construct(BreadcrumbBuilderInterface $breadcrumbBuilder, TranslatorInterface $translator)
    {
        $this->breadcrumbBuilder = $breadcrumbBuilder;
        $this->translator = $translator;
        $this->templates = [];
    }

    /**
     * Build blocks into a specific template
     */
    private function getTemplate(\Twig_Environment $environment, $theme)
    {
        if (isset($this->templates[$theme])) {
            return $this->templates[$theme];
        }
        $this->templates[$theme] = $environment->load($theme);

        return $this->templates[$theme];
    }

    public function render(\Twig_Environment $environment, string $key = null, $theme = 'KRGCmsBundle:Breadcrumb:bootstrap.html.twig')
    {
        $template = $this->getTemplate($environment, $theme);
        $nodes = $this->breadcrumbBuilder->getNodes($key);

        if (!($first = reset($nodes)) || $first['url'] !== '/') {
            array_unshift($nodes, [
                'route'  => null,
                'name'   => $this->translator->trans('Home'),
                'url'    => '/',
                'roles'  => [],
                'active' => false,
            ]);
        }

        return $template->renderBlock('breadcrumb', [
            'id'    => uniqid('krg_breadcrumb_'),
            'nodes' => $nodes
        ]);
    }

    public function getFunctions()
    {
        return [
            'krg_breadcrumb' => new \Twig_SimpleFunction('krg_breadcrumb', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ])
        ];
    }
}
