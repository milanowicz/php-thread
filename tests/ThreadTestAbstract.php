<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

abstract class ThreadTestAbstract extends TestCase
{
    public const COMMAND = '/bin/sleep';
    public const COMMAND_1 = self::COMMAND . ' 1';
    public const COMMAND_2 = self::COMMAND . ' 2';
    protected ?ThreadInterface $thread = null;

    public function tearDown(): void
    {
        parent::tearDown();
        $this->thread
            ->stopAll()
            ->reset();
        $this->thread = null;
        ob_flush();
        flush();
    }

    public function testStartAndStop(): void
    {
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_1);
        $this->assertCount(4, $this->thread->getProcesses());
        $this->assertInstanceOf(
            ThreadInterface::class,
            $this->thread->stopAll()
        );
        sleep(1);
        $this->assertFalse($this->thread->anyRunning());
        $this->assertCount(4, $this->thread->getHistory());
        $this->assertCount(0, $this->thread->getProcesses());
    }

    public function testStartAndStopSingle(): void
    {
        $pid1 = $this->thread->exec(self::COMMAND_1);
        $pid2 = $this->thread->exec(self::COMMAND_1);
        $pid3 = $this->thread->exec(self::COMMAND_1);
        $pid4 = $this->thread->exec(self::COMMAND_1);
        $this->thread->stop($pid1);
        $this->assertGreaterThanOrEqual(3, $this->thread->getProcesses());
        $this->assertGreaterThanOrEqual(1, $this->thread->getHistory());
        $this->thread->reset();

        $this->thread->stop($pid2);
        $this->assertGreaterThanOrEqual(2, $this->thread->getProcesses());
        $this->assertGreaterThanOrEqual(2, $this->thread->getHistory());

        $this->thread->stop($pid3);
        $this->assertGreaterThanOrEqual(1, $this->thread->getProcesses());
        $this->assertGreaterThanOrEqual(3, $this->thread->getHistory());

        $this->thread->stop($pid4);
        sleep(1);
        $this->assertFalse($this->thread->anyRunning());
        $this->assertCount(4, $this->thread->getHistory());
        $this->assertCount(0, $this->thread->getProcesses());
    }

    public function testShell(): void
    {
        $this->thread->shell(self::COMMAND_1);
        $this->thread->shell(self::COMMAND_1, 2);
        $this->thread->shell(self::COMMAND_1, 40);

        $this->assertCount(3, $this->thread->getProcesses());
        $running = false;
        while ($this->thread->anyRunning()) {
            $running = true;
            usleep(300);
        }
        $this->assertFalse($running);
        $this->assertStringContainsString($this->thread::NO_THREAD, $this->thread->getStateAsString());
        $this->assertCount(0, $this->thread->getProcesses());
        $this->assertCount(3, $this->thread->getHistory());
    }

    public function testGetStateAsString(): void
    {
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_2);
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_2);

        $this->assertGreaterThanOrEqual(3, $this->thread->getProcesses());
        $this->assertMatchesRegularExpression('/\d+ is running\s/', $this->thread->getStateAsString());
        $this->assertDoesNotMatchRegularExpression('/\d+ is dead\s/', $this->thread->getStateAsString());
        while ($this->thread->anyRunning()) {
            $this->assertCount(
                abs(count($this->thread->getProcesses()) - 4),
                $this->thread->getHistory()
            );
            sleep(1);
            $this->assertMatchesRegularExpression('/\d+ is dead\s/', $this->thread->getStateAsString());
        }
        $this->assertNotEmpty($this->thread->getHistoryAsString());
        $this->assertMatchesRegularExpression('/\s.*\d+\s/', $this->thread->getHistoryAsString());
        $this->assertStringContainsString($this->thread::NO_THREAD, $this->thread->getStateAsString());
        $this->assertCount(0, $this->thread->getProcesses());
        $this->assertCount(4, $this->thread->getHistory());
    }

    public function testGetStateAsStringNothingRuns(): void
    {
        $running = false;
        $this->assertStringContainsString($this->thread::NO_THREAD, $this->thread->getStateAsString());
        while ($this->thread->anyRunning()) {
            $running = true;
            usleep(300);
        }
        $this->assertFalse($running);
        $this->assertCount(0, $this->thread->getProcesses());
        $this->assertEquals('', $this->thread->getHistoryAsString());
        $this->assertCount(0, $this->thread->getHistory());
    }

    public function testIsRunning(): void
    {
        $this->assertTrue($this->thread->isRunning(1, -1));
        $this->assertFalse($this->thread->isRunning(0, -1));
        $this->assertFalse($this->thread->isRunning(-1, -1));

        $this->thread->addProcess(0);
        $this->assertFalse($this->thread->isRunning(0, 0));
        $this->assertFalse($this->thread->isRunning(0, -1));
        $this->assertCount(0, $this->thread->getHistory());
    }

    public function testGeyKey(): void
    {
        $pid = $this->thread->exec(self::COMMAND_1);
        $pid2 = $this->thread->exec(self::COMMAND_1);
        $this->assertEquals([$pid, $pid2], $this->thread->getProcesses());

        $this->assertEquals(
            0,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                $pid,
                -1
            )
        );

        $this->assertEquals(
            1,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                $pid2,
                -1
            )
        );

        $this->assertEquals(
            0,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                $pid,
                3
            )
        );

        $this->assertEquals(
            1,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                $pid2,
                2
            )
        );

        $this->assertEquals(
            1,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                $pid2,
                1
            )
        );

        $this->assertEquals(
            0,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                -1,
                0
            )
        );

        $this->assertEquals(
            0,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                0,
                -1
            )
        );

        $this->assertEquals(
            0,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                0,
                -1
            )
        );

        $this->assertEquals(
            1,
            $this->invokeMethod(
                $this->thread,
                'getKey',
                1,
                1
            )
        );
    }
}
