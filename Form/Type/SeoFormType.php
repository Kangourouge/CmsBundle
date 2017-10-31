<?php

namespace KRG\SeoBundle\Form\Type;

use KRG\SeoBundle\Form\DataTransformer\SeoFormDataTransformer;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $this->getChoices(),
            ])
            ->add('data', HiddenType::class)
            ->addModelTransformer(new SeoFormDataTransformer());
    }

    public function getChoices()
    {
        $choices = [];
        foreach ($this->registry->all() as $key => $value) {
            $choices[sprintf('%s (%s)', $value['alias'], $key)] = $key;
        }

        return $choices;
    }
}
