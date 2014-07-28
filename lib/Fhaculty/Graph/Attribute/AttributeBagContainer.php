<?php

namespace Fhaculty\Graph\Attribute;

class AttributeBagContainer implements AttributeBag
{
    private $attributes = array();

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
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

    public function getAttributeBag()
    {
        return $this;
    }
}
