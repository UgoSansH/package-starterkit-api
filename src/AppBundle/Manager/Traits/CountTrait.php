<?php

namespace AppBundle\Manager\Traits;


trait CountTrait
{
    public function count(array $criteria = []): int
    {
        return $this->adapter->getRepository($this->getEntityClass())->count($criteria);
    }

}
