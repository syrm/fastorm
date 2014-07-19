<?php

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
}
