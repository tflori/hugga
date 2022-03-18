<?php

use Hugga\Console;
use Hugga\Output\Drawing\ProgressBar;
use Hugga\Output\Drawing\Table;
use Hugga\Output\Drawing\UpdatingText;

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

// Initialization
$console = new Console();

$hashes = [];
for ($i = 0; $i < 333; $i++) {
    $password = str_replace(['+', '/', '='], '', base64_encode(openssl_random_pseudo_bytes(8)));
    $hashes[] = ['i' => $i, 'password' => $password, 'md5' => md5($password), 'sha1' => sha1($password)];
}

$offset = 0;
$pageSize = 30;
$table = new Table($console, array_slice($hashes, $offset, $pageSize), array_keys($hashes[0]));
foreach (array_keys($hashes[0]) as $column) {
    $table->column($column, [
        'width' => max(array_map(function ($row) use ($column) {
            return strlen($row[$column] ?? '');
        }, $hashes)),
    ]);
}
$pagination = new ProgressBar($console, (int)ceil(count($hashes) / $pageSize), 'Page');
$pagination->width(ceil(count($hashes) / $pageSize))
    ->template('{title} {steps} (hint: use cursor left/right for pagination; ctrl+c to exit)');
$console->addDrawing($table);
$console->addDrawing($pagination);
$pagination->start(floor($offset/$pageSize)+1);

$paginate = function ($pages) use (&$offset, $pageSize, $hashes, $table, $console, $pagination) {
    $offset += $pages*$pageSize;
    if ($offset >= count($hashes) || $offset < 0) {
        $offset -= $pages*$pageSize;
    }
    $table->setData(array_slice($hashes, $offset, $pageSize));
    $pagination->progress(floor($offset/$pageSize)+1);
    $console->redraw();
};

$observer = $console->getInputObserver();
$observer->on("\x1b\x5b\x43", function () use ($paginate) {
    $paginate(1);
});
$observer->on("\x1b\x5b\x44", function () use ($paginate) {
    $paginate(-1);
});
$observer->start();
