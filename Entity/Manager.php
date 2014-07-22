<?php

namespace fastorm\Entity;

class Manager
{

    protected static $instance = null;
    protected $connectionConfig = null;
    protected $modelDirectory = null;
    protected $cacheDirectory = null;
    protected $connectionList = array();
    protected $metadataList = array();
    protected $tabletoClass = null;


    protected function __construct(array $config)
    {

        $this->connectionConfig = $config['connections'];
        $this->cacheDirectory = $config['cacheDirectory'];
        $this->modelDirectory = $config['modelDirectory'];

        if (file_exists($this->cacheDirectory . DIRECTORY_SEPARATOR . 'table-to-metadata.php') === false) {
            $fileHandler = fopen($this->cacheDirectory . DIRECTORY_SEPARATOR . 'table-to-metadata.php', 'a+');
            fputs($fileHandler, "<?php\nreturn function (\$table) {\n    \$tables = array();\n");

            foreach (glob($this->modelDirectory . DIRECTORY_SEPARATOR . 'Metadata*.php') as $file) {
                $entityName = str_replace('Metadata', '', basename($file, '.php'));
                $entityMetadata = $this->loadMetadata($entityName);
                fputs($fileHandler, "    \$tables['" . $entityMetadata->getTable() . "'] = '" . $entityName . "';\n");
            }

            fputs(
                $fileHandler,
                "    if (isset(\$tables[\$table]) === false) {\n"
                . "        return null;\n    }\n    return \$tables[\$table];\n};\n"
            );

            fclose($fileHandler);
        }

        $this->tableToClass = require($this->cacheDirectory . DIRECTORY_SEPARATOR . 'table-to-metadata.php');
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
        return $this->tableToClass->__invoke($table);
    }


    public function loadConnection($connections)
    {
        $this->connectionConfig = $connections;
    }


    public function getRepository($entityName)
    {

        $this->loadMetadata($entityName);
        $className = $entityName . 'Repository';

        return new $className($this);
    }


    public function loadMetadata($entityName)
    {

        if (isset($this->metadataList[$entityName]) === true) {
            return $this->metadataList[$entityName];
        }

        $this->metadataList[$entityName] = new Metadata($entityName, $this->modelDirectory);

        return $this->metadataList[$entityName];
    }


    public function doQuery($className, $queryString, $params = array())
    {

        $entityName = str_replace('Repository', '', $className);

        $metadata = $this->loadMetadata($entityName);

        if (isset($this->connectionConfig[$metadata->getConnection()]) === false) {
            throw new \RuntimeException(sprintf('Connection "%s" not found', $metadata->getConnection()));
        }

        $connection = $this->connectionConfig[$metadata->getConnection()];

        if (isset($this->connectionList[$metadata->getConnection()]) === false) {
            switch ($connection['type']) {
                case 'mysql':
                    $databaseHandler = new \fastorm\Adapter\Driver\Mysqli\Mysqli();
                    break;
                case 'postgresql':
                    throw new \RuntimeException('Postgresql handler not found');
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
