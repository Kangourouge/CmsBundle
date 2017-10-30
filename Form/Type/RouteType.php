<?php

namespace KRG\SeoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RouteType extends AbstractType
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
        $builder->add('name', ChoiceType::class, [
                    'choices' => $this->getChoices(),
                    'choice_attr' => function($key) {
                        $route = $this->routes->get($key);
                        $parameters = array_flip($route->compile()->getPathVariables());
                        return ['data-requirements' => json_encode($parameters)];
                    }
                ])
                ->add('params', CollectionType::class, [
                    'allow_add' => true,
                    'allow_delete' => true
                ]);
    }

    public function getChoices()
    {
        $choices = [];

        /* @var $route Route */
        foreach ($this->routes as $name => $route) {
            if (preg_match($this->regexp, $name)) {
                continue;
            }
            $choices[sprintf('%s (%s)', $name, $route->getPath())] = $name;
        }
        return $choices;
    }
}
