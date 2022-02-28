<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

interface ThreadInterface
{
    public const NO_THREAD = 'No Process is running';

    /**
     * Add Pid to array.
     *
     * @param int $pid
     * @return $this
     */
    public function addProcess(int $pid): self;

    /**
     * Add Pid to history array.
     *
     * @param int $pid
     * @return $this
     */
    public function addHistory(int $pid): self;

    /**
     * Remove Key from Pid in array.
     *
     * @param int $key
     * @return $this
     */
    public function removeKey(int $key): self;

    /**
     * Execute a command by PHP 'exec'.
     *
     * @param string $command
     * @return int
     */
    public function exec(string $command): int;

    /**
     * Execute a command by PHP 'shell_exec'.
     *
     * @param string $command
     * @param int $priority
     * @return int
     */
    public function shell(string $command, int $priority = 0): int;

    /**
     * Is process running or not?
     *
     * @param int $pid
     * @return bool
     */
    public function isRunning(int $pid): bool;

    /**
     * Is any process still running?
     *
     * @return bool
     */
    public function anyRunning(): bool;

    /**
     * Get histories process Pid(s)
     *
     * @return array
     */
    public function getHistory(): array;

    /**
     * Get histories process Pid(s) as string
     *
     * @return string
     */
    public function getHistoryAsString(): string;

    /**
     * Get all process by a string
     *
     * @return string
     */
    public function getStateAsString(): string;

    /**
     * Get all process Pid(s)
     *
     * @return array
     */
    public function getProcesses(): array;

    /**
     * Reset all Pid(s), if no process is still running
     *
     * @return $this
     */
    public function reset(): self;

    /**
     * Stop a process by Pid.
     *
     * @param int $pid
     * @return $this
     */
    public function stop(int $pid): self;

    /**
     * Stop all running processes
     *
     * @return $this
     */
    public function stopAll(): self;
}
