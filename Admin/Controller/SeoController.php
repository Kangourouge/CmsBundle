<?php

namespace KRG\SeoBundle\Admin\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Routing\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SeoController extends BaseAdminController
{
    protected function createSeoEntityFormBuilder(SeoInterface $entity, $view)
    {
        $formBuilder = $this->createEntityFormBuilder($entity, $view);

        $formBuilder->remove('route');
        $formBuilder->add('route', ChoiceType::class, array(
            'choices'   => $this->getRouteChoices(),
            'disabled'  => (bool)$entity->getId(), // Disabled on edition
        ));

        // TODO: gérer les paramètres de la route et les stocker dans Seo->parameters (json_array)
        $this->handleParametersKeys($entity, $formBuilder);

        return $formBuilder;
    }

    /**
     * Return all routes
     *
     * @return array
     */
    private function getRouteChoices()
    {
        $choices = [];
        /* @var $route Route */
        foreach ($this->get('router')->getRouteCollection() as $name => $route) {
            if (preg_match('`^(_|admin|easyadmin|liip|krg_seo).*`', $name)) {
                continue;
            }
            $choices[sprintf('%s (%s)', $name, $route->getPath())] = $name;
        }
        return $choices;
    }

    /**
     * @param SeoInterface $seo
     * @param FormBuilderInterface $formBuilder
     * @return array|FormBuilderInterface
     */
    protected function handleParametersKeys(SeoInterface $seo, FormBuilderInterface $formBuilder)
    {
        $route = $this->get('router')->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return [];
        }

        $parameters = array_flip($route->compile()->getPathVariables());
        foreach ($parameters as $key => $value) {
            $formBuilder->add($key, TextType::class, array(
                'label'    => sprintf('%s parameter', ucfirst($key)),
                'required' => false,
                'mapped'   => false
            ));
        }

        return $formBuilder;
    }
}
