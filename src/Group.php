<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Attribute\AttributeBagReference;
use Fhaculty\Graph\Exception\InvalidArgumentException;

class Group implements AttributeAware
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var Graph
     */
    private $graph;

    private $attributes = array();

    public function __construct(Graph $graph, $id)
    {
        if (!is_int($id) && !is_string($id)) {
            throw new InvalidArgumentException('Group ID has to be of type integer or string');
        }

        $this->id = $id;
        $this->graph = $graph;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVerticesInGroup()
    {
        $thisGroup = $this;

        return array_values(array_filter(
            $this->graph->getVertices()->getMap(),
            function (Vertex $vertex) use ($thisGroup) {
                return $vertex->getGroup() instanceof Group && $vertex->getGroup()->equals($thisGroup);
            }
        ));
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    public function getAttributeBag()
    {
        return new AttributeBagReference($this->attributes);
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    public function equals(Group $other)
    {
        return $this->id === $other->id;
    }
}
