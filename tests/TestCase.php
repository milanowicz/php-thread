<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Throwable;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    final public function accessMethod(
        object $class,
        string $method
    ): ReflectionMethod {
        $reflection = new ReflectionMethod($class, $method);
        $reflection->setAccessible(true);
        return $reflection;
    }

    final public function invokeMethod(
        object $class,
        string $method,
        mixed ...$arguments
    ): mixed {
        return $this
            ->accessMethod($class, $method)
            ->invoke($class, ...$arguments);
    }

    final public function setProperty(
        object $class,
        string $property,
        mixed $value = null
    ): ReflectionProperty {
        $method = new ReflectionProperty($class, $property);
        $method->setAccessible(true);
        if ($value !== null) {
            $method->setValue($class, $value);
        }
        return $method;
    }

    /**
     * @throws RuntimeException
     */
    final public function getProperty(
        object $class,
        string $property
    ): mixed {
        $reflectionClass = new ReflectionClass($class);
        do {
            if ($reflectionClass->hasProperty($property)) {
                break;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        if ($reflectionClass !== false) {
            $reflectionProperty = $reflectionClass->getProperty($property);
            $reflectionProperty->setAccessible(true);
            return $reflectionProperty->getValue($class);
        }
        throw new RuntimeException('Could not find property ' . $property);
    }

    /**
     * @throws Throwable
     */
    final public function tryTest(
        callable $func,
        int $tries = 3
    ): void {
        while ($tries > 0) {
            try {
                $func();
                $tries = 0;
            } catch (Throwable $t) {
                $tries--;
                if ($tries < 1) {
                    throw $t;
                }
            }
        }
    }

    /**
     * @throws Throwable
     */
    final public function loopingTest(
        callable $func,
        int $tries = 5
    ): void {
        $error = $tries;
        while ($tries > 0) {
            try {
                $func();
                $tries--;
            } catch (Throwable $t) {
                $error--;
                if ($error < 1) {
                    throw $t;
                }
            }
        }
    }
}
