<?php

namespace KRG\CmsBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, string $dataCacheDir, array $seoParameters)
    {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getMasterRequest();
        $this->filesystemAdapter = new FilesystemAdapter('seo', 0, $dataCacheDir);
        $this->parameters = $seoParameters;
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
        if ($seo === null) {
            return null;
        }

        $item = $this->filesystemAdapter->getItem(sprintf('%s_%s', $this->request->getLocale(), $seo->getUid()));
        if ($item->isHit()) {
            return $item->get();
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

        if (null !== $this->parameters['title']['suffix']) {
            $data['title'] .= ' '.$this->parameters['title']['suffix'];
        }

        if ($this->parameters['og']) {
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

    public function getSeoUrl($key)
    {
        /* @var $page PageInterface */
        $page = $this->entityManager->getRepository(PageInterface::class)->findBy([
            'enabled' => true,
            'key'     => $key,
        ]);

        if ($page) {
            return $page->getSeo()->getUrl();
        }

        return '#';
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
