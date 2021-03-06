<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class ThreadsTest extends ThreadAbstractTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->thread = new Threads();
    }

    public function testSingleBehaviour(): void
    {
        $this->thread = new Threads();
        $pid1 = $this->thread->exec(self::COMMAND_1);
        $pid2 = $this->thread->exec(self::COMMAND_1);
        $this->assertContains($pid1, $this->thread->getProcesses());
        $this->assertContains($pid2, $this->thread->getProcesses());
        $running = false;
        while ($this->thread->anyRunning()) {
            $running = true;
            usleep(300);
        }
        $this->assertTrue($running);

        $b = new Threads();
        $b->exec(self::COMMAND_1);
        $b->exec(self::COMMAND_1);
        $this->assertGreaterThanOrEqual(1, $b->getProcesses());
        $running = false;
        while ($b->anyRunning()) {
            $running = true;
            usleep(300);
        }
        $this->assertTrue($running);
        $this->assertStringContainsString($this->thread::NO_THREAD, $this->thread->getStateAsString());
        $this->assertCount(0, $b->getProcesses());
        $this->assertCount(2, $b->getHistory());
        $this->assertCount(0, $this->thread->getProcesses());
        $this->assertCount(2, $this->thread->getHistory());
    }
}
