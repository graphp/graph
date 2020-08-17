<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Entity;

abstract class EntityTest extends TestCase
{
    abstract protected function createEntity();

    public function testImplementsEntity()
    {
        $entity = $this->createEntity();
        $this->assertInstanceOf('Graphp\Graph\Entity', $entity);

        return $entity;
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testSetAttributeReturnsSelf(Entity $entity)
    {
        $this->assertSame($entity, $entity->setAttribute('test', 'value'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testSetAttributesReturnsSelf(Entity $entity)
    {
        $this->assertSame($entity, $entity->setAttributes(array()));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testSetAttributesAddsNewAttributes(Entity $entity)
    {
        $entity->setAttributes(array('hello' => 'wörld'));

        $this->assertEquals('wörld', $entity->getAttribute('hello'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testSetAttributesOverwritesExistingAttributes(Entity $entity)
    {
        $entity->setAttributes(array('hello' => 'na'));
        $entity->setAttributes(array('hello' => 'wörld'));

        $this->assertEquals('wörld', $entity->getAttribute('hello'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testGetAttributeReturnsValueIfItExists(Entity $entity)
    {
        $entity->setAttribute('test', 'value');

        $this->assertEquals('value', $entity->getAttribute('test'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testGetAttributeReturnsNullIfItDoesNotExist(Entity $entity)
    {
        $this->assertEquals(null, $entity->getAttribute('unknown'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testGetAttributeReturnsExpicitDefaultIfItDoesNotExist(Entity $entity)
    {
        $this->assertEquals('default', $entity->getAttribute('unknown', 'default'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testRemoveAttributeReturnsSelfIfItDoesNotExist(Entity $entity)
    {
        $this->assertSame($entity, $entity->removeAttribute('unknown'));
    }

    /**
     * @depends testImplementsEntity
     * @param Entity $entity
     */
    public function testRemoveAttributeReturnsSelfAndGetAttributeThenReturnsNull(Entity $entity)
    {
        $entity->setAttribute('test', 'value');

        $this->assertSame($entity, $entity->removeAttribute('test'));

        $this->assertEquals(null, $entity->getAttribute('test'));
    }
}
