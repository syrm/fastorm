<?php

namespace fastorm\Adapter;

interface Database
{

    public function connect($hostname, $username, $password, $port);
    public function setDatabase($database);
    public function prepare($sql);
    public function escape($value);
    public function error();
    public function getInsertId();
    public function getSqlState();
    public function getErrorNo();
    public function getErrorMessage();
    public function protectFieldName($fieldName);
}
