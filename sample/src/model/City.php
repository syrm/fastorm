<?php

namespace sample\model;

class City
{

    protected $id   = null;
    protected $name = null;
    protected $countryCode = null;
    protected $district = null;
    protected $population = null;


    public function setId($id)
    {
        $this->id = (int) $id;
    }


    public function getId()
    {
        return (int) $this->id;
    }


    public function setName($name)
    {
        $this->name = (string) $name;
    }


    public function getName()
    {
        return (string) $this->name;
    }


    public function setCountryCode($countryCode)
    {
        $this->countryCode = (string) $countryCode;
    }


    public function getCountryCode()
    {
        return (string) $this->countryCode;
    }


    public function setDistrict($district)
    {
        $this->district = (string) $district;
    }


    public function getDistrict()
    {
        return (string) $this->district;
    }


    public function setPopulation($population)
    {
        $this->population = (int) $population;
    }


    public function getPopulation()
    {
        return (int) $this->population;
    }
}
