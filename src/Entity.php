<?php

namespace Graphp\Graph;

/**
 * The abstract `Entity` class is the base for `Graph`, `Vertex` and `Edge`.
 *
 * It contains common methods to access additional attributes.
 * Each attribute consists of a name (string) and an arbitrary value.
 */
abstract class Entity
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * get a single attribute with the given $name (or return $default if attribute was not found)
     *
     * @param  string $name
     * @param  mixed  $default to return if attribute was not found
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    /**
     * set a single attribute with the given $name to given $value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this chainable
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Removes a single attribute with the given $name
     *
     * @param  string $name
     * @return $this chainable
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }

    /**
     * get an array of all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * set an array of additional attributes
     *
     * @param  array $attributes
     * @return $this chainable
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes + $this->attributes;

        return $this;
    }
}
