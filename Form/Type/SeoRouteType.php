<?php

namespace KRG\SeoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class SeoRouteType extends AbstractType
{
    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var string
     */
    private $regexp;

    /**
     * RouteType constructor.
     *
     * @param RouterInterface $router
     * @param string $regexp
     */
    public function __construct(RouterInterface $router, $regexp = null)
    {
        $this->routes = $router->getRouteCollection();
        $this->regexp = $regexp ?: '`^(_|admin|easyadmin|liip|krg_seo).*`';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, [
                'choices'     => $this->getChoices(),
                'choice_attr' => function ($key) {
                    $route = $this->routes->get($key);
                    $compiledRoute = $route->compile();
                    $parameters = array_flip($compiledRoute->getPathVariables());

                    return ['data-params' => json_encode($parameters)];
                },
            ])
            ->add('params', CollectionType::class, [
                'allow_add'    => true,
                'allow_delete' => true,
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
        /* @var $route RouterInterface */
        foreach ($this->routes as $name => $route) {
            if (preg_match($this->regexp, $name)) {
                continue;
            }
            $choices[sprintf('%s (%s)', $name, $route->getPath())] = $name;
        }

        return $choices;
    }
}
