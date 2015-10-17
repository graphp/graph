<?php

use Fhaculty\Graph\Attribute\AttributeAware;
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
        $returnValue = $entity->setAttribute('test', 'value');
        $this->assertEquals('value', $entity->getAttribute('test'));
        $this->assertEquals($entity, $returnValue);

        $this->assertEquals(null, $entity->getAttribute('unknown'));

        $this->assertEquals('default', $entity->getAttribute('unknown', 'default'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testAttributeSetRemoveGet(AttributeAware $entity)
    {
        $returnValue = $entity->setAttribute('test', 'value');
        $this->assertEquals('value', $entity->getAttribute('test'));
        $this->assertEquals($entity, $returnValue);

        $returnValue = $entity->removeAttribute('test');
        $this->assertEquals(null, $entity->getAttribute('test'));
        $this->assertEquals($entity, $returnValue);
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
