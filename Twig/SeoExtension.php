<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Finder\SeoFinder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class SeoExtension extends \Twig_Extension
{
    /** @var $entityManager EntityManager */
    private $entityManager;

    /** @var Request */
    private $request;

    /** @var FilesystemAdapter */
    private $filesystemAdapter;

    /** @var array */
    private $parameters;

    /** @var SeoFinder */
    private $seoFinder;

    /** @var array */
    private $intlLocales;

    /** @var Serializer */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, string $dataCacheDir, array $seoParameters, SeoFinder $seoFinder, array $intlLocales)
    {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getMasterRequest();
        $this->filesystemAdapter = new FilesystemAdapter('seo', 0, $dataCacheDir);
        $normalizer = new PropertyNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
        $this->parameters = $seoParameters;
        $this->seoFinder = $seoFinder;
        $this->intlLocales = $intlLocales;
    }

    public function getSeoHead(\Twig_Environment $environment)
    {
        if ($seoVars = $this->getSeoVars()) {
            return $environment->render('KRGCmsBundle:Seo:head.html.twig', $seoVars);
        }

        return null;
    }

    public function getSeoPreContent()
    {
        $data = $this->getSeoVars();

        return $data['preContent'] ?? null;
    }

    public function getSeoPostContent()
    {
        $data = $this->getSeoVars();

        return $data['postContent'] ?? null;
    }

    protected function getSeoVars()
    {
        if ($this->request === null) {
            return null;
        }

        /* @var $seo SeoInterface */
        $seo = $this->request->get('_seo');
        if (is_string($seo)) {
            $seo = $this->serializer->deserialize($seo, $this->entityManager->getClassMetadata(SeoInterface::class)->getName(), 'json');
        }

        if ($seo === null) {
            return null;
        }

        $item = $this->filesystemAdapter->getItem(sprintf('%s_%s', $this->request->getLocale(), $seo->getUid()));
        if ($item->isHit()) {
            return $item->get();
        }

        if (count($this->intlLocales) > 0) {
            // Find and bind Seo translations
            $translatableRepository = $this->entityManager->getRepository(Translation::class);
            if ($translations = $translatableRepository->findTranslations($seo)) {
                if (null !== ($trans = ($translations[$this->request->getLocale()] ?? null))) {
                    foreach ($trans as $property => $value) {
                        $method = 'set'.ucwords($property);
                        if (method_exists($seo, $method)) {
                            $seo->{$method}($value);
                        }
                    }
                }
            }
        }

        // Get usefull parameters from the request
        $params = array_filter($this->request->attributes->all(), function ($key) {
            return substr($key, 0, 1) !== '_';
        }, ARRAY_FILTER_USE_KEY);

        $twig = new \Twig_Environment(new \Twig_Loader_Array([]));

        $data = [
            'title'       => $this->fetchVars($seo->getMetaTitle(), $params, $twig),
            'preContent'  => $this->fetchVars($seo->getPreContent(), $params, $twig),
            'postContent' => $this->fetchVars($seo->getPostContent(), $params, $twig),
        ];

        $data['metas'] = [
            'description' => $this->fetchVars($seo->getMetaDescription(), $params, $twig),
        ];

        if (strlen($data['title']) && isset($this->parameters['title']['suffix']) && strlen($this->parameters['title']['suffix']) > 0) {
            $data['title'] .= ' '.$this->parameters['title']['suffix'];
        }

        if (isset($this->parameters['og']) && $this->parameters['og']) {
            $data['ogs'] = [
                'title'       => $data['title'],
                'description' => $data['metas']['description'],
            ];
        }

        $item->set($data);
        $this->filesystemAdapter->save($item);

        return $data;
    }

    protected function fetchVars($content, $params, \Twig_Environment $twig)
    {
        if (null == $content) {
            return null;
        }

        return $twig->createTemplate($content)->render($params);
    }

    /**
     * Get Seo url from several inputs
     */
    public function getSeoUrl($input)
    {
        if ($input instanceof SeoInterface) {
            return $input->getUrl();
        }

        /* @var $page PageInterface */
        $page = $this->entityManager->getRepository(PageInterface::class)->findBy([
            'enabled' => true,
            'key'     => $input,
        ]);

        if ($page) {
            return $page->getSeo()->getUrl();
        }

        return '#';
    }

    /**
     * Find Seo by custom $data (only filters for the moment)
     */
    public function findSeo(array $data, string $formType = null)
    {
        return $this->seoFinder->findSeoByFilterData($data, $formType);
    }

    public function getFunctions()
    {
        return [
            'seo_head'         => new \Twig_SimpleFunction('seo_head', [$this, 'getSeoHead'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            'seo_url'          => new \Twig_SimpleFunction('seo_url', [$this, 'getSeoUrl'], [
                'is_safe' => ['html'],
            ]),
            'seo_find'         => new \Twig_SimpleFunction('seo_find', [$this, 'findSeo'], [
                'is_safe' => ['html'],
            ]),
            'seo_pre_content'  => new \Twig_SimpleFunction('seo_pre_content', [$this, 'getSeoPreContent'], [
                'is_safe' => ['html'],
            ]),
            'seo_post_content' => new \Twig_SimpleFunction('seo_post_content', [$this, 'getSeoPostContent'], [
                'is_safe' => ['html'],
            ]),
            'seo_canonical' => new \Twig_SimpleFunction('seo_canonical', [$this, 'getCanonical'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
