<?php

$metadata->setConnection('main');
$metadata->setDatabase('world');
$metadata->setTable('T_COUNTRY_COU');

$metadata->addField(array(
   'id'         => true,
   'fieldName'  => 'code',
   'columnName' => 'cou_code'
));

$metadata->addField(array(
    'fieldName'  => 'name',
    'columnName' => 'cou_name',
));

$metadata->addField(array(
    'fieldName'  => 'continent',
    'columnName' => 'cou_continent',
));

$metadata->addField(array(
    'fieldName'  => 'region',
    'columnName' => 'cou_region',
));
