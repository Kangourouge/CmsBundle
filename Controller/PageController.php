<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\Entity\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/seo/page")
 */
class PageController extends Controller
{
    /**
     * @Route("/show/{id}", name="krg_seo_page_show")
     * @Template
     */
    public function showAction(Request $request, $id)
    {
        /* @var $seoPage SeoPageInterface */
        $seoPage = $this->getDoctrine()->getRepository('AppBundle:SeoPage')->find($id);

        if (!$seoPage) {
            throw $this->createNotFoundException();
        }

        $controller = null;
        if ($formType = $seoPage->getFormType()) {
            $form = $this->createForm($formType);
            $formName = $form->getConfig()->getName();

            // If there is no request
            if (!$request->get($formName)) {
                $csrf = $this->get('security.csrf.token_manager');
                $request->setMethod('POST');
                $request->request->set($formName, array_merge(
                    $seoPage->getFormData(),
                    array('_token' => $csrf->refreshToken($formName))
                ));
                $request->request->set('_seo_page', true);
            }

            $routes = $this->get('router')->getRouteCollection();
            $controller = $routes->get($seoPage->getFormRoute())->getDefaults()['_controller'];
        }

        return array(
            'seoPage'    => $seoPage,
            'controller' => $controller,
        );
    }
}
