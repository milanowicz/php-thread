<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

use InvalidArgumentException;

final class Worker implements WorkerInterface
{
    /** @var null|callable $checkDispatcher */
    private $checkDispatcher = null;
    private string $command = '';
    private int $execCounter = 0;
    private int $execMaxCounter = 50;
    private int $maxWorkers = 2;
    private int $maxExecutionTime = 300;
    private string $memoryLimit = '128M';
    private int $workerDelayStart = 10;
    private int $workerRunSleep = 5;

    public function __construct(
        private array $config = [],
        private ?ThreadInterface $thread = null
    ) {
        foreach ($this->config as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key} = $value;
            } else {
                throw new InvalidArgumentException(
                    'Config for ' . $key . ' not exists!'
                );
            }
        }
        if ($this->thread === null) {
            $this->thread = new Threads();
        }
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getMaxExecutionTime(): int
    {
        return $this->maxExecutionTime;
    }

    public function getMaxWorkers(): int
    {
        return $this->maxWorkers;
    }

    public function getMemoryLimit(): string
    {
        return $this->memoryLimit;
    }

    public function getThread(): ThreadInterface
    {
        return $this->thread;
    }

    public function getWorkerRunSleep(): int
    {
        return $this->workerRunSleep;
    }

    public function getWorkerDelayStart(): int
    {
        return $this->workerDelayStart;
    }

    public function getExecCounter(): int
    {
        return $this->execCounter;
    }

    public function setCheckDispatcher(callable $function): self
    {
        $this->checkDispatcher = $function;
        return $this;
    }

    private function check(): bool
    {
        if (is_callable($this->checkDispatcher)) {
            return (bool) ($this->checkDispatcher)();
        }
        return true;
    }

    public function run(): self
    {
        $this->execCounter = 0;
        ini_set('memory_limit', $this->getMemoryLimit());
        ini_set('max_execution_time', (string) $this->getMaxExecutionTime());
        while ($this->thread->anyRunning() || $this->check()) {
            $runProcesses = count($this->thread->getProcesses());

            if ($runProcesses < $this->getMaxWorkers() && $this->check()) {
                if ($this->execCounter >= $this->execMaxCounter) {
                    break;
                }
                while ($runProcesses < $this->getMaxWorkers() && $this->check()) {
                    $this->thread->exec($this->command);
                    $runProcesses++;
                    $this->execCounter++;
                    sleep($this->getWorkerDelayStart());
                }
            }
            sleep($this->getWorkerRunSleep());
        }
        return $this;
    }
}
