<?php

namespace KRG\CmsBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Form\FilterRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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

        $form = $this->createForm($type, null, ['method' => 'GET', 'csrf_protection' => false]);

        return $this->renderForm($form, '@KRGCms/Filter/edit.html.twig', $request, $config, [
            'display_form' => true,
        ]);
    }

    /**
     * @Route(
     *     "/show/{key}/{page}",
     *     defaults = {"page": "1"},
     *     requirements = { "page": "\d+" },
     *     name="krg_cms_filter_show"
     * )
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
                return $this->renderForm($form, $config['template'], $request, $config);
            } catch(\Exception $exception) {
                $logger = $this->container->get('logger');
                $logger->error(sprintf('Block form error (id: %d) (%s)', $filter->getId(), $exception->getMessage()));
                $filter->setWorking(false);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response();
    }

    protected function renderForm(FormInterface $form, string $template, Request $request, array $config, array $options = [])
    {
        $vars = [];
        if ($config['handler']) {
            $vars = $config['handler']->perform($request, $form, $options);
        }
        $vars['form'] = $form->createView();
        $vars['config'] = $config;

        return $this->render($template, $vars);
    }
}

