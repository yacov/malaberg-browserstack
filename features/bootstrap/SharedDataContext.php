<?php

namespace Features\Bootstrap;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

class SharedDataContext implements Context
{
    private static $instance = null;
    private $data = [];

    public function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

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

    public function cleanup(): void
    {
        $this->data = [];
    }
}
