<?php

namespace fastorm\Adapter\Driver\Mysqli;

class Statement implements \fastorm\Adapter\DatabaseStatement
{

    protected $statement;
    protected $paramsOrder;

    public function __construct(\mysqli_stmt $statement)
    {

        $this->statement = $statement;

    }


    public function setParamsOrder(array $params)
    {
        $this->paramsOrder = $params;
    }


    public function bindParams(array $params)
    {

        $values = array();
        foreach (array_keys($this->paramsOrder) as $key) {
            $values[] = &$params[$key];
        }

        array_unshift($values, str_repeat('s', count($this->paramsOrder)));
        call_user_func_array(array($this->statement, 'bind_param'), $values);
    }


    public function execute()
    {
        return $this->statement->execute();
    }


    public function getAffectedRows()
    {
        return $this->statement->affected_rows;
    }


    public function getResult()
    {

        $result = $this->statement->get_result();
        if ($result === false) {
            return null;
        }

        return new Result($result);
    }


    public function close()
    {
        $this->statement->close();
    }
}
