<?php

use Hugga\Console;
use Hugga\Input\Question\Choice;
use Hugga\Input\Question\Confirmation;
use Hugga\Output\Drawing\ProgressBar;
use Hugga\Output\Drawing\Table;

require_once __DIR__ . '/vendor/autoload.php';

// This script will demonstrate the usage of Hugga\Console and allow users to test if this lib fits into their
// environment.

/**********
 * BASICS *
 **********/

// Initialization
$console = new Console();
//$console->setVerbosity(Console::WEIGHT_HIGH);
//$console->nonInteractive();

// Formatted output
//$console->line('${bold;cyan}Formatting');
//$console->line('${red}This is a red text');
//$console->line('${fg:blue;bg:white}Blue text on white background');
//$console->line('${u}Underlined ${cyan} and teal');
//$console->line('${bold}Bold${r} and ${underline}underline${reset} can be written out too');
//// preformatted:
//$console->info('This is an information');
//$console->warn('This is a warning');
//$console->error('This is an error');
//
//// Color table
//$console->line(PHP_EOL . '${bold;cyan}Colors');
//$colors = [
//    'black', 'dark-grey', 'grey', 'white', 'red', 'light-red', 'green', 'light-green', 'yellow', 'light-yellow', 'blue',
//    'light-blue', 'magenta', 'light-magenta', 'cyan', 'light-cyan'
//];
//$maxLen = max(array_map('strlen', $colors));
//foreach ($colors as $fgColor) {
//    $console->write(sprintf('${%s}%' . $maxLen . 's: ', $fgColor, $fgColor));
//    foreach ($colors as $bgColor) {
//        $console->write(sprintf('${fg:%s;bg:%s}  #42  ${r}  ', $fgColor, $bgColor));
//    }
//    $console->write(PHP_EOL);
//}
//
///*********
// * Input *
// *********/
//
//// Questions
//$console->line(PHP_EOL . '${bold;cyan}Questions');
//$name = $console->ask('What is your name?', 'John Doe');
//$console->info('Hello ' . $name . '!');
//if ($console->ask(new Confirmation('Is this correct?'))) {
//    $console->info('Great!');
//} else {
//    $console->warn('Why you are lying to me?');
//}
//
//// Manual reading from input
//$console->line(PHP_EOL . '${bold;cyan}Input');
//$console->info('Write exit to continue; press up to restore previous line');
//$line = '';
//do {
//    if (!empty($line)) {
//        $console->warn('processing ' . $line);
//    }
//    $line = $console->readLine('$ ');
//    readline_add_history($line);
//} while (strtolower(trim($line)) != 'exit');
//readline_clear_history();
//$console->write('Enter 3 letters:');
//$input = $console->read(3);
//$console->line(sprintf('You entered: "%s"', $input));
//$console->info('Enter your message (end with dot in line for itself)');
//$message = $console->readUntil(PHP_EOL . '.' . PHP_EOL, '');
//$console->line(sprintf('Message:' . PHP_EOL . '"""%s"""', $message));
//
//// Choices
//$console->line(PHP_EOL . '${bold;cyan}Choices');
//$names = [
//    'ezra' => 'Ezra Trickett', 'leticia' => 'Leticia Karpinski', 'celinda' => 'Celinda Baskett',
//    'jerlene' => 'Jerlene Esteban', 'merideth' => 'Merideth Utsey', 'jame' => 'Jame Depaolo',
//    'shirlene' => 'Shirlene Fraire', 'carmon' => 'Carmon Frese', 'dion' => 'Dion Rundell',
//    'elouise' => 'Elouise Mcgovern', 'leslee' => 'Leslee Rispoli', 'inell' => 'Inell Feinstein',
//    'burton' => 'Burton Lamontagne', 'machelle' => 'Machelle Wattley', 'thomas' => 'Thomas Franklin',
//    'maynard' => 'Maynard Gabourel', 'beverley' => 'Beverley Eisenbarth', 'van' => 'Van Meeks',
//    'maren' => 'Maren Wildermuth', 'shoshana' => 'Shoshana Harry', 'prince' => 'Prince Calbert',
//    'jackeline' => 'Jackeline Livermore', 'eufemia' => 'Eufemia Loux', 'almeda' => 'Almeda Bjornson',
//    'mignon' => 'Mignon Zollars', 'reyes' => 'Reyes Nodine', 'pinkie' => 'Pinkie Hedman',
//    'pablo' => 'Pablo Moyer', 'yuette' => 'Yuette Venezia', 'mitch' => 'Mitch Helwig',
//];
//// without changing options
//$chosen = $console->ask(new Choice(
//    array_values($names),
//    'Choose your character:',
//    'Van Meeks'
//));
//$console->line('You have chosen: ${green}' . $chosen);
//// show only 10 (only if your term is interactive) and return values
//$chosen = $console->ask(
//    (new Choice($names))
//        ->limit(10)
//        ->returnValue()
//);
//$console->line('You have chosen: ${green}' . $chosen);
//// show only 10 (only if your term is interactive) and return keys (by default)
//$chosen = $console->ask(
//    (new Choice($names))
//        ->limit(10)
//);
//$console->line('You have chosen: ${green}' . $chosen);
//// non interactive (write your answer) and return key
//$chosen = $console->ask(
//    (new Choice(array_values($names), '', 23))
//        ->nonInteractive()
//        ->returnKey()
//);
//$console->line('You have chosen: ${green}' . $chosen . ' (' . array_values($names)[$chosen] . ')');
//
///*******************
// * Advanced Output *
// *******************/
//
//// Deleting output
//$console->line(PHP_EOL . '${bold;cyan}Delete');
//$console->write('Importing xml file ... ${yellow}in progress');
//sleep(2);
//$console->delete('in progress'); // or ->delete(11)
//$console->line('${green}done');
//
//// ProgressBar bar
//$console->line(PHP_EOL . '${bold;cyan}ProgressBar bar');
//
//// simple progress bar
//$progress = new ProgressBar($console, 80);
//$progress->width(10)->start();
//for ($i = 0; $i < 80; $i++) {
//    usleep(40000);
//    $progress->advance();
//}
//$progress->finish();
//
//// concurrent progress bars
//$packages = ['openssh', 'gimp', 'libreoffce', 'linux', 'firefox', 'inkscape', 'conky', 'gnome'];
//$downloads = [];
//$console->info(sprintf('Start downloading updates for %d packages', count($packages)));
//foreach ($packages as $package) {
//    $kb = mt_rand(1000, 3000);
//    $packageDownload = [
//        'package' => $package,
//        'kb' => $kb,
//        'progress' => new ProgressBar($console, $kb, 'Downloading ' . $package, 'kb'),
//        'loaded' => 0,
//    ];
//    $downloads[] = $packageDownload;
//    $packageDownload['progress']->updateRate(0.1)->start();
//}
//$progressDownloads = new ProgressBar($console, count($packages), 'Downloaded', 'updates');
//$progressDownloads->start();
//while (!empty($downloads)) {
//    usleep(mt_rand(6000, 20000));
//    foreach ($downloads as $i => &$packageDownload) {
//        $loaded = mt_rand(1, 10);
//        $packageDownload['progress']->advance($loaded);
//        $packageDownload['loaded'] += $loaded;
//        if ($packageDownload['loaded'] >= $packageDownload['kb']) {
//            $packageDownload['progress']->finish();
//            $console->info('Downloaded ' . $packageDownload['package']);
//            array_splice($downloads, $i, 1);
//            $progressDownloads->advance();
//        }
//    }
//}
//$progressDownloads->finish();
//
//// undetermined progress bars
//$console->info('Installing updates...');
//$progressInstall = new ProgressBar($console, null, 'Updating packages');
//$progressInstall->start();
//foreach ($packages as $package) {
//    $installTime = mt_rand(500, 1500) / 1000;
//    $start = microtime(true);
//    while (microtime(true) - $start < $installTime) {
//        usleep(40000);
//        $progressInstall->advance();
//    }
//    $console->info('Updated ' . $package);
//}
//$progressInstall->template('{title} ${green}done')->finish();

// Tables
$console->line(PHP_EOL . '${bold;cyan}Tables');

// Simple table
$table = new Table($console, [
    ['23', 'John Doe', 'john.doe', 'john.doe@example.com'],
    ['42', 'Marvin', 'marvin', 'marvin@example.com'],
    ['5552342', 'Arthur Dent', 'adent', 'arthur@example.com', 'extra column'],
    ['213', 'Röhrich', 'roerich', 'roerich@example.com'],
], ['External ID', 'Name', 'User', 'E-Mail']);
$console->line($table->getText());
$console->line($table->column(4, ['delete' => true])->getText());
$console->line($table->withBorderRows()->getText());
$console->line($table->repeatHeaders(3)->getText());
$console->line($table->headerStyle('${b;red}')->getText());
$console->line($table->padding(3)->getText());
$console->line($table->padding(1)->withoutBorderRows()->getText());
$console->line($table->repeatHeaders(4)->getText());
$console->line($table->column(0, ['header' => 'ID', 'align' => 'center'])->getText());
$console->line($table->withoutBorder()->getText());



//$observer = $console->getInputObserver();
//$observer->on("\e", function ($event) use ($observer) {
//    $event->stopPropagation = true;
//    $observer->stop();
//});
//$observer->addHandler(function ($event) {
//    echo implode(" ", array_map('dechex', array_map('ord', str_split($event->char)))) . PHP_EOL;
//});
//$observer->start();

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

//$listener = new Hugga\Input\Observer(STDIN);
//$buffer = '';
//$listener->addHandler(function ($event) use (&$buffer, $console, $listener) {
//    if (ord($event->char[0]) >= 32 || $event->char === "\n") {
//        $console->write($event->char);
//        $buffer .= $event->char;
//        if (mb_strlen($buffer) >= 5) {
//            $listener->stop();
//            $console->write(PHP_EOL);
//        }
//    }
////    echo implode(' ', array_map('dechex', array_map('ord', str_split($event->char)))) . PHP_EOL;
//});
//$listener->on("\x7f", function ($event) use (&$buffer, $console) {
//    $event->stopPropagation = true;
//    if (strlen($buffer) && mb_substr($buffer, -1) != "\n") {
//        $console->delete(1);
//        $buffer = mb_substr($buffer, 0, -1);
//    }
//});
//$listener->on("\e", function ($event) use ($listener) {
//    $event->stopPropagation = true;
//    $listener->stop();
//});
//$listener->start();
//$input = $buffer;
//var_dump($buffer);

//$console = new \Hugga\Console();
//var_dump($console->read(7));
//$subject = (new \Hugga\Input\Question\Simple('Subject:'))->ask($console);
//echo 'Please write your message (end with . in a line itself):' . PHP_EOL;
//$message = $console->readUntil(PHP_EOL . '.' . PHP_EOL);

//var_dump($subject, $message);
