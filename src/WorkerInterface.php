<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

interface WorkerInterface
{
    /**
     * Get Command for child processes to creating them.
     *
     * @return string
     */
    public function getCommand(): string;

    /**
     * Get Worker config.
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * PHP max execution time for worker process.
     *
     * @return int
     */
    public function getMaxExecutionTime(): int;

    /**
     * How many child processes should be run parallel.
     *
     * @return int
     */
    public function getMaxWorkers(): int;

    /**
     * PHP Memory Limit.
     *
     * @return string
     */
    public function getMemoryLimit(): string;

    /**
     * Get Thread instance object.
     *
     * @return ThreadInterface
     */
    public function getThread(): ThreadInterface;

    /**
     * Sleep time in run method to not run everytime.
     *
     * @return int
     */
    public function getWorkerRunSleep(): int;

    /**
     * Delay time to start process not directly one after the other.
     *
     * @return int
     */
    public function getWorkerDelayStart(): int;

    /**
     * Add callback method for inject it in there and to customize some.
     *
     * @param callable $function
     * @return $this
     */
    public function setCheckDispatcher(callable $function): self;

    /**
     * How many command(s) were executed by run method.
     *
     * @return int
     */
    public function getExecCounter(): int;

    /**
     * Main run for worker process to creating child processes.
     *
     * @return $this
     */
    public function run(): self;
}
