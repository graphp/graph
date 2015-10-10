<?php

namespace Fhaculty\Graph\Attribute;

/**
 * Implemented by any entity that is aware of additional attributes
 *
 * Each attribute consists of a name (string) and an arbitrary value.
 */
interface AttributeAware
{
    /**
     * get a single attribute with the given $name (or return $default if attribute was not found)
     *
     * @param string $name
     * @param mixed  $default to return if attribute was not found
     * @return mixed
     */
    public function getAttribute($name, $default = null);

    /**
     * set a single attribute with the given $name to given $value
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($name, $value);

    /**
     * Removes a single attribute with the given $name
     *
     * @param string $name
     */
    public function removeAttribute($name);

    /**
     * get a container for all attributes
     *
     * @return AttributeBag
     */
    public function getAttributeBag();
}
