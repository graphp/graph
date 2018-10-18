<?php

namespace Fhaculty\Graph\Tests\Attribute;

use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Tests\TestCase;

abstract class AbstractAttributeAwareTest extends TestCase
{
    abstract protected function createAttributeAware();

    public function testAttributeAwareInterface()
    {
        $entity = $this->createAttributeAware();
        $this->assertInstanceOf('Fhaculty\Graph\Attribute\AttributeAware', $entity);

        return $entity;
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testAttributeSetGetDefault(AttributeAware $entity)
    {
        $entity->setAttribute('test', 'value');
        $this->assertEquals('value', $entity->getAttribute('test'));

        $this->assertEquals(null, $entity->getAttribute('unknown'));

        $this->assertEquals('default', $entity->getAttribute('unknown', 'default'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testAttributeSetRemoveGet(AttributeAware $entity)
    {
        $entity->setAttribute('test', 'value');
        $this->assertEquals('value', $entity->getAttribute('test'));

        $entity->removeAttribute('test');
        $this->assertEquals(null, $entity->getAttribute('test'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testAttributeBag(AttributeAware $entity)
    {
        $bag = $entity->getAttributeBag();
        $this->assertInstanceOf('Fhaculty\Graph\Attribute\AttributeBag', $bag);
    }
}
