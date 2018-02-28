<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/cms/util")
 */
class UtilController extends AbstractController
{
    /**
     * @Route("/route-from-url", name="krg_cms_route_from_url")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function routeFromUrlAction(Request $request)
    {
        if ($url = $request->get('url')) {
            try {
                $url = str_replace(['http://', $request->getHttpHost()], '', $url);
                $data = $this->get('router')->match($url);
            } catch (\Exception $e) {
                throw new \Exception('No matching route');
            }

            return new JsonResponse($data);
        }

        throw new \Exception();
    }

    /**
     * @Route("/redirector/{page}", name="krg_page_admin_show")
     */
    public function pageRedirectorAction(Request $request, PageInterface $page)
    {
        return $this->redirect('/'.$page->getSeo()->getUrl());
    }
}
