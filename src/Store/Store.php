<?php
/**
 * Copyright (c) 2020.
 * Adrian Schubek
 * https://adriansoftware.de
 */

namespace adrianschubek\Store;

use ArrayAccess;

class Store implements ArrayAccess
{
    protected static array $mixins;
    protected array $data;

    public function __construct($data = [])
    {
        $this->data = (array)$data;
    }

    public static function mixin(string $name, callable $fun)
    {
        static::$mixins[$name] = $fun;
    }

    public static function fill(int $start, int $end, int $step = 1): Store
    {
        return new static(range($start, $end, $step));
    }

    public function __toString(): string
    {
        return implode(", ", $this->data);
    }

    public function __call($name, $arguments): Store
    {
        $instance = clone $this;
        if (isset(static::$mixins[$name])) {
            (static::$mixins[$name])($instance, ...$arguments);
        }
        return $instance;
    }

    public function only($keys): Store
    {
        return $this->include(fn($val) => in_array($val, $keys));
    }

    public function include(callable $callback): Store
    {
        return $this->filter($callback);
    }

    public function filter(callable $callback): Store
    {
        $data = array_values(array_filter($this->data, $callback));

        return new static($data);
    }

    public function run(callable $callback): Store
    {
        $callback();
        return $this;
    }

    public function sort(): Store
    {
        $c = clone $this;
        return new static(sort($c->data));
    }

    public function exclude(callable $callback): Store
    {
        return $this->filter(fn($val) => !$callback($val));
    }

    public function toUpper(): Store
    {
        return $this->each(fn($val) => mb_strtoupper($val));
    }

    public function each(callable $callback): Store
    {
        $keys = array_keys($this->data);
        $values = array_map($callback, array_values($this->data));

        return new static(array_combine($keys, $values));
    }

    public function map(callable $callback): Store
    {
        $i = clone $this;
        foreach ($i->data as $key => &$val) {
            $callback($key, $val);
        }
        return $i;
    }

    public function mapAssoc(callable $callback): Store
    {
        $result = [];

        foreach ($this->data as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return new static($result);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function add(Store $store): Store
    {
        return new static(array_merge($this->data, $store->data));
    }

    public function strip(): Store
    {
        $data = array_values(array_filter($this->data));

        return new static($data);
    }

    public function toJson($options = null, int $depth = 512): string
    {
        return json_encode($this->data, $options, $depth);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}