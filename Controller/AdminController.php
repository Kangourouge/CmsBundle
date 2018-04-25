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
     * @Route("/filemanager", name="file_manager")
     */
    public function fileManagerAction(Request $request)
    {
        return $this->render('KRGCmsBundle:Admin:file_manager.html.twig');
    }
}
