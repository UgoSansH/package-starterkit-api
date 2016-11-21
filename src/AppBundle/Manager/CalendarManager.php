<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Calendar Manager
 */
class CalendarManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}