<?php

namespace fastorm\Entity;

class Repository
{

    protected $em;


    public function __construct(Manager $em)
    {
        $this->em = $em;
    }


    public function getEntityManager()
    {
        return $this->em;
    }


    public function get($primaryKeyValue)
    {
        $className = get_called_class();
        $entityName = str_replace('Repository', '', $className);
        $metadata = $this->em->loadMetadata($entityName);
        $primaryColumn = $metadata->getPrimary()['column'];
        $sql = 'SELECT * FROM ' . $metadata->getTable() . ' WHERE ' . $primaryColumn . ' = :id LIMIT 1';

        return $this->hydrate(
            $this->em->doQuery($className, $sql, array('id' => $primaryKeyValue))
        )->first();
    }


    public function query($queryString, $params = array())
    {
        return $this->em->doQuery(get_called_class(), $queryString, $params);
    }


    public function hydrate($result)
    {
        return new Hydrator($this, $result);
    }
}
