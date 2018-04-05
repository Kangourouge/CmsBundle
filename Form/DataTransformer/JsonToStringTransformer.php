<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToStringTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        return json_encode($array);
    }

    public function reverseTransform($string)
    {
        return json_decode($string, true);
    }
}
