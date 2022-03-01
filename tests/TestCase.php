<?php

declare(strict_types=1);

namespace Milanowicz\Thread;

abstract class TestCase extends \Milanowicz\Testing\TestCase
{
    public const COMMAND = '/bin/sleep';
    public const COMMAND_1 = self::COMMAND . ' 1';
    public const COMMAND_2 = self::COMMAND . ' 2';
    public const COMMAND_3 = self::COMMAND . ' 3';
}
