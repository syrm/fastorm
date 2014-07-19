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


    public function query($queryString, $params = array())
    {
        return $this->em->doQuery(get_called_class(), $queryString, $params);
    }


    public function hydrate($result)
    {
        return new Hydrator($this, $result);
    }
}
