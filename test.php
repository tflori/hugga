<?php

use Hugga\Console;

require_once __DIR__ . '/vendor/autoload.php';

// This script will demonstrate the usage of Hugga\Console and allow users to test if this lib fits into their
// environment.

// Initialization
$console = new Console();

// Formatted output
$console->line('${red}This is a red text');
$console->line('${fg:blue;bg:white}Blue text on white background');
$console->line('${u}Underlined ${cyan} and teal');
$console->line('${bold}Bold${r} and ${underline}underline${reset} can be written out too');

// Color table
$colors = [
    'black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'grey', 'light-red', 'light-green', 'light-yellow',
    'light-blue', 'light-magenta', 'light-cyan', 'white'
];
$maxLen = max(array_map('strlen', $colors));
foreach ($colors as $fgColor) {
    $console->write(sprintf('${%s}%' . $maxLen . 's: ', $fgColor, $fgColor));
    foreach ($colors as $bgColor) {
        $console->write(sprintf('${fg:%s;bg:%s}  %s  ${r}  ', $fgColor, $bgColor, $bgColor));
    }
    $console->write(PHP_EOL);
}

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

//$input = new \Hugga\Input\TtyHandler(STDIN);
//do {
//    $c = $input->readMultibyte(false);
//    echo implode(' ', array_map('dechex', array_map('ord', str_split($c)))) . PHP_EOL;
////    if (ord($c) === 127) {
////        echo "\e[D \e[D";
////    } else {
////        echo $c;
////    }
//} while ($c != '.');

//$input = new \Hugga\Input\ReadlineHandler(STDIN);
//echo $input->readLine('$ ') . PHP_EOL;
//echo $input->read(5, '$ ') . PHP_EOL;
//echo $input->readUntil(PHP_EOL . '.' . PHP_EOL) . PHP_EOL;

//function getProgressLine($i, $max)
//{
//    $size = 30;
//    $perc = $i / $max;
//    $done = floor($perc * $size);
//    $line = ' [' . str_repeat('#', $done) . str_repeat('-', $size - $done) . '] ';
//    return $line . getProgressText($i, $max);
//}
//
//function getProgressText($i, $max)
//{
//    $perc = round($i / $max * 100, 2);
//    $l = strlen($max);
//    return sprintf('%\' 6.2f %%  ( %\' ' . $l . 'd / %d )', $perc, $i, $max);
//}
//
//
//$max = mt_rand(9000, 12000);
//$th = new Hugga\Output\TtyHandler(STDOUT);
//$s = microtime(true);
//for ($i = 0; $i < $max; $i++) {
//    usleep(1000);
//    if ($i === 0) {
//        $th->write(getProgressLine($i, $max));
//    } elseif ((microtime(true) - $s) > 0.1) {
//        $s = microtime(true);
//        $th->deleteLine();
//        $th->write(getProgressLine($i, $max));
////    } else {
////        $progressText = getProgressText($i, $max);
////        $th->delete(strlen($progressText));
////        $th->write($progressText);
//    }
//}
//$th->deleteLine();
//$th->write(getProgressLine($i, $max) . PHP_EOL);

//function stty($options) {
//    exec($cmd = "/bin/stty $options", $output, $el);
//    $el && die("exec($cmd) failed");
//    return implode(" ", $output);
//}
//
//function getchar($echo = false) {
//    $echo = $echo ? "" : "-echo";
//
//    # Get original settings
//    $stty_settings = preg_replace("#.*; ?#s", "", stty("--all"));
//
//    # Set new ones
//    stty("cbreak $echo");
//
//    # Get characters until a PERIOD is typed,
//    # showing their hexidecimal ordinal values.
//    printf("> ");
//    do {
//        $c = '';
//        do {
//            $c .= fgetc(STDIN);
//            list($input, $output, $error) = [[STDIN], [], []];
//        } while (stream_select($input, $output, $error, 0));
//
//        if (strlen($c) === 1 && ord($c) < 32) {
//            // stop here ?
//            if (ord($c) === 10) {
//                printf("\n> ");
//            } elseif (ord($c) === 27) {
//                break;
//            } else {
//                echo ord($c) . ' ';
//            }
//        } elseif (strlen($c) > 1 && ord($c[0]) < 32) {
//            // what to do ?
//            array_map(function ($c) {
//                echo dechex(ord($c)) . ' ';
//            }, str_split($c));
//        } else {
//            echo '<' . $c . '> ';
//        }
//    } while (true);
//
//    # Return settings
//    stty($stty_settings);
//}
//
//getchar();
