<?php

namespace KRG\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * SeoPageSeoType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }

    public function onPreSetData(FormEvent $event)
    {
        $entity = $event->getData();

        if ($entity === null) {
            /* @var $seo SeoInterface */
            $classMetadata = $this->entityManager->getClassMetadata(SeoInterface::class);
            $seo = $classMetadata->getReflectionClass()->newInstanceArgs();
            $event->setData($seo);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SeoInterface::class
        ]);
    }
}
