<?php

namespace KRG\SeoBundle\Admin;

use KRG\SeoBundle\Entity\RouteParameter;
use KRG\SeoBundle\Entity\SeoInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class SeoAdmin extends Admin
{
    protected $baseRouteName = 'admin_app_seo';
    protected $baseRoutePattern = 'seo';

    /**
     * @var Router
     */
    protected $router;

    protected $clearRoutingCache;

    protected function configureFormFields(FormMapper $formMapper)
    {
        /* @var $seo SeoRoute */
        $seo = $this->getSubject();
        $isNew = $seo->getId() === null;

        $formMapper
            ->add('route', ChoiceType::class, array(
                'choices'   => $this->getRouteChoices(),
                'disabled'  => !$isNew,
                'read_only' => !$isNew,
                'data'      => $this->getRequest()->query->get('route') ?: $seo->getRoute()
            ));

        if (!$isNew) {
            $this->availableProperties = $this->getHelperProperties($seo);

            // Add custom route parameters if exists
            if ($parametersKeys = $this->getParametersKeys($seo)) {
                $formMapper
                    ->add('parameters', 'sonata_type_immutable_array', array(
                        'keys' => $parametersKeys
                    ));
            }

            $formMapper
                ->add('url', null, array(
                    'help' => sprintf('Route name: %s', $seo->getUid())
                ))
                ->add('metaTitle')
                ->add('metaDescription', TextareaType::class, array(
                    'required' => false,
                ))
                ->add('metaRobots')
                ->add('ogTitle')
                ->add('ogDescription')
                ->add('ogImage')
            ;
        }

        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('route')
            ->add('metaTitle', null, array(
                'editable' => true
            ))
            ->add('metaDescription')
            ->add('parameters', 'array')
            ->add('url')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit'   => array(),
                    'delete' => array(),
                ),
            ));
    }

    public function onPostSubmit(FormEvent $event)
    {
        /* @var $seo SeoRoute */
        $seo = $event->getData();

        if ($seo->getUrl() === null) {
            $seo->setUrl($this->getPathFromRoute($seo->getRoute()));
        }

        $this->clearRoutingCache->exec();
    }

    public function getPathFromRoute($route)
    {
        if ($route = $this->router->getRouteCollection()->get($route)) {
            return $route->getPath();
        }

        return null;
    }

    /* */

    protected function getRouteChoices()
    {
        $choices = array();

        /* @var $route Route */
        foreach ($this->router->getRouteCollection() as $name => $route) {
            if (preg_match('`^(_|admin|sonata|liip|krg_seo).*`', $name)) {
                continue;
            }

            $choices[$name] = sprintf('%s (%s)', $name, $route->getPath());
        }

        return $choices;
    }

    /**
     * List available parameters from an entity
     *
     * @param SeoInterface $seo
     *
     * @return array|string
     */
    protected function getHelperProperties(SeoInterface $seo)
    {
        $route = $this->router->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return '';
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
            return '';
        }

        return $availableProperties;
    }

    protected function getParametersKeys(SeoInterface $seo)
    {
        $route = $this->router->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return array();
        }

        $parameters = array_flip($route->compile()->getPathVariables());
        foreach ($parameters as $key => &$value) {
            $value = array($key, TextType::class, array('required' => false));
        }

        unset($value);

        return array_values($parameters);
    }

    /* */

    public function getTemplate($name)
    {
        if ($name === 'edit') {
            return 'KRGSeoBundle:Admin:Form/edit.html.twig';
        }

        return parent::getTemplate($name);
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function setClearRoutingCache($clearRoutingCache)
    {
        $this->clearRoutingCache = $clearRoutingCache;
    }
}
