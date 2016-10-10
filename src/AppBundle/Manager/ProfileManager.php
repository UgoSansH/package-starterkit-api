<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * Profile Manager
 */
class ProfileManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}