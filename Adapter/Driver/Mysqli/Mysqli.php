<?php

namespace fastorm\Adapter\Driver\Mysqli;

class Mysqli implements \fastorm\Adapter\Database
{

    protected $connection;


    public function connect($hostname, $username, $password, $port)
    {

        $this->connection = new \mysqli($hostname, $username, $password, null, $port);

        if (mysqli_connect_error()) {
            throw new \Exception('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
        }

        return $this;

    }


    public function setDatabase($database)
    {
        $this->connection->select_db($database);

        if ($this->error() !== false) {
            throw new \Exception('Select database error : ' . $this->error());
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

        $mysqliStatement = $this->connection->prepare($sql);

        if ($mysqliStatement === false) {
            return false;
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
