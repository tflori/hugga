# tflori/hugga

[![Build Status](https://travis-ci.org/tflori/hugga.svg?branch=master)](https://travis-ci.org/tflori/hugga)
[![Coverage Status](https://coveralls.io/repos/github/tflori/hugga/badge.svg?branch=master)](https://coveralls.io/github/tflori/hugga?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/hugga/v/stable.svg)](https://packagist.org/packages/tflori/hugga) 
[![Total Downloads](https://poser.pugx.org/tflori/hugga/downloads.svg)](https://packagist.org/packages/tflori/hugga) 
[![License](https://poser.pugx.org/tflori/hugga/license.svg)](https://packagist.org/packages/tflori/hugga)

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
$console->line('No you can see this', Hugga\Console::WEIGHT_LOWER);
$console->line('But this is just a debug message', Hugga\Console::WEIGHT_DEBUG);
//$console->setVerbosity(Hugga\Console::WEIGHT_DEBUG);
//$console->debug(
//    ['key' => 'value', 'recursive' => ['string', 42, null, true]],
//    Hugga\Console::DEBUG_PRETTY ^ Hugga\Console::DEBUG_COLOR
// );
```

## Documentation

There is no documentation yet except this [api reference](reference.md). For some examples also have a look at
[test.php](test.php) and try them yourself with `php vendor/tflori/hugga/test.php`.

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
