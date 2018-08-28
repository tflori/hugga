<?php

use Hugga\Console;
use Hugga\Input\Question\Confirmation;

require_once __DIR__ . '/vendor/autoload.php';

// This script will demonstrate the usage of Hugga\Console and allow users to test if this lib fits into their
// environment.

// Initialization
$console = new Console();

// Formatted output
$console->line('${bold;cyan}Formatting');
$console->line('${red}This is a red text');
$console->line('${fg:blue;bg:white}Blue text on white background');
$console->line('${u}Underlined ${cyan} and teal');
$console->line('${bold}Bold${r} and ${underline}underline${reset} can be written out too');
// preformatted:
$console->info('This is an information');
$console->warn('This is a warning');
$console->error('This is an error');

// Color table
$console->line(PHP_EOL . '${bold;cyan}Colors');
$colors = [
    'black', 'dark-grey', 'grey', 'white', 'red', 'light-red', 'green', 'light-green', 'yellow', 'light-yellow', 'blue',
    'light-blue', 'magenta', 'light-magenta', 'cyan', 'light-cyan'
];
$maxLen = max(array_map('strlen', $colors));
foreach ($colors as $fgColor) {
    $console->write(sprintf('${%s}%' . $maxLen . 's: ', $fgColor, $fgColor));
    foreach ($colors as $bgColor) {
        $console->write(sprintf('${fg:%s;bg:%s}  #42  ${r}  ', $fgColor, $bgColor));
    }
    $console->write(PHP_EOL);
}

// Questions
$console->line(PHP_EOL . '${bold;cyan}Questions');
$name = $console->ask('What is your name?', 'John Doe');
$console->info('Hello ' . $name . '!');
if ($console->ask(new Confirmation('Is this correct?'))) {
    $console->info('Great!');
} else {
    $console->warn('Why you are lying to me?');
}

// Manual reading from input
$console->line(PHP_EOL . '${bold;cyan}Input');
$console->info('Write exit to continue; press up to restore previous line');
$line = '';
do {
    if (!empty($line)) {
        $console->warn('processing ' . $line);
    }
    $line = $console->readLine('$ ');
    readline_add_history($line);
} while (strtolower(trim($line)) != 'exit');
$console->read(3, 'Enter 3 letters: ');
$console->write(PHP_EOL);
$console->info('Enter your message (end with dot in line for itself)');
$console->readUntil(PHP_EOL . '.' . PHP_EOL, '');

// Deleting output
$console->line(PHP_EOL . '${bold;cyan}Delete');
$console->write('Importing xml file ... ${yellow}in progress');
sleep(2);
$console->delete('in progress'); // or 11
$console->line('${green}done');

// Progress bar (will be implemented)
$console->line(PHP_EOL . '${bold;cyan}Progress bar');
function getProgressLine($i, $max)
{
    $size = 30;
    $perc = $i / $max;
    $done = floor($perc * $size);
    $line = ' [' . str_repeat('#', $done) . str_repeat('-', $size - $done) . '] ';
    return $line . getProgressText($i, $max);
}

function getProgressText($i, $max)
{
    $perc = round($i / $max * 100, 2);
    $l = strlen($max);
    return sprintf('%\' 6.2f %%  ( %\' ' . $l . 'd / %d )', $perc, $i, $max);
}

$max = mt_rand(9000, 12000);
$s = microtime(true);
for ($i = 0; $i < $max; $i++) {
    usleep(mt_rand(500, 2000));
    if ($i === 0) {
        $console->write(getProgressLine($i, $max));
    } elseif ((microtime(true) - $s) > 0.1) {
        $s = microtime(true);
        $console->deleteLine();
        $console->write(getProgressLine($i, $max));
    }
}
$console->deleteLine();
$console->write(getProgressLine($i, $max) . PHP_EOL);

//$console = new \Hugga\Console();
//
//$question = new \Hugga\Input\Question\Simple('What is your name?', 'World');
//$answer = $question->ask($console);
//$console->info('Hello ' . $answer . '!');
//
//$stream = fopen('php://memory', 'w+');
//fwrite($stream, "\r\n");
//rewind($stream);
//$console->setStdin($stream);
//$question = new \Hugga\Input\Question\Simple\Confirmation('Do you like it?', true);
//var_dump($question->ask($console));

//echo '> ';
////$line = readline(" \e[D") . "\n";
//$line = stream_get_line(STDIN, 4096, PHP_EOL);
//var_dump($line);
//var_dump(fgets(STDIN));

//function rl_callback($ret)
//{
//    global $c, $prompting;
//
//    echo "Sie haben eingegeben: $ret\n";
//    $c++;
//
//    if ($c > 10) {
//        $prompting = false;
//        readline_callback_handler_remove();
//    } else {
//        readline_callback_handler_install("[$c] Geben Sie etwas ein: ", 'rl_callback');
//    }
//}
//
//$c = 1;
//$prompting = true;
//
//readline_callback_handler_install("[$c] Geben Sie etwas ein: ", 'rl_callback');
//
//while ($prompting) {
//    $r = array(STDIN);
//    $n = stream_select($r, $w, $e, null);
//    if ($n && in_array(STDIN, $r)) {
//        // Liest das aktuelle Zeichen und ruft die Callbackfunktion auf, wenn ein
//        // Newline-Zeichen eingegeben wurde
//        readline_callback_read_char();
//        var_dump(readline_info());
//    }
//}
//
//echo "Eingabe deaktiviert. Komplett ausgeführt.\n";

//echo "app> ";
//$previous = '';
//readline_callback_handler_install(" \e[D", function ($str) use (&$previous) {
//    $previous .= $str . PHP_EOL;
//});
//do {
//    $r = array(STDIN);
//    $n = stream_select($r, $w, $e, null);
//    if ($n && in_array(STDIN, $r)) {
//        readline_callback_read_char();
//        $str = $previous . readline_info('line_buffer');
//    }
//} while (mb_strlen($str) < 15);
//readline_callback_handler_remove();
//
//echo PHP_EOL . $str . PHP_EOL;

//declare(ticks=1);
//function signalHandler($signo)
//{
//    echo 'received ' . $signo . PHP_EOL;
//    exit($signo);
//}
//pcntl_signal(SIGINT, 'signalHandler');
//pcntl_signal(SIGTERM, 'signalHandler');

//$listener = new Hugga\InputObserver(STDIN);
//$listener->addHandler(function ($event) {
//    echo implode(' ', array_map('dechex', array_map('ord', str_split($event->char)))) . PHP_EOL;
//});
//$listener->on("\e", function ($event) use ($listener) {
//    $event->stopPropagation = true;
//    $listener->stop();
//});
//sleep(2);
//$listener->start();

//$console = new \Hugga\Console();
//var_dump($console->read(7));
//$subject = (new \Hugga\Input\Question\Simple('Subject:'))->ask($console);
//echo 'Please write your message (end with . in a line itself):' . PHP_EOL;
//$message = $console->readUntil(PHP_EOL . '.' . PHP_EOL);

//var_dump($subject, $message);
