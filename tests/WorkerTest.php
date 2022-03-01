<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

use InvalidArgumentException;
use TypeError;

final class WorkerTest extends TestCase
{
    public function testConstruct(): void
    {
        $config = [
            'command' => self::COMMAND_3,
            'execMaxCounter' => 10,
            'maxWorkers' => 1,
            'maxExecutionTime' => 120,
            'memoryLimit' => '256M',
            'workerDelayStart' => 21,
            'workerRunSleep' => 12,
        ];
        $worker = new Worker($config);
        $this->assertEquals($config, $worker->getConfig());
        $this->assertEquals(1, $worker->getMaxWorkers());
        $this->assertEquals(120, $worker->getMaxExecutionTime());
        $this->assertEquals('256M', $worker->getMemoryLimit());
        $this->assertEquals(self::COMMAND_3, $worker->getCommand());
        $this->assertEquals(12, $worker->getWorkerRunSleep());
        $this->assertEquals(21, $worker->getWorkerDelayStart());
        $this->assertInstanceOf(ThreadInterface::class, $worker->getThread());
    }

    public function testConstructConfigException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Config for commandasd not exists!');
        new Worker([
            'commandasd' => self::COMMAND_3
        ]);
    }

    public function testConstructConfigError(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign string to property Milanowicz\Thread\Worker::$maxWorkers of type int'
        );
        new Worker([
            'maxWorkers' => '3'
        ]);
    }

    public function testCheck(): void
    {
        $worker = new Worker();
        $this->assertTrue($this->invokeMethod($worker, 'check'));
        $this->assertNull($this->getProperty($worker, 'checkDispatcher'));

        $checked = false;
        $cb = static function () use ($checked) {
            return $checked;
        };
        $worker->setCheckDispatcher($cb);
        $this->assertFalse($this->invokeMethod($worker, 'check'));

        $checked = true;
        $cb = static function () use ($checked) {
            return $checked;
        };
        $worker->setCheckDispatcher($cb);
        $this->assertTrue($this->invokeMethod($worker, 'check'));
    }

    public function testRun(): void
    {
        $config = [
            'command' => self::COMMAND_1,
            'maxWorkers' => 2,
            'maxExecutionTime' => 60,
            'memoryLimit' => '32M',
            'execMaxCounter' => 10,
            'workerDelayStart' => 1,
            'workerRunSleep' => 1,
        ];

        $worker = new Worker($config);
        $cb = static function () use ($worker) {
            return count($worker->getThread()->getHistory()) < 4;
        };
        $worker->setCheckDispatcher($cb);

        $worker->run();
        $this->assertEquals(4, $worker->getExecCounter());
        $this->assertCount(4, $worker->getThread()->getHistory());
        $this->assertCount(0, $worker->getThread()->getProcesses());
    }

    public function testRunAndExitCauseRunTooManyTimes(): void
    {
        $config = [
            'command' => self::COMMAND_1,
            'execMaxCounter' => 10,
            'maxWorkers' => 2,
            'maxExecutionTime' => 60,
            'memoryLimit' => '32M',
            'workerDelayStart' => 1,
            'workerRunSleep' => 1,
        ];

        $worker = new Worker($config);
        $cb = static function () use ($worker) {
            return count($worker->getThread()->getHistory()) < 20;
        };
        $worker->setCheckDispatcher($cb);

        $worker->run();
        $this->assertEquals(10, $worker->getExecCounter());
        $this->assertCount(10, $worker->getThread()->getHistory());
        $this->assertCount(0, $worker->getThread()->getProcesses());
    }
}
