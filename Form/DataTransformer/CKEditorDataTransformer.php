<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CKEditorDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!is_string($value)) {
            return null;
        }

        return preg_replace_callback(
            '`\{\{\ ?block\("([a-zA-Z0-9_]+)"\)\ ?\}\}`',
            function(array $matches){ return sprintf('<pre block="%s" contenteditable="false">%s</pre>', $matches[1], $matches[0]); },
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

        return $value;
    }
}