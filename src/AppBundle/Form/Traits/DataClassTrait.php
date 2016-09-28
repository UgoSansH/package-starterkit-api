<?php

namespace AppBundle\Form\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;

trait DataClassTrait
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * Set dataClass
     *
     * @param string $dataClass
     *
     * @return self
     */
    public function setDataClass(string $dataClass): self
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * Get dataClass
     *
     * @return string
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

}
