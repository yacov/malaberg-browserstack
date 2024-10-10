<?php

namespace Features\Bootstrap;

class SharedDataContext
{
    private array $data = [];

    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function setMultiple(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function getMultiple(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    public function getAll(): array
    {
        return $this->data;
    }
}