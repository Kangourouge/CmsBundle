<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Templating\EngineInterface;

class ContentTransformer implements DataTransformerInterface
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function transform($value)
    {
       return $this->templating->render('KRGCmsBundle:Page:edit.html.twig', ['page' => ['content' => $value]]);
    }

    public function reverseTransform($value)
    {
        $crawler = new Crawler($value);
        $content = $crawler->filter('div#krg_cms_wrapper');

        if ($content->getNode(0)) {
            return trim($content->html());
        }

        return $value;
    }
}
