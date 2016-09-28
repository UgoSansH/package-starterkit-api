<?php

namespace AppBundle\Controller\Traits;

use BadMethodCallException;
use AppBundle\Manager\Manager;
use Ugosansh\Component\EntityManager\EntityInterface;
use Ugosansh\Component\EntityManager\ManagerInterface;

trait PartialUpdateTrait
{
    protected function partialUpdate(EntityInterface $entity, ManagerInterface $manager, $forward = null)
    {
        $request   = $this->get('request_stack')->getMasterRequest();
        $operation = $request->get('op');
        $path      = $request->get('path');
        $value     = $request->get('value');

        if ($operation !== 'replace') {
            return $this->view(sprintf('Invalid PATCH operation "%s"', $operation));
        }

        try {
            if (!$manager->updatePartial($entity, $operation, $path, $value)) {
                return $this->view('Invalid field value', 422);
            }
        } catch (BadMethodCallException $e) {
            return $this->view(sprintf('Unreconized field "%s"', $path), 422);
        }

        $manager->save($entity);

        if ($forward) {
            return $this->forward($forward, ['id' => $entity->getId()]);
        }
    }

}