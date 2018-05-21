<?php

namespace KRG\CmsBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Form\FilterRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/cms/filter")
 */
class FilterController extends AbstractController
{
    /**
     * @Route("/admin", name="krg_cms_filter_admin")
     */
    public function adminAction(Request $request)
    {
        $type = $request->get('type');
        if (!$type) {
            throw new BadRequestHttpException('Request must have a type parameter');
        }

        $filterRegistry = $this->container->get(FilterRegistry::class);
        $config = $filterRegistry->get($type);
        if (!$config) {
            throw new InvalidArgumentException('FormType is not managed');
        }

        dump($config);

        $form = $this->createForm($type, null, ['method' => 'GET', 'csrf_protection' => false]);
        $form->handleRequest($request);

        return $this->render('KRGCmsBundle:Filter:edit.html.twig', [
            'form'   => $form->createView(),
            'config' => $config
        ]);
    }

    /**
     * @Route("/show/{key}", name="krg_cms_filter_show")
     */
    public function showAction(Request $request, FilterInterface $filter)
    {
        $filterRegistry = $this->container->get(FilterRegistry::class);
        $config = $filterRegistry->get($filter->getFormType());

        if ($filter->isEnabled() && $filter->isWorking() && $config) {
            $form = $this->createForm($config['form'], null, ['csrf_protection' => false]);

            if (true /* TODO: check request */) {
                // Manually submit form with filter data
                $form->submit($filter->getPureFormData());
            }

            try {
                // Call service handler (from tag)
                $form->handleRequest($request);
                if ($form->isValid() && $config['handler']) {
                    $config['handler']->perform($request, $form);
                }

                return $this->render($config['template'], [
                    'form' => $form->createView()
                ]);
            } catch(\Exception $exception) {
                // Log an error and update filter
                $logger = $this->container->get('logger');
                $logger->error(sprintf('Block form error (id: %d) (%s)', $filter->getId(), $exception->getMessage()));
                $filter->setWorking(false);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response();
    }
}

