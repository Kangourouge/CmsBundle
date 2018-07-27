<?php

namespace KRG\CmsBundle\Entity\Manager;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;

class PageManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createPage(string $name, string $url, string $content = null)
    {
        /** @var $page PageInterface */
        $page = $this->entityManager->getClassMetadata(PageInterface::class)->getReflectionClass()->newInstance();
        /** @var $seo SeoInterface */
        $seo = $this->entityManager->getClassMetadata(SeoInterface::class)->getReflectionClass()->newInstance();

        $seo
            ->setUrl($url)
            ->setEnabled(true);
        $page
            ->setName($name)
            ->setContent($content)
            ->setSeo($seo)
            ->setWorking(true)
            ->setEnabled(true);

        return $page;
    }
}
