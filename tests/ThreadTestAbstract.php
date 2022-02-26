<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

abstract class ThreadTestAbstract extends TestCase
{
    public const COMMAND_1 = 'php scripts/script1.php';
    public const COMMAND_2 = 'php scripts/script2.php';
    public const COMMAND_3 = 'php scripts/script3.php';
    public const COMMAND_4 = 'php scripts/script4.php';
    protected ?ThreadInterface $thread = null;

    public function tearDown(): void
    {
        parent::tearDown();
        $this->thread->reset();
        $this->thread = null;
        ob_flush();
        flush();
    }

    public function testStartAndStop(): void
    {
        $t = $this;
        $this->tryTest(static function () use ($t) {
            $t->thread->reset();
            $t->thread->exec(self::COMMAND_1);
            $t->thread->exec(self::COMMAND_2);
            $t->thread->exec(self::COMMAND_3);
            $t->thread->exec(self::COMMAND_4);
            $t->assertGreaterThanOrEqual(3, $t->thread->getProcesses());
            $t->assertInstanceOf(
                ThreadInterface::class,
                $t->thread->stopAll()
            );
            $t->assertInstanceOf(
                ThreadInterface::class,
                $t->thread->stop(-1)
            );
            $t->assertFalse($t->thread->anyRunning());
            $t->assertCount(4, $t->thread->getHistory());
            $t->assertCount(0, $t->thread->getProcesses());
        });
    }

    public function testStartAndStopSingle(): void
    {
        $t = $this;
        $this->tryTest(static function () use ($t) {
            $t->thread->reset();
            $pid1 = $t->thread->exec(self::COMMAND_1);
            $pid2 = $t->thread->exec(self::COMMAND_2);
            $pid3 = $t->thread->exec(self::COMMAND_3);
            $pid4 = $t->thread->exec(self::COMMAND_4);
            $t->thread->stop($pid1);
            $t->assertGreaterThanOrEqual(3, $t->thread->getProcesses());
            $t->assertGreaterThanOrEqual(1, $t->thread->getHistory());
            $t->thread->reset();

            $t->thread->stop($pid2);
            $t->assertGreaterThanOrEqual(2, $t->thread->getProcesses());
            $t->assertGreaterThanOrEqual(2, $t->thread->getHistory());

            $t->thread->stop($pid3);
            $t->assertGreaterThanOrEqual(1, $t->thread->getProcesses());
            $t->assertGreaterThanOrEqual(3, $t->thread->getHistory());

            $t->thread->stop($pid4);
            $t->assertFalse($t->thread->anyRunning());
            $t->assertCount(4, $t->thread->getHistory());
            $t->assertCount(0, $t->thread->getProcesses());
        });
    }

    public function testShell(): void
    {
        $this->thread->reset();
        $this->thread->shell(self::COMMAND_1);
        $this->thread->shell(self::COMMAND_2, 2);
        $this->thread->shell(self::COMMAND_3, 40);

        $this->assertCount(3, $this->thread->getProcesses());
        $running = false;
        while ($this->thread->anyRunning()) {
            $this->assertCount(
                abs(count($this->thread->getProcesses()) - 3),
                $this->thread->getHistory()
            );
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
        $this->thread->exec(self::COMMAND_1);
        $this->thread->exec(self::COMMAND_2);
        $this->thread->exec(self::COMMAND_3);

        $this->assertGreaterThanOrEqual(3, $this->thread->getProcesses());
        $this->assertMatchesRegularExpression('/\d+ is running\s/', $this->thread->getStateAsString());
        $this->assertMatchesRegularExpression('/\d+ is dead\s/', $this->thread->getStateAsString());
        while ($this->thread->anyRunning()) {
            $this->assertCount(
                abs(count($this->thread->getProcesses()) - 4),
                $this->thread->getHistory()
            );
            usleep(300);
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
        $pid2 = $this->thread->exec(self::COMMAND_2);
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
