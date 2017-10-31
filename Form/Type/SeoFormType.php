<?php

namespace KRG\SeoBundle\Form\Type;

use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

class SeoFormType extends AbstractType
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var SeoFormRegistry
     */
    private $registry;

    /**
     * SeoRouteType constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param SeoFormRegistry $registry
     */
    public function __construct(FormFactoryInterface $formFactory, SeoFormRegistry $registry)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, [
                    'choices' => $this->getChoices()
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
