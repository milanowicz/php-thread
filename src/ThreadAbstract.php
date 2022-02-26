<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

abstract class ThreadAbstract implements ThreadInterface
{
    public function exec(string $command): int
    {
        exec($command . " > /dev/null 2>&1 & echo $!", $output);
        $pid = (int) $output[0];
        $this->addProcess($pid);
        return $pid;
    }

    public function shell(string $command, int $priority = 0): int
    {
        $pid = (int) shell_exec(
            "nohup "
            . ($priority > 0 ? "nice -n $priority" : '')
            . " $command 2> /dev/null 2>&1 & echo $!"
        );
        $this->addProcess($pid);
        return $pid;
    }

    public function isRunning(int $pid, int $key = -1): bool
    {
        $run = false;
        $key = $this->getKey($pid, $key);
        if ($pid > 0) {
            exec("ps $pid", $processState);
            if (count($processState) > 1) {
                $run = true;
            } elseif (isset($this->getProcesses()[$key])) {
                $this
                    ->addHistory($this->getProcesses()[$key])
                    ->removeKey($key);
            }
        }
        return $run;
    }

    public function anyRunning(): bool
    {
        $run = false;
        foreach ($this->getProcesses() as $key => $pid) {
            if ($this->isRunning($pid, $key)) {
                $run = true;
            }
        }
        return $run;
    }

    public function getHistoryAsString(): string
    {
        if (count($this->getHistory()) > 0) {
            $message = PHP_EOL;
            foreach ($this->getHistory() as $value) {
                $message .= $value . PHP_EOL;
            }
        }
        return $message ?? '';
    }

    public function getStateAsString(): string
    {
        if (count($this->getProcesses()) > 0) {
            $message = PHP_EOL;
            foreach ($this->getProcesses() as $pid) {
                exec("ps $pid", $processState);
                if (count($processState) > 1) {
                    $message .= $pid . " is running" . PHP_EOL;
                } else {
                    $message .= $pid . " is dead" . PHP_EOL;
                }
            }
        } else {
            $message = self::NO_THREAD;
        }
        return $message;
    }

    public function stop(int $pid, int $key = -1): self
    {
        $key = $this->getKey($pid, $key);
        if ($key > 0) {
            exec('ps ' . $pid . ' && kill ' . $pid);
            $this->isRunning($pid);
        }
        return $this;
    }

    public function stopAll(): self
    {
        foreach ($this->getProcesses() as $key => $pid) {
            $this->stop($pid, $key);
        }
        return $this;
    }

    private function getKey(int $pid, int $key): int
    {
        if ($key < 1 || !isset($this->getProcesses()[$key])) {
            $key = (int) array_search($pid, $this->getProcesses(), true);
        }
        return $key;
    }
}
