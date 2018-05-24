<?php

namespace KRG\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Routing\UrlResolver;
use KRG\CmsBundle\Form\DataTransformer\UrlDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class UrlType extends TextType
{
    /** @var UrlResolver */
    protected $urlResolver;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(UrlResolver $urlResolver, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->urlResolver = $urlResolver;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'Url',
            ])
            ->add('block', ChoiceType::class, [
                'label'       => 'url.bind',
                'placeholder' => '',
                'choices'     => $this->getChoices(),
            ]);

        $builder->addModelTransformer(new UrlDataTransformer($this->urlResolver, $this->entityManager));
    }

    public function getChoices()
    {
        $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();
        $filters = $this->entityManager->getRepository(FilterInterface::class)->findWithSeo();

        $choices = [];
        /* @var $page PageInterface */
        foreach ($pages as $page) {
            $name = $this->translator->transChoice('url.block_choice_page', null, [
                '%name%' => $page->getName(), '%url%' => $page->getSeo()->getUrl()
            ]);
            $choices[$name] = UrlDataTransformer::getBlockIdentifier($page);
        }

        /* @var $page FilterInterface */
        foreach ($filters as $filter) {
            $name = $this->translator->transChoice('url.block_choice_filter', null, [
                '%name%' => $filter->getName(), '%url%' => $filter->getSeo()->getUrl()
            ]);
            $choices[$name] = UrlDataTransformer::getBlockIdentifier($filter);
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'compound' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'krg_cms_url';
    }
}
