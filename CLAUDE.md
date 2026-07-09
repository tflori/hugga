## Using hugga in a project

`Hugga\Console` is the single entry point for all console I/O. Obtain it from the container or construct it directly:

```php
$console = new Hugga\Console;
```

### Output

```php
$console->line('message');           // line with newline
$console->write('no newline');
$console->info('…');                 // styled informational (stdout)
$console->warn('…');                 // styled warning (stderr)
$console->error('…');                // styled error (stderr)
```

Embed formatting with `${...}` tags; reset with `${r}`:

```php
$console->line('${bold;cyan}Header');
$console->line('${red}Error:${r} details');
$console->line('${fg:blue;bg:white}label');
```

All output methods accept an optional weight (second argument). The default verbosity threshold is `WEIGHT_NORMAL`
(200) — messages with a lower weight are silently dropped. Higher weight means more important (always shown); lower
weight means only shown when the user asks for more output.

```
WEIGHT_HIGH   (300)  errors / always visible, even in quiet mode   ← default for error()/warn()
WEIGHT_HIGHER (250)
WEIGHT_NORMAL (200)  ← default threshold and default for line()/info()
WEIGHT_LOWER  (150)  hidden at normal verbosity
WEIGHT_LOW    (125)
WEIGHT_DEBUG  (100)  only shown at maximum verbosity
```

```php
$console->line('extra detail', Hugga\Console::WEIGHT_LOWER);  // hidden unless verbosity raised
$console->line('debug dump',   Hugga\Console::WEIGHT_DEBUG);  // hidden unless verbosity raised

$console->increaseVerbosity();                                 // lower threshold one step (show more)
$console->setVerbosity(Hugga\Console::WEIGHT_DEBUG);          // show everything
$console->setVerbosity(Hugga\Console::WEIGHT_HIGH);           // quiet mode: only errors/warnings
```

### Input

```php
$answer = $console->ask('Prompt?');                    // free text, returns string
$answer = $console->ask('Prompt?', 'default');         // with default
$yes    = $console->ask(new Hugga\Input\Question\Confirmation('Sure?')); // bool
$key    = $console->ask(new Hugga\Input\Question\Choice($options, 'Pick:'));
```

`Choice` accepts an indexed or associative array. With an associative array the **key** is returned by default;
chain `->returnValue()` to return the value instead. Chain `->limit(N)` to cap how many options are shown at once.

Always check `$console->isInteractive()` before relying on interactive input features. In CI or piped contexts
`Choice` falls back to a text prompt and `readLine` returns an empty string.

### Tables

```php
$table = new Hugga\Output\Drawing\Table($console, $rows, $headers);
$table->borders(true)->bordersInside(true)->padding(2)->draw();
```

`$rows` is an array of arrays. Columns missing from a row are left blank. Cells may contain format tags.

### Progress bars

```php
// Determinate
$bar = new Hugga\Output\Drawing\ProgressBar($console, $total, 'Label', 'unit');
$bar->start();
foreach ($items as $item) { process($item); $bar->advance(); }
$bar->finish();

// Indeterminate (spinner)
$bar = new Hugga\Output\Drawing\ProgressBar($console, null, 'Working…');
$bar->start();
// … poll/wait …
$bar->finish();
```

Multiple `ProgressBar` instances can be active simultaneously — each stays pinned to the bottom while `line()`/`info()`
output scrolls above.

### Deleting output

```php
$console->write('Processing … ${yellow}pending');
doWork();
$console->delete('pending');   // removes that exact string from the current line
$console->line('${green}done');

$console->deleteLine();        // removes the entire current line
```

---

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
