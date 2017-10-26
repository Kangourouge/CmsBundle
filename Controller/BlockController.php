<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\Entity\BlockFormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seo/block")
 */
class BlockController extends AbstractController
{
    public function formAction(Request $request, BlockFormInterface $blockForm)
    {
        $controller = null;
        if ($formType = $blockForm->getType()) {
            $form = $this->createForm($formType);
            $formName = $form->getConfig()->getName();

            // If there is no request, inject form data into request
            if (!$request->get($formName)) {
                $csrf = $this->container->get('security.csrf.token_manager');
                $request->setMethod('POST');
                $request->request->set($formName, array_merge(
                    $blockForm->getData(),
                    ['_token' => $csrf->refreshToken($formName)]
                ));
            }

            $routes = $this->container->get('router')->getRouteCollection();
            $controller = $routes->get($blockForm->getRoute())->getDefaults()['_controller'];

            // Forward marche pas

            return $this->render('@KRGSeo/Block/show_form.html.twig', ['controller' => $controller]);
        }

        return new Response();
    }
}

