<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Util\RouteHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
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
        $this->regexp = $regexp;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'attr'     => ['placeholder' => 'route.paste_here'],
                'mapped'   => false,
                'required' => false,
            ])
            ->add('name', ChoiceType::class, [
                'label'       => 'Route',
                'placeholder' => 'Select a route',
                'choices'     => RouteHelper::getRouteNames($this->routes, $this->regexp),
            ])
            ->add('params', CollectionType::class, [
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

    public function getBlockPrefix()
    {
        return 'krg_cms_route';
    }
}
