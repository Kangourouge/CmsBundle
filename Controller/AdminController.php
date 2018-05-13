<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/cms", name="krg_cms_admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/media", name="media")
     */
    public function mediaAction(Request $request)
    {
        return $this->render('KRGCmsBundle:Admin:media.html.twig');
    }
}
