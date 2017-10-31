<?php

namespace KRG\SeoBundle\Form\Type;

use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class SeoFormType extends AbstractType
{
    /**
     * @var SeoFormRegistry
     */
    private $registry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SeoFormType constructor.
     *
     * @param SeoFormRegistry $registry
     * @param RouterInterface $router
     */
    public function __construct(SeoFormRegistry $registry, RouterInterface $router)
    {
        $this->registry = $registry;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, [
                    'choices' => $this->getChoices(),
                    'choice_attr' => function($type) {
                        return ['data-url' => $this->router->generate('krg_block_form_admin', ['type' => $type])];
                    }
                ])
                ->add('data', HiddenType::class);
    }

    public function getChoices()
    {
        $choices = [];
        foreach ($this->registry->all() as $key => $value) {
            $choices[sprintf('%s (%s)', $value['alias'], $key)] = $key;
        }

        return $choices;
    }

    public function getBlockPrefix()
    {
        return 'seo_form';
    }
}
