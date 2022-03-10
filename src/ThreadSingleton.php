<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class ThreadSingleton extends ThreadAbstract
{
    private static array|null $processes = null;
    private static array|null $history = null;

    public function __construct()
    {
        if (self::$processes === null) {
            self::$processes = [];
            self::$history = [];
        }
    }

    public function addProcess(int $pid): self
    {
        self::$processes[] = $pid;
        return $this;
    }

    public function removeKey(int $key): self
    {
        unset(self::$processes[$key]);
        return $this;
    }

    public function addHistory(int $pid): self
    {
        self::$history[] = $pid;
        return $this;
    }

    public function getHistory(): array
    {
        return self::$history ?? [];
    }

    public function getProcesses(): array
    {
        return self::$processes ?? [];
    }

    public function reset(): self
    {
        if (!$this->anyRunning()) {
            self::$processes = [];
            self::$history = [];
        }
        return $this;
    }
}
