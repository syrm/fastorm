<?php

namespace fastorm\Entity;

class Hydrator implements \Iterator
{

    protected $entityManager = null;
    protected $metadataList = array();
    protected $result = null;
    protected $targets = array();


    public function __construct(Repository $entityRepository, $result)
    {
        $this->entityManager = $entityRepository->getEntityManager();
        $this->result = $result;

        $tableToLoad = array();
        foreach ($this->result->fetchFields() as $index => $field) {
            if (isset($this->targets[$field->table]) === false) {
                $this->targets[$field->table] = array();
            }

            if (in_array($field->orgtable, $tableToLoad) === false) {
                $tableToLoad[] = $field->orgtable;
            }

            $this->targets[$field->table][] = array('name' => $field->orgname, 'table' => $field->orgtable, 'index' => $index);
        }

        foreach ($tableToLoad as $table) {
            if (isset($this->_metadata[$table]) === false) {
                $entityName = $this->entityManager->getClass($table);
                if ($entityName !== null) {
                    $this->metadataList[$table] = $this->entityManager->loadMetadata($entityName);
                }
            }
        }
    }


    public function first()
    {
        $this->valid();
        $objects = $this->current();

        if (count($objects) > 0) {
            return reset($objects);
        }

        return null;
    }


    /** iterator **/

    public function rewind()
    {

        $this->result->dataSeek(0);
        $this->_iteratorPosition = 0;
    }


    public function valid()
    {

        $this->_iteratorPosition++;
        $this->_iteratorCurrent = $this->result->fetchArray();

        if ($this->_iteratorCurrent !== null) {
            return true;
        }

        return false;
    }


    public function key()
    {
        return $this->_iteratorPosition;
    }


    public function current()
    {

        $objects = array();

        foreach ($this->targets as $alias => $columns) {
            foreach ($columns as $column) {
                if (isset($objects[$alias]) === false) {
                    $entityName = $this->entityManager->getClass($column['table']);
                    if ($entityName === null) {
                        continue;
                    }
                    $objects[$alias] = new $entityName();
                }

                $fields = $this->metadataList[$column['table']]->getFields();
                if (isset($fields[$column['name']]['fieldName']) === true) {
                    $propertyName = 'set' . ucfirst($fields[$column['name']]['fieldName']);
                    $objects[$alias]->$propertyName($this->_iteratorCurrent[$column['index']]);
                }
            }
        }

        return $objects;
    }


    public function next()
    {

    }
}
