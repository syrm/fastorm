<?php

namespace sample\model;

class CityRepository extends \fastorm\Entity\Repository
{

    public function getZCountryWithLotsPopulation()
    {

        return $this->hydrate(
            $this->query(
                "select * from T_CITY_CIT as a where cit_name like :name and cit_population > :population limit 3",
                array('name' => 'Z%', 'population' => '200000')
            )
        );

    }


    public static function loadMetadata(\fastorm\Entity\Metadata $metadata)
    {

        $metadata->setConnection('main');
        $metadata->setDatabase('world');
        $metadata->setTable('T_CITY_CIT');

        $metadata->addField(array(
           'id'         => true,
           'fieldName'  => 'id',
           'columnName' => 'cit_id'
        ));

        $metadata->addField(array(
            'fieldName'  => 'name',
            'columnName' => 'cit_name',
        ));

        $metadata->addField(array(
            'fieldName'  => 'countryCode',
            'columnName' => 'cou_code',
        ));

        $metadata->addField(array(
            'fieldName'  => 'district',
            'columnName' => 'cit_district',
        ));

        $metadata->addField(array(
            'fieldName'  => 'population',
            'columnName' => 'cit_population',
        ));

        return $metadata;
    }
}
