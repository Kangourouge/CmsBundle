<?php

namespace KRG\SeoBundle\Twig;

use KRG\SeoBundle\Menu\MenuBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuExtension
 * @package KRG\SeoBundle\Twig
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
     * @param $key
     * @param string $theme
     * @param null $brand
     *
     * @return mixed
     */
    public function render(\Twig_Environment $environment, $key, $theme = 'KRGSeoBundle:Menu:bootstrap.html.twig', $brand = null)
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
    public function isGranted(array $roles) {
        return count($roles) === 0 || $this->authorizationChecker->isGranted($roles);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'krg_menu' => new \Twig_SimpleFunction('krg_menu', array($this, 'render'), [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'is_granted_roles' => new \Twig_SimpleFunction('is_granted_roles', array($this, 'isGranted'), [
                'needs_environment' => false
            ])
        ];
    }
}
