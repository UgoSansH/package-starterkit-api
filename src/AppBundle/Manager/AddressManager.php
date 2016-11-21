<?php

namespace AppBundle\Manager;

use Ugosansh\Component\EntityManager\Manager;

/**
 * AddressManager Manager
 */
class AddressManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

}