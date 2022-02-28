# PHP Thread
![Test](https://github.com/Milanowicz/php-thread/workflows/Testing/badge.svg?branch=master)
[![Mutation testing](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FMilanowicz%2Fphp-thread%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/Milanowicz/php-thread/master)
[![codecov](https://codecov.io/gh/Milanowicz/php-thread/branch/master/graph/badge.svg?token=42G6ETI9NV)](https://codecov.io/gh/Milanowicz/php-thread)

![](https://img.shields.io/github/repo-size/milanowicz/php-thread)
![](https://img.shields.io/github/languages/code-size/milanowicz/php-thread)

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


Singleton to control it everywhere you like

    $singleton = new Milanowicz\Thread\ThreadSingleton();
    $singleton->exec('php scripts/script1.php');
    $singleton->exec('php scripts/script2.php');
    $singleton->anyRunning();
    $singleton->stopAll();
    $singleton->reset



## License

[GNU GPL Version 3](http://www.gnu.org/copyleft/gpl.html)
