<?php

namespace Fhaculty\Graph\Attribute;

class AttributeBagReference implements AttributeBag
{
    private $attributes;

    public function __construct(array &$attributes)
    {
        $this->attributes =& $attributes;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes + $this->attributes;

        return $this;
    }

    public function getNames()
    {
        return array_keys($this->attributes);
    }

    public function getAttributeBag()
    {
        return $this;
    }
}
