# tflori/hugga

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
$name = $console->aks('What is your name?');
$console->writeline('${fg:white;bg:white;bold}Nice to meet you ' . $name . '!');

$console->line('You will see this', Hugga\Console::WEIGHT_NORMAL);
$console->line('You will not see this', Hugga\Console::WEIGHT_LOWER);
$console->increaseVerbosity();
$console->line('No you can see this', Hugga\Console::WEIGHT_LOWER);
$console->line('But this is just a debug message', Hugga\Console::WEIGHT_DEBUG);
$console->setVerbosity(Hugga\Console::WEIGHT_DEBUG);
$console->debug(
    ['key' => 'value', 'recursive' => ['string', 42, null, true]],
    Hugga\Console::DEBUG_PRETTY ^ Hugga\Console::DEBUG_COLOR
 );
```
