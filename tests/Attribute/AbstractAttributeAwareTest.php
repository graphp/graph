<?php

namespace Graphp\Graph\Tests\Attribute;

use Graphp\Graph\Attribute\AttributeAware;
use Graphp\Graph\Tests\TestCase;

abstract class AbstractAttributeAwareTest extends TestCase
{
    abstract protected function createAttributeAware();

    public function testAttributeAwareInterface()
    {
        $entity = $this->createAttributeAware();
        $this->assertInstanceOf('Graphp\Graph\Attribute\AttributeAware', $entity);

        return $entity;
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testSetAttributeReturnsSelf(AttributeAware $entity)
    {
        $this->assertSame($entity, $entity->setAttribute('test', 'value'));

        return $entity;
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testGetAttributeReturnsValueIfItExists(AttributeAware $entity)
    {
        $entity->setAttribute('test', 'value');

        $this->assertEquals('value', $entity->getAttribute('test'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testGetAttributeReturnsNullIfItDoesNotExist(AttributeAware $entity)
    {
        $this->assertEquals(null, $entity->getAttribute('unknown'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testGetAttributeReturnsExpicitDefaultIfItDoesNotExist(AttributeAware $entity)
    {
        $this->assertEquals('default', $entity->getAttribute('unknown', 'default'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testRemoveAttributeReturnsSelfIfItDoesNotExist(AttributeAware $entity)
    {
        $this->assertSame($entity, $entity->removeAttribute('unknown'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testRemoveAttributeReturnsSelfAndGetAttributeThenReturnsNull(AttributeAware $entity)
    {
        $entity->setAttribute('test', 'value');

        $this->assertSame($entity, $entity->removeAttribute('test'));

        $this->assertEquals(null, $entity->getAttribute('test'));
    }

    /**
     * @depends testAttributeAwareInterface
     * @param AttributeAware $entity
     */
    public function testAttributeBag(AttributeAware $entity)
    {
        $bag = $entity->getAttributeBag();
        $this->assertInstanceOf('Graphp\Graph\Attribute\AttributeBag', $bag);
    }
}
