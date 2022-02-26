<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class ThreadsTest extends ThreadTestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->thread = new Threads();
    }

    public function testSingleBehaviour(): void
    {
        $t = $this;
        $this->loopingTest(static function () use ($t) {
            $t->thread = new Threads();
            $t->thread->exec(self::COMMAND_1);
            $t->thread->exec(self::COMMAND_2);

            $b = new Threads();
            $b->exec(self::COMMAND_3);
            $b->exec(self::COMMAND_4);

            $t->assertGreaterThanOrEqual(1, $b->getProcesses());
            $t->assertGreaterThanOrEqual(1, $t->thread->getProcesses());
            $running = false;
            while ($t->thread->anyRunning() || $b->anyRunning()) {
                $running = true;
                usleep(300);
            }
            $t->assertTrue($running);
            $t->assertStringContainsString($t->thread::NO_THREAD, $t->thread->getStateAsString());
            $t->assertCount(0, $b->getProcesses());
            $t->assertCount(2, $b->getHistory());
            $t->assertCount(2, $t->thread->getHistory());
        });
    }
}
