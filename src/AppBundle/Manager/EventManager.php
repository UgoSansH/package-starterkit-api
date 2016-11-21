<?php

namespace AppBundle\Manager;

use DateTime;
use Ugosansh\Component\EntityManager\Manager;
use Ugosansh\Component\EntityManager\ResourceAdapterInterface;

/**
 * Event Manager
 */
class EventManager extends Manager
{
    use Traits\CountTrait;
    use Traits\PartialUpdateTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(ResourceAdapterInterface $adapter, $entityClass)
    {
        $this->partialDateFields['dateStart'] = DateTime::ISO8601;
        $this->partialDateFields['dateEnd']   = DateTime::ISO8601;

        parent::__construct($adapter, $entityClass);
    }

}
