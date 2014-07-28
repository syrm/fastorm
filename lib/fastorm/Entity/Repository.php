<?php

namespace fastorm\Entity;

use fastorm\Adapter\DatabaseResult;

class Repository
{
    /**
     * @var Manager
     */
    protected $em;

    public function __construct(Manager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param $primaryKeyValue
     * @return mixed|null
     */
    public function get($primaryKeyValue)
    {

        $className = get_class($this);
        $entityName = str_replace('Repository', '', $className);
        $metadata = $this->em->loadMetadata($entityName);
        $primaryColumn = $metadata->getPrimary()['column'];
        $fields = $metadata->getFieldsName();
        $databaseHandler = $this->em->getDatabaseHandler($metadata);
        $fields = array_map(array($databaseHandler, 'protectFieldName'), $fields);

        $sql = 'SELECT '.$databaseHandler->protectFieldName($primaryColumn).', '.
            implode(', ', $fields).' FROM ' . $metadata->getTable() .
            ' WHERE ' . $databaseHandler->protectFieldName($primaryColumn) . ' = :id LIMIT 1';

        return $this->hydrate(
            $this->em->doQuery($className, $sql, array('id' => $primaryKeyValue))
        )->first();
    }

    /**
     * @param  string $queryString
     * @param  array  $params
     * @return \fastorm\Adapter\DatabaseResult
     */
    public function query($queryString, $params = array())
    {
        return $this->em->doQuery(get_class($this), $queryString, $params);
    }

    /**
     * @param  \fastorm\Adapter\DatabaseResult $result
     * @return Hydrator
     */
    public function hydrate(DatabaseResult $result)
    {
        return new Hydrator($this, $result);
    }
}
