<?php

namespace KRG\CmsBundle\Twig;

use KRG\CmsBundle\Menu\MenuBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuExtension extends \Twig_Extension
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var MenuBuilderInterface */
    private $menuBuilder;

    /** @var array */
    private $templates;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, MenuBuilderInterface $menuBuilder)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->menuBuilder = $menuBuilder;
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

    public function render(\Twig_Environment $environment, $key, $theme = 'KRGCmsBundle:Menu:bootstrap.html.twig', $brand = null)
    {
        $template = $this->getTemplate($environment, $theme);

        return $template->renderBlock('menu', [
            'id'    => uniqid('krg_menu_'),
            'brand' => $brand,
            'nodes' => $this->menuBuilder->getNodes($key)
        ]);
    }

    public function isGranted(array $roles)
    {
        return count($roles) === 0 || $this->authorizationChecker->isGranted($roles);
    }

    public function getFunctions()
    {
        return [
            'krg_menu' => new \Twig_SimpleFunction('krg_menu', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'is_granted_roles' => new \Twig_SimpleFunction('is_granted_roles', [$this, 'isGranted'], [
                'needs_environment' => false
            ])
        ];
    }
}
