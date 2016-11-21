<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Array with key dataTransformer
 */
class ArrayKeyDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($data)
    {
        if (!$data) {
            return [];
        }

        return is_string($data) ? json_decode($data, true) : $data;
    }

}
