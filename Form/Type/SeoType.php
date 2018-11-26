<?php

namespace KRG\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class SeoType extends AbstractType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var array */
    protected $intlLocales;

    public function __construct(EntityManagerInterface $entityManager, array $intlLocales)
    {
        $this->entityManager = $entityManager;
        $this->intlLocales = $intlLocales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('url', null, [
                'constraints' => $options['required_url'] ? new NotNull() : null,
                'required'    => $options['required_url'],
            ])
            ->add('metaTitle')
            ->add('metaDescription');

        if ($options['affix']) {
            $builder
                ->add('preContent')
                ->add('postContent');
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        if (count($form->all()) === 0) {
            $view->vars['_external_edit_id'] = $form->getData()->getId();
        }
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) {
            /* @var $seo SeoInterface */
            $classMetadata = $this->entityManager->getClassMetadata(SeoInterface::class);
            $seo = $classMetadata->getReflectionClass()->newInstanceArgs();
            $event->setData($seo);
        }
    }

    public function onPostSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // Empty form if multilingual
        if ($data->getId() && count($this->intlLocales) > 1) {
            foreach ($form->all() as $name => $field) {
                $form->remove($name);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'   => SeoInterface::class,
            'required_url' => true,
            'affix'        => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'krg_cms_seo';
    }
}
