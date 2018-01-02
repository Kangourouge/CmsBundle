<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\DataTransformerInterface;

class CKEditorDataTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * CKEditorDataTransformer constructor.
     * @param $uploadDir
     */
    public function __construct($webDir, $uploadDir)
    {
        $this->webDir = $webDir;
        $this->uploadDir = $uploadDir;
    }

    public function transform($value)
    {
        if (!is_string($value)) {
            return null;
        }

        return preg_replace_callback(
            '`\{\{\ ?block\("([a-zA-Z0-9_]+)"\)\ ?\}\}`',
            function(array $matches){
                return sprintf('<pre block="%s" contenteditable="false">%s</pre>', $matches[1], $matches[0]);
            },
            $value
        );
    }

    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            return null;
        }

        $value = preg_replace_callback(
            '`<pre\ block="([a-zA-Z0-9_]+)"[^>]*>[^<]*</pre>`',
            function(array $matches){ return sprintf('{{ block("%s") }}', $matches[1]); },
            $value
        );

        $package = new PathPackage($this->uploadDir, new EmptyVersionStrategy());

        $value = preg_replace_callback(
            '`<img\ .*src="data:image/(.+);base64,([^"]+)"[^>]*>`',
            function(array $matches) use ($package) {

                $filename = sprintf('%s.%s', sha1($matches[2]), $matches[1]);

                $path = sprintf('%s/%s/%s', $this->webDir, $this->uploadDir, $filename);

                $fp = fopen($path, 'wb');
                fwrite($fp, base64_decode($matches[2]));
                fclose($fp);

                return sprintf('<img src="%s"/>', $package->getUrl($filename));
            },
            $value
        );

        return $value;
    }
}