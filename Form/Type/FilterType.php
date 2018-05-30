<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Form\DataTransformer\FilterDataTransformer;
use KRG\CmsBundle\Form\FilterRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterType extends AbstractType
{
    /** @var FilterRegistry */
    protected $filterRegistry;

    public function __construct(FilterRegistry $registry)
    {
        $this->filterRegistry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'     => $this->getChoices(),
                'placeholder' => '',
            ])
            ->add('data', HiddenType::class)
            ->addModelTransformer(new FilterDataTransformer());
    }

    public function getChoices()
    {
        $choices = [];
        foreach ($this->filterRegistry->all() as $key => $value) {
            $choices[sprintf('%s (%s)', $value['alias'], $key)] = $key;
        }

        return $choices;
    }

    public function getBlockPrefix()
    {
        return 'krg_cms_filter';
    }
}
