# tflori/hugga

[![.github/workflows/push.yml](https://github.com/tflori/hugga/actions/workflows/push.yml/badge.svg)](https://github.com/tflori/hugga/actions/workflows/push.yml)
[![Code Coverage](https://qlty.sh/gh/tflori/projects/hugga/coverage.svg)](https://qlty.sh/gh/tflori/projects/hugga)
[![Maintainability](https://qlty.sh/gh/tflori/projects/hugga/maintainability.svg)](https://qlty.sh/gh/tflori/projects/hugga)
[![Latest Stable Version](http://poser.pugx.org/tflori/hugga/v)](https://packagist.org/packages/tflori/hugga)
[![Total Downloads](http://poser.pugx.org/tflori/hugga/downloads)](https://packagist.org/packages/tflori/hugga)
[![License](http://poser.pugx.org/tflori/hugga/license)](https://packagist.org/packages/tflori/hugga)

PHP library for console applications. It supports formatting of messages and tables as well input handling with
choices.

## Installation

Like all my libraries: only with composer

```console
$ composer require tflori/hugga
```

## Basic usage

```php
<?php

$console = new Hugga\Console;
$name = $console->ask('What is your name?');
$console->line('${fg:white;bg:white;bold}Nice to meet you ' . $name . '!');

$console->line('You will see this', Hugga\Console::WEIGHT_NORMAL);
$console->line('You will not see this', Hugga\Console::WEIGHT_LOWER);
$console->increaseVerbosity();
$console->line('Now you can see this', Hugga\Console::WEIGHT_LOWER);
$console->line('But this is just a debug message', Hugga\Console::WEIGHT_DEBUG);
//$console->setVerbosity(Hugga\Console::WEIGHT_DEBUG);
//$console->debug(
//    ['key' => 'value', 'recursive' => ['string', 42, null, true]],
//    Hugga\Console::DEBUG_PRETTY ^ Hugga\Console::DEBUG_COLOR
// );
```

## Usage

Create a `Console` instance and use it throughout your application. All input and output goes through this object.

```php
$console = new Hugga\Console;
```

### Writing output

`line()` writes a message followed by a newline. `write()` writes without a newline. Both accept an optional weight
(verbosity level) as the second argument — output below the current verbosity threshold is silently dropped.

```php
$console->line('Hello world');
$console->write('no newline here');
$console->line('extra detail', Hugga\Console::WEIGHT_LOWER);   // hidden at normal verbosity
$console->increaseVerbosity();                                  // lower threshold — now WEIGHT_LOWER is visible
$console->line('debug dump',   Hugga\Console::WEIGHT_DEBUG);   // still hidden; needs another increaseVerbosity()
```

Convenience methods apply a predefined style and write to the appropriate stream:

```php
$console->info('Operation complete');    // informational, stdout
$console->warn('Disk almost full');      // warning, stderr
$console->error('File not found');       // error, stderr
```

### Formatting

Embed format tags using `${...}` syntax anywhere in a string. Reset all formatting with `${r}` or `${reset}`.

```php
$console->line('${bold;cyan}Section header');
$console->line('${red}Error:${r} something went wrong');
$console->line('${fg:blue;bg:white}Blue on white');
$console->line('${u}underlined${r} and ${bold}bold${r}');
```

Available attributes: color names (`red`, `green`, `cyan`, `yellow`, `blue`, `magenta`, `white`, `grey`, …),
`bold` / `b`, `underline` / `u`, `fg:<color>`, `bg:<color>`.

### Deleting output

Remove recently written text without leaving a blank line:

```php
$console->write('Processing … ${yellow}in progress');
// ... do work ...
$console->delete('in progress');   // removes that exact string
$console->line('${green}done');

$console->write('Loading…');
$console->deleteLine();            // removes the entire current line
```

### Asking questions

Pass a string for a free-text prompt (optional default value as second argument):

```php
$name = $console->ask('What is your name?', 'Anonymous');
```

Use `Confirmation` for yes/no questions — returns `true` or `false`:

```php
if ($console->ask(new Hugga\Input\Question\Confirmation('Are you sure?'))) {
    // confirmed
}
```

Use `Choice` to let the user pick from a list. With an associative array the key is returned by default:

```php
$role = $console->ask(new Hugga\Input\Question\Choice(
    ['admin' => 'Administrator', 'guest' => 'Guest'],
    'Select a role:',
    'guest'   // default
));
// $role === 'admin' or 'guest'
```

In an interactive terminal the choice is navigated with cursor keys. Chain methods to customise behaviour:

```php
$console->ask(
    (new Hugga\Input\Question\Choice($options, 'Pick one:'))
        ->limit(10)        // show at most 10 options at a time
        ->returnValue()    // return the value instead of the key
);
```

### Tables

Pass the data rows and an optional header row to `Table`, then call `draw()`:

```php
$table = new Hugga\Output\Drawing\Table($console, [
    ['1', 'Alice', 'alice@example.com'],
    ['2', 'Bob',   'bob@example.com'],
], ['ID', 'Name', 'E-Mail']);

$table->draw();
```

Chain options to customise the appearance before drawing:

```php
$table
    ->borders(true)
    ->bordersInside(true)
    ->padding(2)
    ->repeatHeaders(20)
    ->headerStyle('${bold;cyan}')
    ->column(0, ['align' => 'center'])
    ->draw();
```

### Progress bars

Provide the total number of steps (or `null` for an indeterminate spinner) and call `start()`. Call `advance()` inside
your loop and `finish()` when done:

```php
$bar = new Hugga\Output\Drawing\ProgressBar($console, 100, 'Importing', 'rows');
$bar->start();
foreach ($rows as $row) {
    process($row);
    $bar->advance();
}
$bar->finish();
```

Multiple progress bars can run concurrently — each one stays pinned to the bottom of the output while regular log
lines scroll above it. Pass `null` as the total for an indeterminate throbber:

```php
$spinner = new Hugga\Output\Drawing\ProgressBar($console, null, 'Waiting for server');
$spinner->start();
// ... poll until done ...
$spinner->template('{title} ${green}done')->finish();
```

### Reading raw input

```php
$line  = $console->readLine('$ ');   // prompt + readline with history support
$chars = $console->read(3);          // read exactly 3 characters (multibyte-safe)
$text  = $console->readUntil("\n.\n"); // read until a sentinel string appears
```

### Observing keyboard input

`getInputObserver()` gives direct, non-echoed access to individual key presses:

```php
$observer = $console->getInputObserver();
$observer->on("\e", function ($event) use ($observer) {
    $observer->stop();
});
$observer->addHandler(function ($event) {
    echo 'key: ' . $event->char . PHP_EOL;
});
$observer->start();
```

### Full reference

See the [API reference](reference.md) for the complete method list, or run the bundled demo:

```console
$ php vendor/tflori/hugga/examples/test.php
```

A paginated-table example is in [examples/paginated-table.php](examples/paginated-table.php).

## Features

Some features are still planned but a lot of features are available and they are enough for start and replacing
symfony/console.

### Output Handling

- Drawings: a mechanism to stay at the end of your output while other output is printed above (clocks, progress bars
  etc.)
- Weighted output: just output and hugga will handle if the user want's to see it or not (verbosity)
- Formatting output: easy formatting with combined expression (example: `${red;bold}text${r}`)
- Output tables: easy to use tables with a lot of formatting features:
  - Predefined format: configure the formatting once and for all later tables
  - Borders: enable or disable borders (borders inside: between rows)
  - Padding: left and right padding inside cells
  - Repeat headers: repeat headers every nth row
  - Header style: define styles used for headers
- Progress bars: smooth progress bars with 8 steps (utf-8) and other formatting features:
  - Undetermined: throbber that spins between edges
  - Update rate: instead of define after how much steps the progressbar should update (symfony/console) you define how
    much time has to elapse before redrawing
  - Characters: change the characters used for the progress bar
  - Throbber: change the throbber used for undetermined progress bar
  - Floating point steps: use floating point numbers
  
### Input Handling

- InputObserver: directly access the keyboard without writing the output to console
- EditLine fix: edit line (replacement for read line) can not read single key presses
- ReadLine: use read line for reading from stdin if available
- Read chars: read a specific amount of characters (multibyte safe)
- Read until: read input until a specific string appears (example: `\n.\n`)
- Simple question: a simple question with default value
- Confirmation: a question with the choice between y(es) and n(o) (characters can be changed)
- Choice: a question to choose between a list of options
  - Interactive by default: choose with cursor keys and select with enter using InputObserver
  - Return key: return the key instead of the value (default for assoc arrays)
  - Limit: change the limit of visible options (for interactive version)
  
### Planned features

- Debug output: output variables in a human readable format with highlighting
- Interactive tables: scroll through tables using cursor keys and pagination
