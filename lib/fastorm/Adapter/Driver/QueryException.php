<?php

namespace fastorm\Adapter\Driver;

use fastorm\Adapter\Database;

class QueryException extends DriverException{
    public static function throwException($queryString, Database $databaseHandler){
        throw new self($databaseHandler->error().' (the query was : '.$queryString.')', $databaseHandler->getErrorNo());
    }
} 