<?php

namespace KRG\CmsBundle\Controller;

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
     * @Route("/admin/cms/page/content.js", name="content_js")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function contentJSAction(Request $request)
    {
        return $this->render('@KRGCms/Page/content.js.twig');
    }
}
