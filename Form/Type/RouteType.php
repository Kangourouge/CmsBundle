<?php

namespace KRG\CmsBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Route;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RouteType extends AbstractType
{
    /** @var RouteCollection */
    private $routes;

    /** @var string */
    private $regexp;

    public function __construct(RouterInterface $router, $regexp = null)
    {
        $this->routes = $router->getRouteCollection();
        $exludedRoutes = [
            'wdt',
            'admin',
            'easyadmin',
            'liip',
            'profiler',
            '_twig',
            '_guess_token',
            KRGCmsExtension::KRG_ROUTE_SEO_PREFIX
        ];

        $this->regexp = $regexp ?: '/('.join('|', $exludedRoutes).')/';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, [
                'label'       => 'seo.route',
                'placeholder' => '',
                'choices'     => $this->getChoices(),
            ])
            ->add('params', CollectionType::class, [
                'label'        => 'seo.params',
                'allow_add'    => true,
                'allow_delete' => true,
                'required'     => false
            ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $view->children['params']->vars['allow_add'] = false;
        $view->children['params']->vars['allow_delete'] = false;
    }

    public function getChoices()
    {
        $choices = [];
        /* @var $route Route */
        foreach ($this->routes as $name => $route) {
            if (preg_match($this->regexp, $name) || $route->hasRequirement('_locale')) {
                continue;
            }
            $choices[$route->getPath()] = $name;
        }

        return $choices;
    }

    public function getBlockPrefix()
    {
        return 'krg_cms_route';
    }
}
