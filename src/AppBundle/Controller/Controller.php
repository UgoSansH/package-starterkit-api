<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Context\Context;

/**
 * Base rest controller
 */
class Controller extends FOSRestController
{
    use Traits\OrderByQueryTrait;
    use Traits\PartialUpdateTrait;
    use Traits\RestFormTrait;
    use Traits\PageHeaderTrait;

    /**
     * Format and create response error
     *
     * @param array $errors
     *
     * @return Response
     */
    protected function createErrorResponse(array $errors, $statusCode = 422)
    {
        return $this->view(['errors' => $errors], $statusCode);
    }

    /**
     * Override view method in order to set serialization groups easily
     *
     * @param null  $data
     * @param null  $statusCode
     * @param array $headers
     * @param array $groups
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function view($data = null, $statusCode = null, array $headers = [], array $groups = ['Default'])
    {
        $context = new Context();
        $context->setGroups($groups);

        $view = parent::view($data, $statusCode, $headers);
        $view->setContext($context);

        return $view;
    }

}
