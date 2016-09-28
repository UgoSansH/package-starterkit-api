<?php

namespace AppBundle\Controller\Traits;

use Symfony\Component\Form\Form;

/**
 * Create unamed form
 */
trait RestFormTrait
{
    /**
     * Create named form
     *
     * @param string $typeClass
     * @param mixed  $formData
     *
     * @return Form
     */
    protected function createRestForm(string $typeClass, $formData)
    {
        return $this->get('form.factory')->createNamed('', $typeClass, $formData);
    }

    /**
     * Processing form and validate
     *
     * @param Form $form
     *
     * @return bool
     */
    protected function processForm(Form $form): bool
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $handler = $this->get('app.form_handler');

        return $handler->process($request, $form);
    }

    /**
     * Get form errors
     *
     * @param Form $form
     *
     * @return array
     */
    protected function getFormErrors(Form $form): array
    {
        return $this->get('app.form_handler')->getErrors($form);
    }

}
