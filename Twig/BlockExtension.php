<?php

namespace KRG\SeoBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use KRG\SeoBundle\Entity\BlockFormInterface;
use KRG\SeoBundle\Entity\BlockStaticInterface;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Entity\SeoPageInterface;
use Doctrine\ORM\EntityManager;
use KRG\SeoBundle\KRGSeoBundle;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\PhpArrayCache;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BlockExtension extends \Twig_Extension
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
    private $cacheKey;

    public function __construct(EntityManagerInterface $entityManager, $cacheDir)
    {
        $this->entityManager = $entityManager;
        $this->cacheDir = $cacheDir;
        $this->cacheKey = 'krg_seo_blocks';
    }

    /**
     * Build blocks into a specific template
     *
     * @return \Twig_Template
     */
    private function getTemplateFromCache()
    {
        // Dossier krg a faire
        $path = sprintf('%s/twig/krg_seo_blocks.html.twig', $this->cacheDir);

        if (!file_exists($path)) {
            $blocksStatic = $this->entityManager->getRepository(BlockStaticInterface::class)->findAll();
            $blocksForm = $this->entityManager->getRepository(BlockFormInterface::class)->findAll();

            $content = [];
            /* @var $block BlockStaticInterface */
            foreach ($blocksStatic as $blockStatic) {
                $content[] = sprintf("{%% block %s %%}%s{%% endblock %%}\n", $blockStatic->getKey(), $blockStatic->getContent());
            }
            /* @var $blockForm BlockFormInterface */
            foreach ($blocksForm as $blockForm) {
    //            $content[] = sprintf("{%% block %s %%}{{ render(controller('KRGSeoBundle:Block:form', {'blockForm': %d})) }}{%% endblock %%}\n", $blockForm->getKey(), $blockForm->getId());
            }

            file_put_contents($path, implode('', $content));
        }


        $loader = new \Twig_Loader_Filesystem(sprintf('%s/twig', $this->cacheDir));
        $twig = new \Twig_Environment($loader, array('cache' => $this->cacheDir));
        $template = $twig->load('krg_seo_blocks.html.twig');

        return $template;
    }

    /**
     * Render a block by is key
     *
     * @param \Twig_Environment $environment
     * @param $key
     */
    public function getBlock(\Twig_Environment $environment, $key)
    {
        $block = $this->entityManager->getRepository(BlockStaticInterface::class)->findOneBy([
            'key'     => $key,
            'enabled' => true,
        ]);

        if ($block) {
            $template = $this->getTemplateFromCache();
            if ($template->hasBlock($key, [])) {
                echo $template->renderBlock($key, []);
            }
        }
    }

    public function getFunctions()
    {
        return array(
            'krg_block' => new \Twig_SimpleFunction('krg_block', array($this, 'getBlock'), array(
                'needs_environment' => true,
                'is_safe'           => array('html'),
            ))
        );
    }
}
