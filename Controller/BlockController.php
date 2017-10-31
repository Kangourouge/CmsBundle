<?php

namespace KRG\SeoBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        if (!$type) {
            throw new BadRequestHttpException('Request must have a type parameter');
        }

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
        if ($blockForm->isEnabled() && $blockForm->isWorking() && $seoForm) {
            $form = $this->createForm($seoForm['form'], null, ['csrf_protection' => false]);

            if (true /* TODO: check request */) {
                // Manually submit form with blockForm data
                $form->submit($blockForm->getPureFormData());
            }

            try {
                // Call service handler (from tag)
                $form->handleRequest($request);
                if ($form->isValid() && $seoForm['handler']) {
                    $seoForm['handler']->perform($request, $form);
                }

                return $this->render($seoForm['template'], [
                    'form' => $form->createView()
                ]);
            } catch(\Exception $exception) {
                // Log an error and update blockForm
                $logger = $this->container->get('logger');
                $logger->error(sprintf('Block form error (id: %d) (%s)', $blockForm->getId(), $exception->getMessage()));
                $blockForm->setWorking(false);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response();
    }
}

