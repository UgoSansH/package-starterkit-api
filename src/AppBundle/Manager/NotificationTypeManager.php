<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Notification type Manager
 */
class NotificationTypeManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}