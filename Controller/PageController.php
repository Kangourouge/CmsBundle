<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\Entity\PageInterface;
use KRG\SeoBundle\Entity\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/seo/page")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/show/{key}", name="krg_page_show")
     * @Template
     */
    public function showAction(Request $request, PageInterface $page)
    {
        return ['page' => $page];
    }
}
