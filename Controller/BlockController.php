<?php

namespace KRG\SeoBundle\Controller;

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
     * @Route("/show/{key}", name="krg_block_form")
     *
     * @param Request $request
     * @param BlockFormInterface $blockForm
     * @return Response
     */
    public function formAction(Request $request, BlockFormInterface $blockForm)
    {
        $seoFormRegistry = $this->container->get(SeoFormRegistry::class);
        $seoForm = $seoFormRegistry->get($blockForm->getType());

        if ($seoForm) {
            $form = $this->createForm($seoForm['form']);
            $formName = $form->getConfig()->getName();

            // If there is no request, build data
            if (!$request->get($formName)) {
                $csrf = $this->container->get('security.csrf.token_manager');
                $data = array_merge(
                    $blockForm->getData(),
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

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
            '?'.SeoFormRegistry::class,
        ]);
    }
}

