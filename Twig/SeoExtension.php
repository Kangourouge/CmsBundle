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

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getMasterRequest();
        $this->filesystemAdapter = new FilesystemAdapter('seo', 0, $dataCacheDir);
    }

    public function getSeoHead(\Twig_Environment $environment)
    {
        return $environment->render('KRGCmsBundle:Seo:head.html.twig', $this->getSeoVars());
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
            return [];
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
            'metaTitle'       => null,
            'metaDescription' => null,
            'metaRobots'      => null,
            'preContent'      => null,
            'postContent'     => null,
        ];

        foreach ($data as $key => &$value) {
            $getter = 'get' . ucfirst($key);
            if (method_exists($seo, $getter)) {
                if ($input = call_user_func([$seo, $getter])) {
                    $value = $twig->createTemplate($input)->render($params);
                }
            }
        }
        unset($value);

        $item->set($data);
        $this->filesystemAdapter->save($item);

        return $data;
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
        ];
    }
}
