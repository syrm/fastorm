<?php

$this->setConnection('main');
$this->setDatabase('world');
$this->setTable('T_CITY_CIT');

$this->addField(array(
   'id'         => true,
   'fieldName'  => 'id',
   'columnName' => 'cit_id'
));

$this->addField(array(
    'fieldName'  => 'name',
    'columnName' => 'cit_name',
));

$this->addField(array(
    'fieldName'  => 'countryCode',
    'columnName' => 'cou_code',
));

$this->addField(array(
    'fieldName'  => 'district',
    'columnName' => 'cit_district',
));

$this->addField(array(
    'fieldName'  => 'population',
    'columnName' => 'cit_population',
));
