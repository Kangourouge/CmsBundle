<?php

namespace KRG\CmsBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Routing\UrlResolver;
use KRG\CmsBundle\Form\DataTransformer\UrlDataTransformer;
use KRG\CmsBundle\Util\RouteHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UrlType extends TextType
{
    /** @var UrlResolver */
    protected $urlResolver;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(UrlResolver $urlResolver, EntityManagerInterface $entityManager, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->urlResolver = $urlResolver;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'Url',
            ])
            ->add('related', ChoiceType::class, [
                'label'       => 'url.bind',
                'placeholder' => '',
                'choices'     => $this->getChoices(),
            ]);

        $builder->addModelTransformer(new UrlDataTransformer($this->urlResolver, $this->entityManager, $this->router));
    }

    /**
     * List page and filters urls
     */
    public function getChoices()
    {
        $pages = $this->entityManager->getRepository(PageInterface::class)->findAll();
        $filters = $this->entityManager->getRepository(FilterInterface::class)->findWithSeo();
        $routes = RouteHelper::getRouteNames($this->router->getRouteCollection(), null, function($route) {
            return count($route->compile()->getVariables()) === 0; // Route without vars
        });

        $pageChoices = [];
        /* @var $page PageInterface */
        foreach ($pages as $page) {
            $name = $this->getChoice($page->getName(), $page->getSeo()->getUrl(), 'url.related_choice_page');
            $pageChoices[$name] = UrlDataTransformer::getRelatedIdentifier($page);
        }

        /* @var $page FilterInterface */
        $filterChoices = [];
        foreach ($filters as $filter) {
            $name = $this->getChoice($filter->getName(), $filter->getSeo()->getUrl(), 'url.related_choice_filter');
            $filterChoices[$name] = UrlDataTransformer::getRelatedIdentifier($filter);
        }

        /* @var $route Route */
        $routeChoices = [];
        foreach ($routes as $url => $route) {
            $name = $this->getChoice($route, $url, 'url.related_choice_route');
            $routeChoices[$name] = UrlDataTransformer::getTypeIdentifier('route', $route);
        }

        return array_merge($pageChoices, $filterChoices, $routeChoices);
    }

    protected function getChoice(string $name, string $url, string $transDomain, string $locale = null)
    {
        $params = ['%name%' => $name, '%url%' => $url];
        if ($locale) {
            $params['%locale%'] = $locale;
        }

        return $this->translator->transChoice($transDomain, null, $params);
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
