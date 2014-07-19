<?php

define('ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

require_once ROOT . '/SplClassLoader.php';

$classLoader = new \fastorm\SplClassLoader('fastorm', ROOT . DIRECTORY_SEPARATOR . '..');
$classLoader->register();

require_once 'model\City.php';
require_once 'model\CityRepository.php';
require_once 'model\Country.php';
require_once 'model\CountryRepository.php';

$connections = array(
    'main' => array(
        'type'     => 'mysql',
        'host'     => 'localhost',
        'user'     => 'world_sample',
        'password' => 'world_sample'
    ),
);

$em = \fastorm\Entity\Manager::getInstance();
$em->loadConnection($connections);
$em->setModelDirectory(ROOT . '/sample/model');
$em->setCacheDirectory(ROOT . '/sample/tmp');
$em->generateCache();

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

try {
    $countryRepository = $em->getRepository('City');
    $results = $countryRepository->hydrate(
        $countryRepository->query(
            "select * from T_CITY_CIT as c inner join T_COUNTRY_COU as co on (c.cou_code = co.cou_code) limit 3"
        )
    );

    foreach ($results as $result) {
        var_dump($result);
        echo str_repeat("-", 40) . "\n";
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
}
