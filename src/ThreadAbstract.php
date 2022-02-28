<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

abstract class ThreadAbstract implements ThreadInterface
{
    public function exec(string $command): int
    {
        exec($command . " > /dev/null 2>&1 & echo $!", $output);
        if (isset($output[0])) {
            $pid = (int) $output[0];
            $this->addProcess($pid);
        }
        return $pid ?? 0;
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

    public function isRunning(int $pid): bool
    {
        $run = false;
        if ($pid > 0) {
            exec("ps $pid", $processState);
            $key = $this->getPidKey($pid);
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
        foreach ($this->getProcesses() as $pid) {
            if ($this->isRunning($pid)) {
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

    public function stop(int $pid): self
    {
        $key = $this->getPidKey($pid);
        if ($key > 0) {
            exec('ps ' . $pid . ' && kill -s 9 ' . $pid);
            $this->isRunning($pid);
        }
        return $this;
    }

    public function stopAll(): self
    {
        foreach ($this->getProcesses() as $pid) {
            $this->stop($pid);
        }
        return $this;
    }

    private function getPidKey(int $pid): int
    {
        return (int) array_search($pid, $this->getProcesses(), true);
    }
}
