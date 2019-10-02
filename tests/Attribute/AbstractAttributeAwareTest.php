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
    public function testAttributeBag(AttributeAware $entity)
    {
        $bag = $entity->getAttributeBag();
        $this->assertInstanceOf('Graphp\Graph\Attribute\AttributeBag', $bag);
    }
}
