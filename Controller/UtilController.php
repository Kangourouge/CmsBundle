<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/upload-image", name="krg_cms_upload_image")
     */
    public function uploadImageAction(Request $request)
    {
        $image = $request->files->get('image');
        list($width, $height) = getimagesize($image->getPathname());

        return new JsonResponse([
            'url'  => sprintf('/uploads/%s', $this->get(FileUploader::class)->upload($image)),
            'size' => [$width, $height]
        ]);
    }

    /**
     * @Route("/page-redirector", name="krg_page_admin_show")
     */
    public function pageRedirectorAction(Request $request)
    {
        $page = $this->getDoctrine()->getRepository(PageInterface::class)->find($request->get('id'));

        return $this->redirect('/'.$page->getSeo()->getUrl());
    }
}
