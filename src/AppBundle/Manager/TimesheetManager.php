<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Timesheet Manager
 */
class TimesheetManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}