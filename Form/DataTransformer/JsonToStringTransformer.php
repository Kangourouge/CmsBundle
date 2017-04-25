<?php

namespace KRG\SeoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToStringTransformer implements DataTransformerInterface
{
    /**
     * Transform an array to a JSON string
     */
    public function transform($array)
    {
        return json_encode($array);
    }

    /**
     * Transform a JSON string to an array
     */
    public function reverseTransform($string)
    {
        return json_decode($string, true);
    }
}
