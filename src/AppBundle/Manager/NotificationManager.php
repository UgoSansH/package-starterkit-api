<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Notification Manager
 */
class NotificationManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}