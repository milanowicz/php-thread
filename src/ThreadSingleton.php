<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class ThreadSingleton extends ThreadAbstract
{
    private static array|null $_processes = null;
    private static array|null $_history = null;

    public function __construct()
    {
        if (self::$_processes === null) {
            self::$_processes = [];
            self::$_history = [];
        }
    }

    public function addProcess(int $pid): self
    {
        self::$_processes[] = $pid;
        return $this;
    }

    public function removeKey(int $key): self
    {
        unset(self::$_processes[$key]);
        return $this;
    }

    public function addHistory(int $pid): self
    {
        self::$_history[] = $pid;
        return $this;
    }

    public function getHistory(): array
    {
        return self::$_history ?? [];
    }

    public function getProcesses(): array
    {
        return self::$_processes ?? [];
    }

    public function reset(): self
    {
        if (!$this->anyRunning()) {
            self::$_processes = [];
            self::$_history = [];
        }
        return $this;
    }
}
