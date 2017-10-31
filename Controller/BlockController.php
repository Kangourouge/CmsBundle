<?php

namespace KRG\SeoBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seo/block")
 */
class BlockController extends AbstractController
{
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
            $form = $this->createForm($seoForm['form']);
            $formName = $form->getConfig()->getName();


            // If there is no request, build data
            if (!$request->get($formName)) {
                $csrf = $this->container->get('security.csrf.token_manager');
                $data = array_merge(
                    $blockForm->getFormData(),
                    ['_token' => $csrf->refreshToken($formName)]
                );

                // Manually submit form with blockForm data
                $form->submit($data);
            }

            // Call service handler (from tag)
            $seoForm['handler']->handle($request, $form);

            return $this->render($seoForm['template'], [
                'form' => $form->createView()
            ]);
        }

        return new Response();
    }

    /**
     * @Route("/form/admin/{type}", name="krg_block_form_admin")
     */
    public function formAdminAction(Request $request, $type)
    {
        $seoFormRegistry = $this->container->get(SeoFormRegistry::class);
        $seoForm = $seoFormRegistry->get($type);

        if (!$seoForm) {
            throw new InvalidArgumentException('FormType is not managed');
        }

        $form = $this->createForm($seoForm['form']);
        $formName = $form->getConfig()->getName();

        // If there is no request, build data
        if (!$request->get($formName)) {
            $csrf = $this->container->get('security.csrf.token_manager');
            $data = array_merge(
                $request->get('data'),
                ['_token' => $csrf->refreshToken($formName)]
            );

            $form->submit($data);
        }

        return $this->render('KRGSeoBundle:Block:admin.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
            '?'.SeoFormRegistry::class,
        ]);
    }
}

