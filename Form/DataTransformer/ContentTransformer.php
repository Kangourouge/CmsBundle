<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use KRG\CmsBundle\Service\FileBase64Uploader;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Templating\EngineInterface;

class ContentTransformer implements DataTransformerInterface
{
    /** @var EngineInterface */
    protected $templating;

    /** @var FileBase64Uploader */
    protected $fileUploader;

    public function __construct(EngineInterface $templating, FileBase64Uploader $fileUploader)
    {
        $this->templating = $templating;
        $this->fileUploader = $fileUploader;
    }

    public function transform($value)
    {
        $filename = tempnam(sys_get_temp_dir(), 'page-');
        file_put_contents($filename, $value);

        $html = $this->templating->render('KRGCmsBundle:Page:edit.html.twig', ['page' => ['content' => $value], 'block' => $filename]);

        unlink($filename);

        return base64_encode($html);
    }

    public function reverseTransform($value)
    {
        $crawler = new Crawler(base64_decode($value));
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

            $content->filter('*')->each(function (Crawler $_crawler) {
                $_crawler->each(function (Crawler $__crawler) {
                    /** @var \DOMElement $node */
                    foreach ($__crawler->getNode(0)->childNodes as $node) {
                        if ($node->nodeType === XML_TEXT_NODE) {
                            $text = trim($node->data);
                            if (strlen($text) > 0) {
                                $node->textContent = sprintf('{%% trans %%}%s{%% endtrans %%}', $text);
                            }
                        }
                    }
                });

            });

            return $content->html();
        }

        return $value;
    }
}
