<?php

namespace Fhaculty\Graph\Attribute;

/**
 * An attribute bag that automatically prefixes a given namespace
 */
class AttributeBagNamespaced implements AttributeBag
{
    private $bag;
    private $prefix;

    public function __construct(AttributeAware $bag, $prefix)
    {
        if (!($bag instanceof AttributeBag)) {
            $bag = $bag->getAttributeBag();
        }
        $this->bag = $bag;
        $this->prefix = $prefix;
    }

    public function getAttribute($name)
    {
        return $this->bag->getAttribute($this->prefix . $name);
    }

    public function setAttribute($name, $value)
    {
        $this->bag->setAttribute($this->prefix . $name, $value);
    }

    public function getAttributes()
    {
        $attributes = array();
        $len = strlen($this->prefix);

        foreach ($this->bag->getAttributes() as $name => $value) {
            if (strpos($name, $this->prefix) === 0) {
                $attributes[substr($name, $len)] = $value;
            }
        }

        return $attributes;
    }

    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->bag->setAttribute($this->prefix . $name, $value);
        }
    }

    public function getAttributeBag()
    {
        return $this;
    }
}
