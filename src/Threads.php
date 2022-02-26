<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class Threads extends ThreadAbstract
{
    public function __construct(
        private array $processes = [],
        private array $history = []
    ) {
    }

    public function addProcess(int $pid): self
    {
        $this->processes[] = $pid;
        return $this;
    }

    public function removeKey(int $key): self
    {
        unset($this->processes[$key]);
        return $this;
    }

    public function addHistory(int $pid): self
    {
        $this->history[] = $pid;
        return $this;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function getProcesses(): array
    {
        return $this->processes;
    }

    public function reset(): self
    {
        if (!$this->anyRunning()) {
            $this->processes = [];
            $this->history = [];
        }
        return $this;
    }
}
