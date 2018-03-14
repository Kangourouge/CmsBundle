<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cms/page")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/show/{key}", name="krg_page_show")
     */
    public function showAction(Request $request, PageInterface $page)
    {
        return $this->render('KRGCmsBundle:Page:show.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/edit/{key}", name="krg_page_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')"))
     */
    public function editAction(Request $request, PageInterface $page)
    {
        return $this->render('KRGCmsBundle:Page:edit.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/snippets.html", name="krg_page_snippets")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function snippetsAction(Request $request)
    {
        return $this->render('@KRGCms/Page/snippets.html.twig');
    }
}
