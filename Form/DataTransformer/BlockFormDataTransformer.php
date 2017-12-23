<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BlockFormDataTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     * @return array
     */
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

    /**
     * @param mixed $value
     * @return array
     */
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
