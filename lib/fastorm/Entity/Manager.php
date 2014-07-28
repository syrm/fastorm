<?php

namespace fastorm\Entity;

use fastorm\Adapter\Driver\DriverException;
use fastorm\Adapter\Driver\QueryException;
use fastorm\Exception;

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
                throw new Exception('First call to EntityManager must pass configuration in parameters');
            }

            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @param $table
     * @return Model
     */
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

    /**
     * @param $entityName
     * @return Repository
     */
    public function getRepository($entityName)
    {
        $className = $entityName . 'Repository';

        return new $className($this);
    }

    /**
     * @param $repositoryName
     * @return Metadata
     */
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

    /**
     * @param  string                                  $repository
     * @param  string                                  $queryString
     * @param  array                                   $params
     * @return \fastorm\Adapter\Driver\Mysqli\Result
     * @throws \fastorm\Adapter\Driver\DriverException
     */
    public function doQuery($repository, $queryString, $params = array())
    {

        $metadata = $this->loadMetadata($repository);

        if (isset($this->connectionConfig[$metadata->getConnection()]) === false) {
            throw new DriverException(sprintf('Connection "%s" not found', $metadata->getConnection()));
        }

        $databaseHandler = $this->getDatabaseHandler($metadata);
        if ($databaseHandler->getConnected() === false) {
            $this->connectAndSetDatabase($metadata);
        }
        $stmt = $databaseHandler->prepare($queryString);

        if (count($params) > 0) {
            $stmt->bindParams($params);
        }

        if ($databaseHandler->error() !== false) {
            QueryException::throwException($queryString, $databaseHandler);
        }

        $stmt->execute();

        if ($databaseHandler->error() !== false) {
            QueryException::throwException($queryString, $databaseHandler);
        }

        $result = $stmt->getResult();

        return $result;
    }

    public function getDatabaseHandler(Metadata $metadata)
    {
        $connection = $this->connectionConfig[$metadata->getConnection()];

        if (isset($this->connectionList[$metadata->getConnection()]) === false) {
            switch ($connection['type']) {
                case 'mysql':
                    $databaseHandler = new \fastorm\Adapter\Driver\Mysqli\Mysqli();
                    break;
                default:
                    throw new DriverException($connection['type'] . ' handler not found');
                    break;
            }

            $this->connectionList[$metadata->getConnection()] = $databaseHandler;
        } else {
            $databaseHandler = $this->connectionList[$metadata->getConnection()];
        }

        return $databaseHandler;
    }

    /**
     * @param Metadata $metadata
     */
    private function connectAndSetDatabase(Metadata $metadata)
    {
        $connection = $this->connectionConfig[$metadata->getConnection()];
        $databaseHandler = $this->getDatabaseHandler($metadata);
        $databaseHandler->connect(
            $connection['host'],
            $connection['user'],
            $connection['password'],
            $connection['port']
        );
        $databaseHandler->setDatabase($metadata->getDatabase());
    }
}
