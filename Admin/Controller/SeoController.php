<?php

namespace KRG\SeoBundle\Admin\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Routing\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends BaseAdminController
{
    protected function createSeoEntityFormBuilder(SeoInterface $entity, $view)
    {
        $formBuilder = $this->createEntityFormBuilder($entity, $view);

        // Assign Seo.route choices
        $formBuilder->remove('route');
        $formBuilder->add('route', ChoiceType::class, array(
            'choices'  => $this->getRouteChoices(),
            'disabled' => (bool)$entity->getId(), // Disabled on edition
            'data'     => $this->request->get('route')
        ));

        if ($view === 'edit') {
            $this->handleParametersKeys($entity, $formBuilder); // immutable
            $formBuilder->remove('url');
            $formBuilder->add('url', TextType::class, array(
                'attr'  => array('available' => implode(', ', $this->getHelperProperties($entity)))
            ));
        }

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
    private function handleParametersKeys(SeoInterface $seo, FormBuilderInterface $formBuilder)
    {
        $route = $this->get('router')->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return [];
        }

        $parameters = array_flip($route->compile()->getPathVariables());
        foreach ($parameters as $key => $value) {
//            $formBuilder->add($key, TextType::class, array(
//                'label'    => sprintf('%s parameter', ucfirst($key)),
//                'required' => false,
//                'mapped'   => false
//            ));
        }

        return $formBuilder;
    }

    /**
     * List available parameters from an entity
     *
     * @param SeoInterface $seo
     *
     * @return array|string
     */
    private function getHelperProperties(SeoInterface $seo)
    {
        $route = $this->get('router')->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return [];
        }

        $availableProperties = $route->compile()->getVariables();
        list($class, $method) = preg_split('`::`', $route->getDefault('_controller'));
        $reflection = new \ReflectionMethod($class, $method);

        /* @var $parameter \ReflectionParameter */
        foreach ($reflection->getParameters() as $parameter){
            if ($parameter->getClass() === NULL || $parameter->getClass() && $parameter->getClass()->getName() === Request::class) {
                continue;
            }

            $entityName = $parameter->getName();
            $properties = array_map(function(\ReflectionProperty $property) use ($entityName) {
                return sprintf('%s.%s', $entityName, $property->getName());
            }, $parameter->getClass()->getProperties());

            $availableProperties = array_merge($availableProperties, $properties);
        }

        if (count($availableProperties) === 0) {
            return [];
        }

        return $availableProperties;
    }
}
