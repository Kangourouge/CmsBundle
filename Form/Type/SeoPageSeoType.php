<?php

namespace KRG\SeoBundle\Form\Type;

use KRG\SeoBundle\Entity\SeoInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoPageSeoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled')
            ->add('url')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('metaRobots')
            ->add('ogTitle')
            ->add('ogDescription')
            ->add('ogImage')
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SeoInterface::class
        ]);
    }
}
