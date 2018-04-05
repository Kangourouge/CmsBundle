<?php

namespace KRG\CmsBundle\Twig;

use KRG\CmsBundle\Menu\MenuBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuExtension
 * @package KRG\CmsBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var MenuBuilderInterface
     */
    private $menuBuilder;

    /**
     * @var array
     */
    private $templates;

    /**
     * MenuExtension constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param MenuBuilderInterface $menuBuilder
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, MenuBuilderInterface $menuBuilder)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->menuBuilder = $menuBuilder;
        $this->templates = [];
    }

    /**
     * Build blocks into a specific template
     *
     * @param \Twig_Environment $environment
     * @param                   $theme
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
     * @param                   $key
     * @param string            $theme
     * @param null              $brand
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(\Twig_Environment $environment, $key, $theme = 'KRGCmsBundle:Menu:bootstrap.html.twig', $brand = null)
    {
        $template = $this->getTemplate($environment, $theme);

        return $template->renderBlock('menu', [
            'id'    => uniqid('krg_menu_'),
            'brand' => $brand,
            'nodes' => $this->menuBuilder->getNodes($key)
        ]);
    }

    /**
     * @param array $roles
     *
     * @return bool
     */
    public function isGranted(array $roles)
    {
        return count($roles) === 0 || $this->authorizationChecker->isGranted($roles);
    }

    /**
     * @return array
     */
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
