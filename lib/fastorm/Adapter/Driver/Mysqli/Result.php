<?php

namespace fastorm\Adapter\Driver\Mysqli;

class Result implements \fastorm\Adapter\DatabaseResult
{

    protected $result = null;

    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    public function dataSeek($offset)
    {
        return $this->result->data_seek($offset);

    }

    public function fetchArray()
    {
        return $this->result->fetch_array(MYSQLI_NUM);

    }

    public function fetchFields()
    {

        $fields = $this->result->fetch_fields();

        if ($fields === false) {
            return null;
        }

        return $fields;

    }
}
