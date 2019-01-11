<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Annotation\Menu;
use KRG\CmsBundle\Entity\PageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="krg_page_")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/cms/page/show/{key}", name="show")
     * @Menu("{page.name}", route="krg_page_show", params={"slug" = "{page.key}"})
     */
    public function showAction(Request $request, PageInterface $page)
    {
        return $this->render('KRGCmsBundle:Page:show.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/admin/cms/page/edit/{key}", name="edit")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function editAction(Request $request, PageInterface $page)
    {
        return $this->render('KRGCmsBundle:Page:edit.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/admin/cms/page/snippets.html", name="snippets")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function snippetsAction(Request $request)
    {
        return $this->render('@KRGCms/Page/snippets.html.twig');
    }
}
