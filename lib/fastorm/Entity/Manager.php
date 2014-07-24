<?php

namespace fastorm\Entity;

class Manager
{

    protected static $instance = null;
    protected $connectionConfig = null;
    protected $connectionList = array();
    protected $metadataList = array();
    protected $tableToClass = array();


    protected function __construct(array $config)
    {
        $this->connectionConfig = $config['connections'];
    }


    public static function getInstance(array $config = array())
    {

        if (self::$instance === null) {
            if (count($config) === 0) {
                throw new \Exception('First call to EntityManager must pass configuration in parameters');
            }

            self::$instance = new self($config);
        }

        return self::$instance;
    }


    public function getClass($table)
    {

        if (isset($this->tableToClass[$table]) === false) {
            return null;
        }

        return $this->tableToClass[$table];
    }


    public function loadConnection($connections)
    {
        $this->connectionConfig = $connections;
    }


    public function getRepository($entityName)
    {
        $className = $entityName . 'Repository';
        return new $className($this);
    }


    public function loadMetadata($repositoryName)
    {

        $entityName = str_replace('Repository', '', $repositoryName);
        if (isset($this->metadataList[$entityName]) === true) {
            return $this->metadataList[$entityName];
        }

        $this->metadataList[$entityName] = $repositoryName::loadMetadata(new Metadata());
        $this->tableToClass[$this->metadataList[$entityName]->getTable()] = $entityName;

        return $this->metadataList[$entityName];
    }


    public function doQuery($repository, $queryString, $params = array())
    {

        $metadata = $this->loadMetadata($repository);

        if (isset($this->connectionConfig[$metadata->getConnection()]) === false) {
            throw new \RuntimeException(sprintf('Connection "%s" not found', $metadata->getConnection()));
        }

        $connection = $this->connectionConfig[$metadata->getConnection()];

        if (isset($this->connectionList[$metadata->getConnection()]) === false) {
            switch ($connection['type']) {
                case 'mysql':
                    $databaseHandler = new \fastorm\Adapter\Driver\Mysqli\Mysqli();
                    break;
                default:
                    throw new \RuntimeException($connection['type'] . ' handler not found');
                    break;
            }

            $this->connectionList[$metadata->getConnection()] = $databaseHandler;
            $databaseHandler->connect(
                $connection['host'],
                $connection['user'],
                $connection['password'],
                $connection['port']
            );
            $databaseHandler->setDatabase($metadata->getDatabase());
        } else {
            $databaseHandler = $this->connectionList[$metadata->getConnection()];
        }

        $stmt = $databaseHandler->prepare($queryString);

        if ($databaseHandler->error() !== false) {
            throw new \Exception($databaseHandler->error());
        }

        if (count($params) > 0) {
            $stmt->bindParams($params);
        }

        if ($databaseHandler->error() !== false) {
            throw new \Exception($databaseHandler->error());
        }

        $stmt->execute();

        if ($databaseHandler->error() !== false) {
            throw new \Exception($databaseHandler->error());
        }

        $result = $stmt->getResult();

        return $result;
    }
}
