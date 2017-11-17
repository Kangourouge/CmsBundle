<?php

namespace KRG\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use KRG\SeoBundle\Entity\MenuInterface;
use KRG\SeoBundle\Menu\MenuBuilderInterface;

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
     * @var array
     */
    private $templates;

    /**
     * @var MenuBuilderInterface
     */
    private $menuBuilder;

    /**
     * MenuExtension constructor.
     * @param EntityManagerInterface $entityManager
     * @param MenuBuilderInterface $menuBuilder
     */
    public function __construct(EntityManagerInterface $entityManager, MenuBuilderInterface $menuBuilder)
    {
        $this->entityManager = $entityManager;
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

    public function render(\Twig_Environment $environment, $brand = null, array $additionalNodes = [], $theme = 'KRGSeoBundle:Menu:bootstrap.html.twig')
    {
        /* @var $repository NestedTreeRepository */
        $repository = $this->entityManager->getRepository(MenuInterface::class);
        $qb = $repository->getRootNodesQueryBuilder()->addOrderBy('node.position');
        $rootNodes = $qb->getQuery()->getResult();
        $nodes = $this->menuBuilder->build($rootNodes);

        $template = $this->getTemplate($environment, $theme);

        return $template->renderBlock('menu', [
            'id'    => uniqid('krg_menu_'),
            'brand' => $brand,
            'nodes' => $nodes
        ]);
    }

    public function getFunctions()
    {
        return [
            'krg_menu' => new \Twig_SimpleFunction('krg_menu', array($this, 'render'), [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }
}
