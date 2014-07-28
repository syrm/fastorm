<?php

namespace tests\units\fastorm\Entity;

use \mageekguy\atoum;

class Metadata extends atoum
{

    public function testSetterGetterConnection()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->then($object->setConnection('bouhConnection'))
            ->string($object->getConnection())
                ->isEqualTo('bouhConnection');
    }


    public function testSetterGetterDatabase()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->then($object->setDatabase('bouhDatabase'))
            ->string($object->getDatabase())
                ->isEqualTo('bouhDatabase');
    }


    public function testSetterGetterTable()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->then($object->setTable('bouhTable'))
            ->string($object->getTable())
                ->isEqualTo('bouhTable');
    }


    public function testExceptionAddField()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->exception(function () use ($object) {
                $object->addField(array('fieldName' => 'bouh'));
            })
                ->hasDefaultCode()
                ->hasMessage('Field configuration must have fieldName and columnName properties');
    }


    public function testSetterGetterAddField()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->then($object->addField(array('fieldName' => 'bouhField', 'columnName' => 'bouhColumn')))
            ->array($object->getFields())
                ->isEqualTo(array('bouhColumn' => array('fieldName' => 'bouhField', 'columnName' => 'bouhColumn')));
    }


    public function testSetterGetterPrimary()
    {
        $this
            ->if($object = new \fastorm\Entity\Metadata())
            ->then($object->addField(array(
                'id' => true,
                'fieldName' => 'bouhField',
                'columnName' => 'bouhColumn')))
            ->array($object->getPrimary())
                ->isEqualTo(array('field' => 'bouhField', 'column' => 'bouhColumn'));
    }
}
