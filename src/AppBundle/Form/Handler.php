<?php

namespace AppBundle\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

/**
 * Form Handler
 */
class Handler
{
    /**
     * handle and validate
     *
     * @return boolean
     */
    public function validate(Request $request, Form $form): bool
    {
        if ($request->getMethod() != 'POST') {
            $form->submit($request->request->all());
        } else {
            $form->handleRequest($request);
        }

        return $form->isValid();
    }

    /**
     * form process
     *
     * @return boolean
     */
    public function process(Request $request, Form $form): bool
    {
        return $this->validate($request, $form);
    }

    /**
     * Get array of form errors
     *
     * @param Form $form
     *
     * @return array
     */
    public function getErrors(Form $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrors($child);
            }
        }

        return $errors;
    }

}
