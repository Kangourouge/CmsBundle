<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\Entity\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seo/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/seopage/edit/{id}", name="krg_seo_admin_edit_seo_page");
     */
    public function editSeoPageParametersAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /* @var $seoPage SeoPageInterface */
        $seoPage = $entityManager->getRepository('AppBundle:SeoPage')->find($id);
        if (!$seoPage) {
            throw $this->createNotFoundException();
        }

        if ($parameters = $request->get('parameters')) {
            $seoPage->setFormData($parameters);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_app_seo_page_edit', array(
            'id' => $seoPage->getId())
        );
    }
}
