<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\Entity\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/seo/page")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="krg_seo_page_show")
     * @Template
     */
    public function showAction(Request $request, $id)
    {
        /* @var $seoPage SeoPageInterface */
        $seoPage = $this->getDoctrine()->getRepository(SeoPageInterface::class)->find($id);

        if (!$seoPage) {
            throw $this->createNotFoundException();
        }

        $controller = null;
        if ($formType = $seoPage->getFormType()) {
            $form = $this->createForm($formType);
            $formName = $form->getConfig()->getName();

            // If there is no request, inject form data into request
            if (!$request->get($formName)) {
                $csrf = $this->container->get(CsrfTokenManagerInterface::class);
                $request->setMethod('POST');
                $request->request->set($formName, array_merge(
                    $seoPage->getFormData(),
                    ['_token' => $csrf->refreshToken($formName)]
                ));
                $request->request->set('_seo_page', true);
            }

            $routes = $this->container->get(RouterInterface::class)->getRouteCollection();
            $controller = $routes->get($seoPage->getFormRoute())->getDefaults()['_controller'];
        }

        return [
            'seoPage'    => $seoPage,
            'controller' => $controller,
        ];
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
        ]);
    }
}
