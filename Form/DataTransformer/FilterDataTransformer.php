<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class FilterDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!is_array($value)) {
            return ['type' => null, 'data' => null];
        }

        return [
            'type' => $value['type'],
            'data' => json_encode($value['data']),
        ];
    }

    public function reverseTransform($value)
    {
        $data = [
            'type' => $value['type'],
            'data' => json_decode($value['data'], true),
        ];

        unset($data['data']['_token']);

        return $data;
    }
}
