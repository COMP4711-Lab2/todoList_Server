<?php

class Entity extends CI_Model {
// If this class has a setProp method, use it, else modify the property directly
    public function __set($key, $value) {
        // if a set* method exists for this key,
        // use that method to insert this value.
        // For instance, setName(...) will be invoked by $object->name = ...
        // and setLastName(...) for $object->last_name = 
        $method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
        if (method_exists($this, $method))
        {
                $this->$method($value);
                return $this;
        }

        // Otherwise, just set the property value directly.
        $this->$key = $value;
        return $this;
    }
}