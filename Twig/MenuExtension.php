<?php

namespace KRG\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use KRG\SeoBundle\Entity\MenuInterface;
use KRG\SeoBundle\Service\MenuBuilderInterface;

/**
 * Class MenuExtension
 * @package KRG\SeoBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $cacheFileName;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var \Twig_TemplateWrapper
     */
    private $template;

    /**
     * @var MenuBuilderInterface
     */
    private $menuBuilder;

    /**
     * MenuExtension constructor.
     * @param EntityManagerInterface $entityManager
     * @param $cacheDir
     * @param MenuBuilderInterface $menuBuilder
     * @param null $theme
     */
    public function __construct(EntityManagerInterface $entityManager, $cacheDir, MenuBuilderInterface $menuBuilder, $theme = null)
    {
        $this->entityManager = $entityManager;
        $this->cacheDir = $cacheDir;
        $this->menuBuilder = $menuBuilder;
        $this->theme = $theme ?? 'KRGSeoBundle:Menu:layout.html.twig';
    }

    /**
     * Build blocks into a specific template
     *
     * @param \Twig_Environment $environment
     * @return \Twig_TemplateWrapper
     */
    private function getTemplate(\Twig_Environment $environment)
    {
        if ($this->template) {
            return $this->template;
        }

        $this->template = $environment->load($this->theme); // Load template from cache

        return $this->template;
    }

    /**
     * Generate $this->cacheFileName twig template composed of each blocks
     */
    public function createFileTemplate()
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $path = sprintf('%s/seo_menu.html.twig', $this->cacheDir);
        if (!file_exists($path)) {
            /* @var $repository NestedTreeRepository */
            $repository = $this->entityManager->getRepository(MenuInterface::class);
            $nodes = $repository->childrenHierarchy();
            $content = $this->menuBuilder->build($nodes);

            return $content;
//            return (bool)file_put_contents($path, implode('', $content));
        }

        return false;
    }

    public function getMenu(\Twig_Environment $environment, $key)
    {
        $template = $this->getTemplate($environment);

        $content = $this->createFileTemplate();

        return $environment->render('@KRGSeo/Menu/layout.html.twig', ['menu' => [$content]]);


//        $environment->setCache($this->cacheDir);
//        $environment->setLoader(new \Twig_Loader_Chain([
//            $environment->getLoader(), // Preserve old loader
//            new \Twig_Loader_Filesystem([$this->cacheDirKrg]) // Add KRG cache dir
//        ]));
//
//        $this->template = $environment->load($this->cacheFileName); // Load template from cache

        $template->renderBlock($key);
    }

    public function getFunctions()
    {
        return [
            'krg_menu' => new \Twig_SimpleFunction('krg_menu', array($this, 'getMenu'), [
                'needs_environment' => true, // Tell twig we need the environment
                'is_safe'           => ['html'],
            ]),
        ];
    }
}
