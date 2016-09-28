<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

/**
 * Base rest controller
 */
class Controller extends FOSRestController
{
    use Traits\OrderByQueryTrait;
    use Traits\PartialUpdateTrait;
    use Traits\RestFormTrait;
    use Traits\PageHeaderTrait;

}
