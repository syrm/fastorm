<?php

namespace fastorm\Adapter\Driver\Mysqli;

use fastorm\Adapter\Driver\DriverException;
use fastorm\Adapter\Driver\PreparedQueryException;

class Mysqli implements \fastorm\Adapter\Database
{
    /**
     * @var \mysqli $connection
     */
    protected $connection;


    public function connect($hostname, $username, $password, $port)
    {
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $this->connection = new \mysqli($hostname, $username, $password, null, $port);
        }Catch(\mysqli_sql_exception $e){
            throw new DriverException('Connect Error : '.$e->getMessage(), $e->getCode());
        }

        return $this;

    }


    public function setDatabase($database)
    {
        $this->connection->select_db($database);

        if ($this->error() !== false) {
            throw new DriverException('Select database error : ' . $this->error(), $this->connection->errno);
        }
    }


    public function error()
    {

        if ($this->connection->error === '') {
            return false;
        } else {
            return $this->connection->error;
        }

    }


    public function escape($value)
    {
        return $this->connection->real_escape_string($value);
    }


    public function prepare($sql)
    {

        $paramsOrder = array();
        $sql = preg_replace_callback(
            '/:([a-zA-Z0-9_-]+)/',
            function ($match) use (&$paramsOrder) {
                $paramsOrder[$match[1]] = null;
                return '?';
            },
            $sql
        );

        try{
            $mysqliStatement = $this->connection->prepare($sql);
        }catch(\mysqli_sql_exception $e){
            throw new PreparedQueryException($e->getMessage(), $e->getCode());
        }

        $statement = new Statement($mysqliStatement);
        $statement->setParamsOrder($paramsOrder);
        return $statement;
    }


    public function getInsertId()
    {
        return $this->connection->insert_id;
    }


    public function getSqlState()
    {
        return $this->connection->sqlstate;
    }


    public function getErrorNo()
    {
        return $this->connection->errno;
    }


    public function getErrorMessage()
    {
        return $this->connection->error;
    }
}
