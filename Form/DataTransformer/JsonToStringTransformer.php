<?php

namespace KRG\SeoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToStringTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $array
     * @return string
     */
    public function transform($array)
    {
        return json_encode($array);
    }

    /**
     * @param mixed $string
     * @return mixed
     */
    public function reverseTransform($string)
    {
        return json_decode($string, true);
    }
}
