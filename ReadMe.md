# PHP Thread
![Test](https://github.com/milanowicz/php-thread/workflows/Testing/badge.svg?branch=master)
[![Mutation testing](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FMilanowicz%2Fphp-thread%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/Milanowicz/php-thread/master)
[![codecov](https://codecov.io/gh/Milanowicz/php-thread/branch/master/graph/badge.svg?token=42G6ETI9NV)](https://codecov.io/gh/Milanowicz/php-thread)

![](https://img.shields.io/github/v/tag/milanowicz/php-thread)
![](https://img.shields.io/github/repo-size/milanowicz/php-thread)
![](https://img.shields.io/github/languages/code-size/milanowicz/php-thread)

![](https://img.shields.io/packagist/v/milanowicz/php-thread)
![](https://img.shields.io/packagist/dt/milanowicz/php-thread)
![](https://img.shields.io/packagist/dd/milanowicz/php-thread)
![](https://img.shields.io/packagist/dm/milanowicz/php-thread)

Control processes in GNU/Linux systems by this package for PHP 8.x


## Usage

Install it by Composer

    $ composer require milanowicz/php-thread


Class to control thread for one command

    $thread = new Milanowicz\Thread\Threads();
    $thread->exec('php scripts/script1.php');
    $thread->exec('php scripts/script2.php');
    $thread->anyRunning();
    $thread->stopAll();
    $thread->reset();


Worker to run commands

    $worker = new Milanowicz\Thread\Worker([
        'command' => '/bin/sleep 2',
        'execMaxCounter' => 10, // How many would you execute?
        'maxWorkers' => 2, // How process(es) should be started? 
        'maxExecutionTime' => 120, // Seconds to run php script
        'memoryLimit' => '32M', // Maximum for php memory limit
        'workerDelayStart' => 1, // Delay for starting processes after each other
        'workerRunSleep' => 2, // Sleep to check again if enough processes are running
    ]);
    $worker->setCheckDispatcher(function () { return <bool>; });
    $worker->run(); // Main call to start worker loop

    $worker->getThread(); // Get current Thread instance


Singleton to control it everywhere you like

    $singleton = new Milanowicz\Thread\ThreadSingleton();
    $singleton->exec('php scripts/script1.php');
    $singleton->exec('php scripts/script2.php');
    $singleton->anyRunning();
    $singleton->stopAll();
    $singleton->reset

Worker and Singleton

    $worker = new Milanowicz\Thread\Worker([
        'command' => '/bin/sleep 20',
        'execMaxCounter' => 10, // How many would you execute?
        'maxWorkers' => 2, // How process(es) should be started?
    ], new Milanowicz\Thread\ThreadSingleton());
    $worker->run();


## License

[GNU GPL Version 3](http://www.gnu.org/copyleft/gpl.html)
