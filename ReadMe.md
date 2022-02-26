# PHP Thread

Control processes in GNU/Linux systems by this package.


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
