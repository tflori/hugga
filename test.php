<?php

use Hugga\Console;
use Hugga\Input\Question\Choice;
use Hugga\Input\Question\Confirmation;
use Hugga\Output\Drawing\ProgressBar;
use Hugga\Output\Drawing\Table;

// find the next autoload..
$dir = __DIR__;
while ($dir !== '/') {
    if (file_exists($dir . '/vendor/autoload.php')) {
        $loader = require_once $dir . '/vendor/autoload.php';
        break;
    }
    $dir = dirname($dir);
}
if (!isset($loader)) {
    require_once __DIR__ . '/vendor/autoload.php'; // we fail here
}

// This script will demonstrate the usage of Hugga\Console and allow users to test if this lib fits into their
// environment.

/**********
 * BASICS *
 **********/

// Initialization
$console = new Console();
//$console->setVerbosity(Console::WEIGHT_HIGH);
//$console->nonInteractive();

function drawLogo(Console $console)
{
    $logo = [
        ['default',       ' ,dPYb,                                                    '],
        ['light-magenta', ' IP\'`Yb                                                    '],
        ['magenta',       ' I8  8I                                                    '],
        ['blue',          ' I8  8\'                                                    '],
        ['blue',          ' I8 dPgg,   gg      gg    ,gggg,gg    ,gggg,gg    ,gggg,gg '],
        ['cyan',          ' I8dP" "8I  I8      8I   dP"  "Y8I   dP"  "Y8I   dP"  "Y8I '],
        ['cyan',          ' I8P    I8  I8,    ,8I  i8\'    ,8I  i8\'    ,8I  i8\'    ,8I '],
        ['yellow',        ',d8     I8,,d8b,  ,d8b,,d8,   ,d8I ,d8,   ,d8I ,d8,   ,d8b,'],
        ['yellow',        '88P     `Y88P\'"Y88P"`Y8P"Y8888P"888P"Y8888P"888P"Y8888P"`Y8'],
        ['green',         '                              ,d8I\'       ,d8I\'            '],
        ['green',         '                            ,dP\'8I      ,dP\'8I             '],
        ['red',           '                           ,8"  8I     ,8"  8I             '],
        ['red',           '                           I8   8I     I8   8I             '],
        ['light-red',     '                           `8, ,8I     `8, ,8I             '],
        ['default',       '                            `Y8P"       `Y8P"              '],
    ];

    $console->line('');
    foreach ($logo as $i => $row) {
        $console->line('   ${' . $row[0] . '}' . $row[1] . '   ');
    }
    $console->line('');
};

function askNext(Console $console, $default = 'exit')
{
    $console->line('');
    return $console->ask(new Choice(
        [
            'exit' => 'Quit demo',
            'formatting' => 'Formatting texts',
            'colors' => 'Foreground and background colors',
            'input' => 'Basic input and reading stdin',
            'choices' => 'Selection of predefined values',
            'advanced' => 'Advanced output methods',
            'progress' => 'Progress bars',
            'tables' => 'Table formatting',
            'observer' => 'Observe keyboard input',
        ],
        'Which demo you want to see next?',
        $default
    ));
}

function formattingDemo(Console $console)
{
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
}

function colorsDemo(Console $console)
{
    // Color table
    $console->line(PHP_EOL . '${bold;cyan}Colors');
    $colors = [
        39 => 'default', 30 => 'black', 90 => 'dark-grey', 37 => 'grey', 97 => 'white', 31 => 'red', 91 => 'light-red',
        32 => 'green', 92 => 'light-green', 33 => 'yellow', 93 => 'light-yellow', 34 => 'blue', 94 => 'light-blue',
        35 => 'magenta', 95 => 'light-magenta', 36 => 'cyan', 96 => 'light-cyan'
    ];
    $maxLen = max(array_map('strlen', $colors));
    foreach ($colors as $key => $fgColor) {
        $console->write(sprintf('${%s}%' . $maxLen . 's: ', $fgColor, $fgColor));
        foreach ($colors as $bgColor) {
            $console->write(sprintf('${fg:%s;bg:%s} %s ${r}', $fgColor, $bgColor, $key));
        }
        $console->write(PHP_EOL);
    }
}

function inputDemo(Console $console)
{
    // Questions
    $console->line(PHP_EOL . '${bold;cyan}Questions');
    $name = $console->ask('What is your name?', 'John Doe');
    $console->info('Hello ' . $name . '!');
    if ($console->ask(new Confirmation('Is this correct?'))) {
        $console->info('Great!');
    } else {
        $console->warn('Why you are lying to me?');
    }

    // Check if interactive
    if (!$console->isInteractive()) {
        $console->warn('non-interactive output detected');
        $console->info('The result of read* is always "' . $console->readLine('$ ') . '"');
    }

    // Read line (prefers readline with history)
    if ($console->isInteractive()) {
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
        readline_clear_history();
    }

    // Read specific amount of chars (UTF-8 compatible)
    $console->write('Enter 3 letters:');
    $input = $console->read(3);
    $console->line(sprintf('You entered: "%s"', $input));

    // Read until
    $console->info('Enter your message (end with dot in line for itself)');
    $message = $console->readUntil(PHP_EOL . '.' . PHP_EOL, " \e[D"); // draw empty lines
    $console->line(sprintf('Message:' . PHP_EOL . '"""%s"""', $message));
}

function choicesDemo(Console $console)
{
    // Choices
    $console->line(PHP_EOL . '${bold;cyan}Choices');

    if (!$console->isInteractive()) {
        $console->warn('non-interactive output detected');
        $console->info('The result of ask is always the default value');
    }

    $names = [
        'ezra' => 'Ezra Trickett', 'leticia' => 'Leticia Karpinski', 'celinda' => 'Celinda Baskett',
        'jerlene' => 'Jerlene Esteban', 'merideth' => 'Merideth Utsey', 'jame' => 'Jame Depaolo',
        'shirlene' => 'Shirlene Fraire', 'carmon' => 'Carmon Frese', 'dion' => 'Dion Rundell',
        'elouise' => 'Elouise Mcgovern', 'leslee' => 'Leslee Rispoli', 'inell' => 'Inell Feinstein',
        'burton' => 'Burton Lamontagne', 'machelle' => 'Machelle Wattley', 'thomas' => 'Thomas Franklin',
        'maynard' => 'Maynard Gabourel', 'beverley' => 'Beverley Eisenbarth', 'van' => 'Van Meeks',
        'maren' => 'Maren Wildermuth', 'shoshana' => 'Shoshana Harry', 'prince' => 'Prince Calbert',
        'jackeline' => 'Jackeline Livermore', 'eufemia' => 'Eufemia Loux', 'almeda' => 'Almeda Bjornson',
        'mignon' => 'Mignon Zollars', 'reyes' => 'Reyes Nodine', 'pinkie' => 'Pinkie Hedman',
        'pablo' => 'Pablo Moyer', 'yuette' => 'Yuette Venezia', 'mitch' => 'Mitch Helwig',
    ];
    // without changing options
    $chosen = $console->ask(new Choice(
        array_values($names),
        'Choose your character:',
        'Van Meeks'
    ));
    $console->line('You have chosen: ${green}' . $chosen);
    // show only 10 (only if your term is interactive) and return values
    $chosen = $console->ask(
        (new Choice($names))
            ->limit(10)
            ->returnValue()
    );
    $console->line('You have chosen: ${green}' . $chosen);
    // show only 10 (only if your term is interactive) and return keys (by default)
    $chosen = $console->ask(
        (new Choice($names))
            ->limit(10)
    );
    $console->line('You have chosen: ${green}' . $chosen);
    // non interactive (write your answer) and return key
    $chosen = $console->ask(
        (new Choice(array_values($names), '', 23))
            ->nonInteractive()
            ->returnKey()
    );
    $console->line('You have chosen: ${green}' . $chosen . ' (' . array_values($names)[$chosen] . ')');
}

function advancedDemo(Console $console)
{
    $console->line(PHP_EOL . '${bold;cyan}Delete');
    $console->write('Importing xml file ... ${yellow}in progress');
    sleep(2);
    $console->delete('in progress'); // or ->delete(11)
    $console->line('${green}done');
    $console->write('waiting for something...');
    $console->deleteLine();
}

function progressDemo(Console $console)
{
    $console->line(PHP_EOL . '${bold;cyan}ProgressBar bar');

    // simple progress bar
    $progress = new ProgressBar($console, 80);
    $progress->width(10)->start();
    for ($i = 0; $i < 80; $i++) {
        usleep(40000);
        $progress->advance();
    }
    $progress->finish();

    // concurrent progress bars
    $packages = ['openssh', 'gimp', 'libreoffce', 'linux', 'firefox', 'inkscape', 'conky', 'gnome'];
    $downloads = [];
    $console->info(sprintf('Start downloading updates for %d packages', count($packages)));
    foreach ($packages as $package) {
        $kb = mt_rand(1000, 3000);
        $packageDownload = [
            'package' => $package,
            'kb' => $kb,
            'progress' => new ProgressBar($console, $kb, 'Downloading ' . $package, 'kb'),
            'loaded' => 0,
        ];
        $downloads[] = $packageDownload;
        $packageDownload['progress']->updateRate(0.1)->start();
    }
    $progressDownloads = new ProgressBar($console, count($packages), 'Downloaded', 'updates');
    $progressDownloads->start();
    while (!empty($downloads)) {
        usleep(mt_rand(6000, 20000));
        foreach ($downloads as $i => &$packageDownload) {
            $loaded = mt_rand(1, 10);
            $packageDownload['progress']->advance($loaded);
            $packageDownload['loaded'] += $loaded;
            if ($packageDownload['loaded'] >= $packageDownload['kb']) {
                $packageDownload['progress']->finish();
                $console->info('Downloaded ' . $packageDownload['package']);
                array_splice($downloads, $i, 1);
                $progressDownloads->advance();
            }
        }
    }
    $progressDownloads->finish();

    // undetermined progress bars
    $console->info('Installing updates...');
    $progressInstall = new ProgressBar($console, null, 'Updating packages');
    $progressInstall->start();
    foreach ($packages as $package) {
        $installTime = mt_rand(500, 1500) / 1000;
        $start = microtime(true);
        while (microtime(true) - $start < $installTime) {
            usleep(40000);
            $progressInstall->advance();
        }
        $console->info('Updated ' . $package);
    }
    $progressInstall->template('{title} ${green}done')->finish();
}

function tablesDemo(Console $console)
{
    $console->line(PHP_EOL . '${bold;cyan}Tables');

    $table = new Table($console, [
        ['23', 'John Doe', 'john.doe', 'john.doe@example.com'],
        ['42', 'Marvin', 'marvin', 'marvin@example.com'],
        ['5552342', 'Arthur Dent', 'adent', 'arthur@example.com', 'extra column'],
        ['213', 'Röhrich', 'roerich', '${red}roerich'],
    ], ['External ID', 'Name', 'User', 'E-Mail']);

    // Simple table
    $table->draw();

    // Simple table
    $table->column(4, ['delete' => true])
        ->bordersInside(true)
        ->repeatHeaders(3)
        ->headerStyle('${b;red}')
        ->padding(3)
        ->draw();

    $table->padding(1)
        ->bordersInside(false)
        ->draw();

    $table->column(0, ['header' => 'ID', 'align' => 'center'])
        ->borders(false)
        ->draw();

    $table->borderStyle([
        Table::BORDER_HORIZONTAL => '-',
        Table::BORDER_VERTICAL => '|',
        Table::CROSS => ' ',
    ])
        ->borders(true)
        ->draw();
}

function observerDemo(Console $console)
{
    $console->line(PHP_EOL . '${bold;cyan}Observer (press esc to stop)');
    $observer = $console->getInputObserver();
    if (!$observer) {
        $console->warn('observers are not supported for this input / output interfaces');
        return;
    }
    $observer->on("\e", function ($event) use ($observer) {
        $event->stopPropagation = true;
        $observer->stop();
    });
    $observer->addHandler(function ($event) {
        $keycodes = implode(" ", array_map('dechex', array_map('ord', str_split($event->char))));
        echo  "Keycode: $keycodes" . PHP_EOL;
    });
    $observer->start();
}

drawLogo($console);

$ranDemo = false;
foreach ($_SERVER['argv'] as $arg) {
    if ($arg === '--force-ansi') {
        $console->disableAnsi(false);
    } elseif (in_array($arg, [
            'formatting',
            'colors',
            'choices',
            'input',
            'advanced',
            'progress',
            'tables',
            'observer'
        ])) {
        call_user_func($arg . 'Demo', $console);
        $ranDemo = true;
    }
}

if ($ranDemo) {
    return;
}

while (true) {
    $next = askNext($console, $next ?? 'exit');
    if ($next === 'exit') {
        return;
    }

    $console->line('');
    call_user_func($next . 'Demo', $console);
}
