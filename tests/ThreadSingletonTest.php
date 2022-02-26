<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

final class ThreadSingletonTest extends ThreadTestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->thread = new ThreadSingleton();
    }

    public function testSingleBehaviour(): void
    {
        $t = $this;
        $this->loopingTest(static function () use ($t) {
            $t->thread = new ThreadSingleton();
            $t->thread->reset();
            $t->thread->exec(self::COMMAND_1);
            $t->thread->exec(self::COMMAND_2);

            $b = new ThreadSingleton();
            $b->exec(self::COMMAND_3);
            $b->exec(self::COMMAND_4);

            $running = false;
            while ($t->thread->anyRunning()) {
                $running = true;
                usleep(300);
            }
            $t->assertTrue($running);
            $t->assertStringContainsString($t->thread::NO_THREAD, $t->thread->getStateAsString());
            $t->assertCount(0, $b->getProcesses());
            $t->assertCount(4, $b->getHistory());
            $t->assertCount(4, $t->thread->getHistory());
        });
    }
}
