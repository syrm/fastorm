<?php

namespace fastorm;

class UnitOfWork
{

    const STATE_MANAGED = 1;

    protected $entityManager = null;
    public $identityMap = array();


    public function __construct(Entity\Manager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function addToIdentityMap($entity)
    {
        $metadata = $this->entityManager->loadMetadata(get_class($entity));

        if (isset($this->identityMap[$metadata->getRootEntityName()]) === false) {
            $this->identityMap[$metadata->getRootEntityName()] = array();
        }

        $primary = $metadata->getPrimary();
        $method = 'get' . ucfirst($primary['field']);
        $this->identityMap[$metadata->getRootEntityName()][$entity->$method()] = $entity;
    }


    public function getEntityFromIdentityMap($class, $id)
    {
        if (isset($this->identityMap[$class][$id]) === true) {
            return $this->identityMap[$class][$id];
        }

        return null;
    }
}
