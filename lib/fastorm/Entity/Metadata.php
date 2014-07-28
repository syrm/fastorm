<?php

namespace fastorm\Entity;

use fastorm\Exception;

class Metadata
{

    protected $connectionName = null;
    protected $databaseName = null;
    protected $table = null;
    protected $fields = array();
    protected $primary = array();

    public function setConnection($connectionName)
    {
        $this->connectionName = (string) $connectionName;
    }

    public function getConnection()
    {
        return (string) $this->connectionName;
    }

    public function setDatabase($databaseName)
    {
        $this->databaseName = (string) $databaseName;
    }

    public function getDatabase()
    {
        return (string) $this->databaseName;
    }

    public function setTable($tableName)
    {
        $this->table = (string) $tableName;
    }

    public function getTable()
    {
        return (string) $this->table;
    }

    public function addField(array $params)
    {
        if (isset($params['fieldName']) === false || isset($params['columnName']) === false) {
            throw new Exception('Field configuration must have fieldName and columnName properties');
        }

        $this->fields[$params['columnName']] = $params;

        if (isset($params['id']) === true && $params['id'] === true) {
            $this->primary = array('field' => $params['fieldName'], 'column' => $params['columnName']);
        }
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
