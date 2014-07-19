<?php

namespace fastorm\Entity;

class Metadata
{

    protected $connectionName = null;
    protected $databaseName = null;
    protected $table = null;
    protected $fields = array();


    public function __construct($entityName, $directory)
    {
        $metadata = $this;
        require_once $directory . '/Metadata' . $entityName . '.php';
    }


    public function setConnection($connectionName)
    {
        $this->connectionName = $connectionName;
    }


    public function getConnection()
    {
        return $this->connectionName;
    }


    public function setDatabase($databaseName)
    {
        $this->databaseName = $databaseName;
    }


    public function getDatabase()
    {
        return $this->databaseName;
    }


    public function setTable($tableName)
    {
        $this->table = $tableName;
    }


    public function getTable()
    {
        return $this->table;
    }


    public function addField(array $params)
    {
        if (isset($params['fieldName']) === false || isset($params['columnName']) === false) {
            throw new \RuntimeException('Field configuration must have fieldName and columnName properties');
        }

        $this->fields[$params['columnName']] = $params;
    }


    public function getFields()
    {
        return $this->fields;
    }
}
