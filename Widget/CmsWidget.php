<?php

namespace KRG\CmsBundle\Widget;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\BlockInterface;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\EasyAdminExtensionBundle\Widget\WidgetInterface;
use Symfony\Component\Templating\EngineInterface;

class CmsWidget implements WidgetInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EngineInterface */
    protected $templating;

    public function __construct(EntityManagerInterface $entityManager, EngineInterface $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function render()
    {
        $pages = $this->entityManager->getRepository(PageInterface::class)->findBy(['enabled' => true]);
        $menus = $this->entityManager->getRepository(MenuInterface::class)->findBy(['enabled' => true]);
        $blocks = $this->entityManager->getRepository(BlockInterface::class)->findBy(['enabled' => true]);

        return $this->templating->render('@KRGCms/widget/cms.html.twig', [
            'title' => 'CMS',
            'data'  => [
                [
                    'title' => 'PAGES',
                    'icon'  => 'file',
                    'value' => count($pages)
                ],
                [
                    'title' => 'MENUS',
                    'icon'  => 'reorder',
                    'value' => count($menus)
                ],
                [
                    'title' => 'BLOCKS',
                    'icon'  => 'cubes',
                    'value' => count($blocks)
                ],
            ],
        ]);
    }
}
