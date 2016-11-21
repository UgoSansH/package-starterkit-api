<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Unavailability parameter Manager
 */
class UnavailabilityManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}