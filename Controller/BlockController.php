<?php

namespace KRG\SeoBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/seo/block")
 */
class BlockController extends AbstractController
{
    /**
     * @Route("/admin/form", name="krg_block_form_admin")
     */
    public function formAdminAction(Request $request)
    {
        $type = $request->get('type');
        $seoFormRegistry = $this->container->get(SeoFormRegistry::class);
        $seoForm = $seoFormRegistry->get($type);

        if (!$seoForm) {
            throw new InvalidArgumentException('FormType is not managed');
        }

        $form = $this->createForm($type, null, ['method' => 'GET', 'csrf_protection' => false]);

        $form->handleRequest($request);

        return $this->render('KRGSeoBundle:Block:admin_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/form/show/{key}", name="krg_block_form")
     *
     * @param Request $request
     * @param BlockFormInterface $blockForm
     * @return Response
     */
    public function formAction(Request $request, BlockFormInterface $blockForm)
    {
        $seoFormRegistry = $this->container->get(SeoFormRegistry::class);
        $seoForm = $seoFormRegistry->get($blockForm->getFormType());

        if ($seoForm) {

            $form = $this->createForm($seoForm['form'], null, ['csrf_protection' => false]);

            if (true /* check */) {
                $formData = $blockForm->getFormData();
                // Manually submit form with blockForm data
                $form->submit($formData);
            }

            try {
                // Call service handler (from tag)
                $seoForm['handler']->handle($request, $form);
                return $this->render($seoForm['template'], [
                    'form' => $form->createView()
                ]);
            } catch(\Exception $exception) {
                /* log error */
            }
        }

        return new Response();
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
            '?'.SeoFormRegistry::class,
        ]);
    }
}

