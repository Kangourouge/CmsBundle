<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use KRG\CmsBundle\Service\FileBase64Uploader;
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
     * @var FileBase64Uploader
     */
    protected $fileUploader;

    /**
     * ContentTransformer constructor.
     * @param EngineInterface    $templating
     * @param FileBase64Uploader $fileUploader
     */
    public function __construct(EngineInterface $templating, FileBase64Uploader $fileUploader)
    {
        $this->templating = $templating;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function transform($value)
    {
       return $this->templating->render('KRGCmsBundle:Page:edit.html.twig', ['page' => ['content' => $value]]);
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function reverseTransform($value)
    {
        $crawler = new Crawler($value);
        $content = $crawler->filter('div#krg_cms_page_wrapper');

        if ($content->getNode(0)) {
            $images = $content->filter('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                if (preg_match('/^data:(image\/([a-z]+));base64,(.*)$/', $src)) {
                    $path = $this->fileUploader->uploadBase64($src);
                    $image->setAttribute('src', $path);
                }
            }

            return $content->html();
        }

        return $value;
    }
}
