<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoPageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
}
