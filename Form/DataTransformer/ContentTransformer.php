<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use KRG\CmsBundle\Routing\UrlResolver;
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

    /** @var UrlResolver */
    protected $urlResolver;

    /**
     * ContentTransformer constructor.
     *
     * @param EngineInterface $templating
     * @param FileBase64Uploader $fileUploader
     * @param UrlResolver $urlResolver
     */
    public function __construct(EngineInterface $templating, FileBase64Uploader $fileUploader, UrlResolver $urlResolver)
    {
        $this->templating = $templating;
        $this->fileUploader = $fileUploader;
        $this->urlResolver = $urlResolver;
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

            $value = $content->html();

            if (preg_match_all('|href="(.*)"|U', $value, $matches, PREG_SET_ORDER)) {
                foreach($matches as $match) {
                    $href = $match[1];
                    try {
                        $routeInfo = $this->urlResolver->resolve($href);
                        $path = sprintf('{{ path("%s", %s) }}', $routeInfo['name'], json_encode($routeInfo['params']));
                        $value = preg_replace(sprintf('`%s`', $match[0]), sprintf('href="%s"', $path), $value);
                    } catch (\Exception $exception) {}
                }
            }
        }

        return $value;
    }
}
