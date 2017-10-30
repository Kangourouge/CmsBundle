<?php

namespace KRG\SeoBundle\Form\Type;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Form\EventListener\SeoEventListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('metaRobots')
            ->add('ogTitle')
            ->add('ogDescription')
            ->add('ogImage')
            ;
        
        $builder->addEventSubscriber(new SeoEventListener());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SeoInterface::class
        ]);
    }
}
