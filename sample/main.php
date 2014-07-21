<?php

require_once __DIR__ . '/../Autoloader.php';

require_once 'model/City.php';
require_once 'model/CityRepository.php';
require_once 'model/Country.php';
require_once 'model/CountryRepository.php';

$connections = array(
    'main' => array(
        'type'     => 'mysql',
        'host'     => 'localhost',
        'user'     => 'world_sample',
        'password' => 'world_sample'
    ),
);

$em = \fastorm\Entity\Manager::getInstance(array(
    'connections' => $connections,
    'modelDirectory' =>  __DIR__ . '/model',
    'cacheDirectory' =>  __DIR__ . '/tmp'
));

try {
    $cityRepository = $em->getRepository('City');
    $results = $cityRepository->getZCountryWithLotsPopulation();

    foreach ($results as $result) {
        var_dump($result);
        echo str_repeat("-", 40) . "\n";
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
}

echo str_repeat("-", 40) . "\n";

try {
    $countryRepository = $em->getRepository('Country');
    $results = $countryRepository->hydrate($countryRepository->query("select * from T_COUNTRY_COU as b limit 3"));

    foreach ($results as $result) {
        var_dump($result);
        echo str_repeat("-", 40) . "\n";
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
}

echo str_repeat("-", 40) . "\n";
$em2 = \fastorm\Entity\Manager::getInstance();

try {
    $cityRepository = $em2->getRepository('City');
    $results = $cityRepository->hydrate(
        $cityRepository->query(
            "select * from T_CITY_CIT as c inner join T_COUNTRY_COU as co on (c.cou_code = co.cou_code) where co.cou_code = :code limit 3",
            array('code' => 'FRA')
        )
    );

    foreach ($results as $result) {
        var_dump($result);
        echo str_repeat("-", 40) . "\n";
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
}

echo str_repeat("-", 40) . "\n";
$countryRepository = $em2->getRepository('Country');
var_dump($countryRepository->get('FRA'));
