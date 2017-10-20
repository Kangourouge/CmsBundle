<?php

namespace KRG\SeoBundle\Controller;

use KRG\SeoBundle\DependencyInjection\ClearRoutingCache;
use KRG\SeoBundle\Entity\SeoPageInterface;
use KRG\SeoBundle\Util\Redirector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/seo/page")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="krg_seo_admin_seo_page_update");
     */
    public function updateSeoPageAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /* @var $seoPage SeoPageInterface */
        $seoPage = $entityManager->getRepository(SeoPageInterface::class)->find($id);
        if (!$seoPage) {
            throw $this->createNotFoundException();
        }

        $formData = $this->container->get('session')->get(sprintf('seo_page_%s', $seoPage->getId()));
        if ($formData) {
            $seoPage->setFormData($formData);
            $entityManager->flush();
            $this->container->get(ClearRoutingCache::class)->exec();
        }

        return $this->redirect(Redirector::getEasyAdminUrl(
            $this->container->get('router')->generate('easyadmin'),
            'SeoPage',
            'edit',
            $seoPage->getId()
        ));
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
                '?'.ClearRoutingCache::class
            ]);
    }
}
