<?php

namespace AppBundle\Manager\Traits;

use DateTime;
use BadMethodCallException;
use Ugosansh\Component\EntityManager\EntityInterface;

trait PartialUpdateTrait
{
    /**
     * @var array
     */
    protected $partialDateFields = [];

    /**
     * Update partial entity
     *
     * @param EntityInterface $entity
     * @param string          $operation
     * @param string          $path
     * @param mixed           $value
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function updatePartial(EntityInterface $entity, string $operation, string $path, $value)
    {
        if ($operation == 'replace') {

            $field    = lcfirst(preg_replace_callback('/_(.?)/', function ($matches) { return strtoupper($matches[1]); }, strtolower($path)));
            $setter   = sprintf('set%s', ucfirst($field));

            if (!method_exists($entity, $setter)) {
                $setter = sprintf('add%s', ucfirst($field));
            }

            if (!method_exists($entity, $setter)) {
                    printf('set%s', ucfirst($field)) ."<br />\n\n<br />";
                    printf('add%s', ucfirst($field));die;
               throw new BadMethodCallException(sprintf(
                    'Attempted to call an undefined method named "%s" or "%s" of class "%s".',
                    sprintf('set%s', ucfirst($field)),
                    sprintf('add%s', ucfirst($field)),
                    get_class($entity)
                ));
            }

            if (array_key_exists($field, $this->partialDateFields)) {
                $value = DateTime::createFromFormat($this->partialDateFields[$field], $value);
            }

            $entity->$setter($value);

            return $entity;
        }

        return false;
    }

}
