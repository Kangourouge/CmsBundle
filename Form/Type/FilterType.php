<?php

namespace KRG\CmsBundle\Form\Type;

use KRG\CmsBundle\Form\DataTransformer\FilterDataTransformer;
use KRG\CmsBundle\Form\FilterRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class FilterType extends AbstractType
{
    /**
     * @var FilterRegistry
     */
    private $registry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SeoFormType constructor.
     *
     * @param FilterRegistry $registry
     * @param RouterInterface $router
     */
    public function __construct(FilterRegistry $registry, RouterInterface $router)
    {
        $this->registry = $registry;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $this->getChoices(),
            ])
            ->add('data', HiddenType::class)
            ->addModelTransformer(new FilterDataTransformer());
    }

    public function getChoices()
    {
        $choices = [''];
        foreach ($this->registry->all() as $key => $value) {
            $choices[sprintf('%s (%s)', $value['alias'], $key)] = $key;
        }

        return $choices;
    }
}
