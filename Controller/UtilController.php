<?php

namespace KRG\CmsBundle\Controller;

use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Model\RouteInfo;
use KRG\CmsBundle\Image\FileBase64Uploader;
use KRG\CmsBundle\Util\Helper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/cms/util")
 */
class UtilController extends AbstractController
{
    /**
     * @Route("/route_data", name="krg_cms_route_data")
     * @Security("has_role('ROLE_ADMIN')"))
     */
    public function getRouteDataAction(Request $request)
    {
        $router = $this->get('router');
        $routeCollection = $router->getRouteCollection();
        $routeName = $request->get('route');
        $url = $request->get('url');
        $route = null;

        $routeInfo = new RouteInfo();
        $routeInfo->setRoute($routeName);

        if ($url) {
            try {
                $data = $router->match(str_replace(['http://', $request->getHttpHost()], '', $url));
            } catch (\Exception $e) {
                throw new \Exception('No matching route');
            }

            $routeInfo
                ->setRoute($data['_route'])
                ->setParameters(RouteInfo::extractParameters($data));
        }

        if ($routeInfo->getRoute()) {
            $seoRepository = $this->getDoctrine()->getRepository(SeoInterface::class);
            if ($seo = $seoRepository->findOneBy(['uid' => $routeInfo->getRoute()])) {
                $routeInfo->setRoute($seo->getRouteName());
            }

            /** @var $route \Symfony\Component\Routing\Route */
            if ($route = $routeCollection->get($routeInfo->getRoute())) {
                if ($route->hasDefault('_canonical_route')) {
                    $routeInfo->setRoute($route->getDefault('_canonical_route'));
                    $route = $routeCollection->get($routeInfo->getRoute());
                }

                if ($route->hasDefault('_controller')) {
                    $routeInfo
                        ->setController($route->getDefault('_controller'))
                        ->setProperties(RouteInfo::extractProperties(Helper::getAvailablePropertiesFromRoute($route)));
                }
            } else {
                throw new \Exception('No matching route');
            }
        }

        if ($route && $routeName) {
            $routeInfo->setParameters(RouteInfo::extractParameters(array_flip($route->compile()->getPathVariables())));
        }

        return new JsonResponse($routeInfo->toArray());
    }

    /**
     * @Route("/upload-image", name="krg_cms_upload_image")
     */
    public function uploadImageAction(Request $request)
    {
        $image = $request->files->get('image');
        list($width, $height) = getimagesize($image->getPathname());

        return new JsonResponse([
            'url'  => sprintf('/uploads/%s', $this->get(FileBase64Uploader::class)->upload($image)),
            'size' => [$width, $height]
        ]);
    }

    /**
     * @Route("/page-redirector", name="krg_page_admin_show")
     */
    public function pageRedirectorAction(Request $request)
    {
        $page = $this->getDoctrine()->getRepository(PageInterface::class)->find($request->get('id'));
        $url = $page->getSeo()->getUrl();

        return $this->redirect(sprintf('%s%s', $url[0] === '/' ? '' : '/', $url));
    }
}
