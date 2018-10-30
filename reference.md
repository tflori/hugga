## API Reference


### Hugga

* [AbstractInputOutput](#huggaabstractinputoutput)
* [DrawingInterface](#huggadrawinginterface)
* [Formatter](#huggaformatter)
* [InputInterface](#huggainputinterface)
* [InteractiveInputInterface](#huggainteractiveinputinterface)
* [InteractiveOutputInterface](#huggainteractiveoutputinterface)
* [OutputInterface](#huggaoutputinterface)
* [QuestionInterface](#huggaquestioninterface)


### Hugga\Input

* [AbstractInput](#huggainputabstractinput)
* [Editline](#huggainputeditline)
* [File](#huggainputfile)
* [Observer](#huggainputobserver)
* [ObserverFaker](#huggainputobserverfaker)
* [Readline](#huggainputreadline)


### Hugga\Input\Question

* [AbstractQuestion](#huggainputquestionabstractquestion)
* [Choice](#huggainputquestionchoice)
* [Confirmation](#huggainputquestionconfirmation)
* [Simple](#huggainputquestionsimple)


### Hugga\Output

* [AbstractOutput](#huggaoutputabstractoutput)
* [File](#huggaoutputfile)
* [Tty](#huggaoutputtty)


### Hugga\Output\Drawing

* [ProgressBar](#huggaoutputdrawingprogressbar)
* [Table](#huggaoutputdrawingtable)


---

### Hugga\Input\Observer









#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$stdin` |  |  |
| **protected** | `$charHandler` |  |  |
| **protected** | `$handler` |  |  |
| **protected** | `$stop` |  |  |



#### Methods

* [__construct](#huggainputobserver__construct) 
* [addHandler](#huggainputobserveraddhandler) 
* [handle](#huggainputobserverhandle) 
* [isCompatible](#huggainputobserveriscompatible) 
* [off](#huggainputobserveroff) 
* [on](#huggainputobserveron) 
* [removeHandler](#huggainputobserverremovehandler) 
* [start](#huggainputobserverstart) 
* [stop](#huggainputobserverstop) 
* [ttySettings](#huggainputobserverttysettings) 

#### Hugga\Input\Observer::__construct

```php?start_inline=true
public function __construct( \Hugga\InputInterface $input ): Observer
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$input` | **\Hugga\InputInterface**  |  |



#### Hugga\Input\Observer::addHandler

```php?start_inline=true
public function addHandler( callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable**  |  |



#### Hugga\Input\Observer::handle

```php?start_inline=true
protected function handle( \Hugga\Input\string $char )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |



#### Hugga\Input\Observer::isCompatible

```php?start_inline=true
public static function isCompatible( \Hugga\InputInterface $input )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$input` | **\Hugga\InputInterface**  |  |



#### Hugga\Input\Observer::off

```php?start_inline=true
public function off( \Hugga\Input\string $char, callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |
| `$callback` | **callable**  |  |



#### Hugga\Input\Observer::on

```php?start_inline=true
public function on( \Hugga\Input\string $char, callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |
| `$callback` | **callable**  |  |



#### Hugga\Input\Observer::removeHandler

```php?start_inline=true
public function removeHandler( callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable**  |  |



#### Hugga\Input\Observer::start

```php?start_inline=true
public function start()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Input\Observer::stop

```php?start_inline=true
public function stop()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Input\Observer::ttySettings

```php?start_inline=true
public static function ttySettings( $options )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` |   |  |





---

### Hugga\DrawingInterface











#### Methods

* [getText](#huggadrawinginterfacegettext) Get the output for this drawing.

#### Hugga\DrawingInterface::getText

```php?start_inline=true
public function getText(): string
```

##### Get the output for this drawing.

The drawing may include formatting and line breaks.
It should never change the amount of rows.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />





---

### Hugga\Formatter









#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$formats` |  |  |
| **protected static** | `$fgColors` |  |  |
| **protected static** | `$bgColors` |  |  |
| **protected** | `$regexDefinition` |  |  |
| **protected** | `$regexTag` |  |  |



#### Methods

* [escape](#huggaformatterescape) 
* [format](#huggaformatterformat) Format a message
* [getEscapeSequence](#huggaformattergetescapesequence) Get the escape sequence(s) for $def
* [replaceFormatting](#huggaformatterreplaceformatting) 
* [stripFormatting](#huggaformatterstripformatting) 

#### Hugga\Formatter::escape

```php?start_inline=true
protected function escape( \Hugga\string $code )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$code` | **string**  |  |



#### Hugga\Formatter::format

```php?start_inline=true
public function format( string $message ): string
```

##### Format a message



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$message` | **string**  |  |



#### Hugga\Formatter::getEscapeSequence

```php?start_inline=true
protected function getEscapeSequence( string $def ): string
```

##### Get the escape sequence(s) for $def

$def can be anything that is defined static::$formats, just a foreground color name defined in static::$fgColors,
or prefixed color name or number like `bg:cyan` or `fg:256`.

In this function we don't test if the terminal supports a code. When the terminal does not support the code
it is simply not used. So keep in mind that many terminals don't support dim, blink and hidden.

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$def` | **string**  |  |



#### Hugga\Formatter::replaceFormatting

```php?start_inline=true
protected function replaceFormatting( \Hugga\string $message, $strip = false )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$message` | **string**  |  |
| `$strip` |   |  |



#### Hugga\Formatter::stripFormatting

```php?start_inline=true
public function stripFormatting( string $message ): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$message` | **string**  |  |





---

### Hugga\AbstractInputOutput









#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | **Console** |  |



#### Methods

* [__construct](#huggaabstractinputoutput__construct) 
* [getResource](#huggaabstractinputoutputgetresource) 
* [isCompatible](#huggaabstractinputoutputiscompatible) 

#### Hugga\AbstractInputOutput::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\AbstractInputOutput::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\AbstractInputOutput::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |





---

### Hugga\QuestionInterface











#### Methods

* [ask](#huggaquestioninterfaceask) 
* [getDefault](#huggaquestioninterfacegetdefault) 

#### Hugga\QuestionInterface::ask

```php?start_inline=true
public function ask( \Hugga\Console $console )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **Console**  |  |



#### Hugga\QuestionInterface::getDefault

```php?start_inline=true
public function getDefault()
```




**Visibility:** this method is **public**.
<br />






---

### Hugga\Output\Drawing\ProgressBar


**Implements:** [Hugga\DrawingInterface](#huggadrawinginterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$formatDefinitions` |  |  |
| **protected static** | `$defaultProgressCharacters` | **array&lt;string>** | Characters to use [empty, full, half] or [empty, full, one third, two thirds]. |
| **protected static** | `$defaultThrobber` | **string** | Throbber for undetermined progress bar |
| **protected** | `$width` | **integer** | Width of the progress bar (excl. other text) |
| **protected** | `$template` | **string** | Template to use |
| **protected** | `$max` | **float &#124; integer** |  |
| **protected** | `$done` | **integer** |  |
| **protected** | `$templateEngine` | ** \ StringTemplate \ AbstractEngine** |  |
| **protected** | `$title` | **string** |  |
| **protected** | `$type` | **string** |  |
| **protected** | `$undetermined` | **boolean** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |
| **protected** | `$lastFlush` | **float** |  |
| **protected** | `$updateRate` | **float** | Update every x seconds |
| **protected** | `$progressCharacters` | **array&lt;string>** |  |
| **protected** | `$throbber` | **string** |  |



#### Methods

* [__construct](#huggaoutputdrawingprogressbar__construct) ProgressBar constructor.
* [advance](#huggaoutputdrawingprogressbaradvance) 
* [finish](#huggaoutputdrawingprogressbarfinish) 
* [getMaxFormat](#huggaoutputdrawingprogressbargetmaxformat) 
* [getProgress](#huggaoutputdrawingprogressbargetprogress) 
* [getTemplate](#huggaoutputdrawingprogressbargettemplate) 
* [getTemplateEngine](#huggaoutputdrawingprogressbargettemplateengine) 
* [getText](#huggaoutputdrawingprogressbargettext) Get the output for this drawing.
* [getUndeterminedProgress](#huggaoutputdrawingprogressbargetundeterminedprogress) 
* [isUndetermined](#huggaoutputdrawingprogressbarisundetermined) 
* [progress](#huggaoutputdrawingprogressbarprogress) 
* [progressCharacters](#huggaoutputdrawingprogressbarprogresscharacters) 
* [resetDefaultProgressCharacters](#huggaoutputdrawingprogressbarresetdefaultprogresscharacters) Reset the default progress characters
* [resetDefaultThrobber](#huggaoutputdrawingprogressbarresetdefaultthrobber) Reset the default throbber
* [setDefaultProgressCharacters](#huggaoutputdrawingprogressbarsetdefaultprogresscharacters) Change the default progress characters
* [setDefaultThrobber](#huggaoutputdrawingprogressbarsetdefaultthrobber) Change the default throbber
* [setFormatDefinition](#huggaoutputdrawingprogressbarsetformatdefinition) Change or add format definitions
* [start](#huggaoutputdrawingprogressbarstart) 
* [template](#huggaoutputdrawingprogressbartemplate) 
* [throbber](#huggaoutputdrawingprogressbarthrobber) 
* [undetermined](#huggaoutputdrawingprogressbarundetermined) 
* [updateRate](#huggaoutputdrawingprogressbarupdaterate) 
* [width](#huggaoutputdrawingprogressbarwidth) 

#### Hugga\Output\Drawing\ProgressBar::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, float $max, string $title = '', string $type = ''
): ProgressBar
```

##### ProgressBar constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$max` | **float &#124; integer &#124; null**  |  |
| `$title` | **string**  |  |
| `$type` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::advance

```php?start_inline=true
public function advance( $steps = 1 )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$steps` |   |  |



#### Hugga\Output\Drawing\ProgressBar::finish

```php?start_inline=true
public function finish()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\ProgressBar::getMaxFormat

```php?start_inline=true
protected function getMaxFormat()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\ProgressBar::getProgress

```php?start_inline=true
protected function getProgress()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\ProgressBar::getTemplate

```php?start_inline=true
protected function getTemplate()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\ProgressBar::getTemplateEngine

```php?start_inline=true
protected function getTemplateEngine()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\ProgressBar::getText

```php?start_inline=true
public function getText(): string
```

##### Get the output for this drawing.

The drawing may include formatting and line breaks.
It should never change the amount of rows.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### Hugga\Output\Drawing\ProgressBar::getUndeterminedProgress

```php?start_inline=true
protected function getUndeterminedProgress()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\ProgressBar::isUndetermined

```php?start_inline=true
public function isUndetermined()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\ProgressBar::progress

```php?start_inline=true
public function progress( $done, \Hugga\Output\Drawing\bool $flush = false )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$done` |   |  |
| `$flush` | **bool**  |  |



#### Hugga\Output\Drawing\ProgressBar::progressCharacters

```php?start_inline=true
public function progressCharacters(
    \Hugga\Output\Drawing\string $empty, \Hugga\Output\Drawing\string $full, 
    \Hugga\Output\Drawing\string $steps
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$empty` | **string**  |  |
| `$full` | **string**  |  |
| `$steps` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::resetDefaultProgressCharacters

```php?start_inline=true
public static function resetDefaultProgressCharacters()
```

##### Reset the default progress characters



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\ProgressBar::resetDefaultThrobber

```php?start_inline=true
public static function resetDefaultThrobber()
```

##### Reset the default throbber



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\ProgressBar::setDefaultProgressCharacters

```php?start_inline=true
public static function setDefaultProgressCharacters(
    string $empty, string $full, string $steps
)
```

##### Change the default progress characters



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$empty` | **string**  |  |
| `$full` | **string**  |  |
| `$steps` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::setDefaultThrobber

```php?start_inline=true
public static function setDefaultThrobber( string $throbber )
```

##### Change the default throbber



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$throbber` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::setFormatDefinition

```php?start_inline=true
public static function setFormatDefinition( string $name, string $format )
```

##### Change or add format definitions



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$format` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::start

```php?start_inline=true
public function start( $done )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$done` |   |  |



#### Hugga\Output\Drawing\ProgressBar::template

```php?start_inline=true
public function template( \Hugga\Output\Drawing\string $template )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$template` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::throbber

```php?start_inline=true
public function throbber( \Hugga\Output\Drawing\string $throbber )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$throbber` | **string**  |  |



#### Hugga\Output\Drawing\ProgressBar::undetermined

```php?start_inline=true
public function undetermined()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\ProgressBar::updateRate

```php?start_inline=true
public function updateRate( \Hugga\Output\Drawing\float $updateRate )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$updateRate` | **float**  |  |



#### Hugga\Output\Drawing\ProgressBar::width

```php?start_inline=true
public function width( \Hugga\Output\Drawing\int $width )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$width` | **int**  |  |





---

### Hugga\Output\Drawing\Table


**Implements:** [Hugga\DrawingInterface](#huggadrawinginterface)






#### Constants

| Name | Value |
|------|-------|
| CORNER_TOP_LEFT | `'ctl'` |
| CORNER_TOP_RIGHT | `'ctr'` |
| CORNER_BOTTOM_LEFT | `'cbl'` |
| CORNER_BOTTOM_RIGHT | `'cbr'` |
| BORDER_HORIZONTAL | `'bho'` |
| BORDER_VERTICAL | `'bve'` |
| TEE_HORIZONTAL_DOWN | `'thd'` |
| TEE_HORIZONTAL_UP | `'thu'` |
| TEE_VERTICAL_RIGHT | `'tvr'` |
| TEE_VERTICAL_LEFT | `'tvl'` |
| CROSS | `'cro'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$defaultStyle` |  |  |
| **protected** | `$console` |  |  |
| **protected** | `$data` | **iterable** |  |
| **protected** | `$columns` |  |  |
| **protected** | `$border` |  |  |
| **protected** | `$bordersInside` |  |  |
| **protected** | `$padding` |  |  |
| **protected** | `$repeatHeader` |  |  |
| **protected** | `$headerStyle` |  |  |
| **protected** | `$borderStyle` |  |  |



#### Methods

* [__construct](#huggaoutputdrawingtable__construct) Table constructor.
* [applyDefaultStyle](#huggaoutputdrawingtableapplydefaultstyle) 
* [borders](#huggaoutputdrawingtableborders) Enable / disable borders
* [bordersInside](#huggaoutputdrawingtablebordersinside) Enable / disable borders inside
* [borderStyle](#huggaoutputdrawingtableborderstyle) Set the border style (the chars used for drawing the border)
* [column](#huggaoutputdrawingtablecolumn) Adjust column identified by $key
* [draw](#huggaoutputdrawingtabledraw) Draw the table on provided console
* [getBorderRow](#huggaoutputdrawingtablegetborderrow) 
* [getBottomBorderRow](#huggaoutputdrawingtablegetbottomborderrow) 
* [getDivider](#huggaoutputdrawingtablegetdivider) 
* [getHeaderRow](#huggaoutputdrawingtablegetheaderrow) 
* [getRows](#huggaoutputdrawingtablegetrows) 
* [getText](#huggaoutputdrawingtablegettext) Get the output for this drawing.
* [getTopBorderRow](#huggaoutputdrawingtablegettopborderrow) 
* [hasHeaders](#huggaoutputdrawingtablehasheaders) Check if headers are defined
* [headersFromKeys](#huggaoutputdrawingtableheadersfromkeys) Use the keys as of the rows as header
* [headerStyle](#huggaoutputdrawingtableheaderstyle) Set the header style
* [padding](#huggaoutputdrawingtablepadding) Set the padding in number of spaces
* [prepareColumns](#huggaoutputdrawingtablepreparecolumns) 
* [repeatHeaders](#huggaoutputdrawingtablerepeatheaders) Repeat the headers every $n rows
* [repeatRow](#huggaoutputdrawingtablerepeatrow) 
* [setDefaultStyle](#huggaoutputdrawingtablesetdefaultstyle) Change the default style for tables
* [setHeaders](#huggaoutputdrawingtablesetheaders) Set / Change the headers

#### Hugga\Output\Drawing\Table::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, \Hugga\Output\Drawing\iterable $data, 
    array $headers = null
): Table
```

##### Table constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$data` | **iterable**  |  |
| `$headers` | **array &#124; null**  |  |



#### Hugga\Output\Drawing\Table::applyDefaultStyle

```php?start_inline=true
protected function applyDefaultStyle()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::borders

```php?start_inline=true
public function borders( boolean $borders = true ): $this
```

##### Enable / disable borders



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$borders` | **boolean**  |  |



#### Hugga\Output\Drawing\Table::bordersInside

```php?start_inline=true
public function bordersInside( boolean $bordersInside = false ): $this
```

##### Enable / disable borders inside



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$bordersInside` | **boolean**  |  |



#### Hugga\Output\Drawing\Table::borderStyle

```php?start_inline=true
public function borderStyle( array $borderStyle ): $this
```

##### Set the border style (the chars used for drawing the border)



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$borderStyle` | **array**  |  |



#### Hugga\Output\Drawing\Table::column

```php?start_inline=true
public function column( $key, $definition ): $this
```

##### Adjust column identified by $key

$definition may include `width`, `header`, `delete` and `format`

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` |   |  |
| `$definition` |   |  |



#### Hugga\Output\Drawing\Table::draw

```php?start_inline=true
public function draw()
```

##### Draw the table on provided console



**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Drawing\Table::getBorderRow

```php?start_inline=true
protected function getBorderRow()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::getBottomBorderRow

```php?start_inline=true
protected function getBottomBorderRow()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::getDivider

```php?start_inline=true
protected function getDivider(
    $padding = ' ', $borderLeft = null, $borderRight = null, 
    $borderInside = null
)
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$padding` |   |  |
| `$borderLeft` |   |  |
| `$borderRight` |   |  |
| `$borderInside` |   |  |



#### Hugga\Output\Drawing\Table::getHeaderRow

```php?start_inline=true
protected function getHeaderRow()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::getRows

```php?start_inline=true
protected function getRows( array $data )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array**  |  |



#### Hugga\Output\Drawing\Table::getText

```php?start_inline=true
public function getText(): string
```

##### Get the output for this drawing.

The drawing may include formatting and line breaks.
It should never change the amount of rows.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### Hugga\Output\Drawing\Table::getTopBorderRow

```php?start_inline=true
protected function getTopBorderRow()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::hasHeaders

```php?start_inline=true
public function hasHeaders(): boolean
```

##### Check if headers are defined



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### Hugga\Output\Drawing\Table::headersFromKeys

```php?start_inline=true
public function headersFromKeys(): $this
```

##### Use the keys as of the rows as header



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### Hugga\Output\Drawing\Table::headerStyle

```php?start_inline=true
public function headerStyle( string $format ): $this
```

##### Set the header style



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$format` | **string**  |  |



#### Hugga\Output\Drawing\Table::padding

```php?start_inline=true
public function padding( integer $padding ): $this
```

##### Set the padding in number of spaces



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$padding` | **integer**  |  |



#### Hugga\Output\Drawing\Table::prepareColumns

```php?start_inline=true
protected function prepareColumns()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Output\Drawing\Table::repeatHeaders

```php?start_inline=true
public function repeatHeaders( integer $n = 10 ): $this
```

##### Repeat the headers every $n rows



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$n` | **integer**  |  |



#### Hugga\Output\Drawing\Table::repeatRow

```php?start_inline=true
protected function repeatRow(
    array &$rows, $repeat, \Hugga\Output\Drawing\int $every = 1
)
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rows` | **array**  |  |
| `$repeat` |   |  |
| `$every` | **int**  |  |



#### Hugga\Output\Drawing\Table::setDefaultStyle

```php?start_inline=true
public static function setDefaultStyle( array $defaultStyle )
```

##### Change the default style for tables

The default default style is:
```
[
     'border' => true,
     'bordersInside' => false,
     'padding' => 1,
     'repeatHeader' => 0,
     'headerStyle' => '${b}',
     'borderStyle' => [
         self::CORNER_TOP_LEFT => '╭',
         self::CORNER_TOP_RIGHT => '╮',
         self::CORNER_BOTTOM_LEFT => '╰',
         self::CORNER_BOTTOM_RIGHT => '╯',
         self::BORDER_HORIZONTAL => '─',
         self::BORDER_VERTICAL => '│',
         self::TEE_HORIZONTAL_DOWN => '┬',
         self::TEE_HORIZONTAL_UP => '┴',
         self::TEE_VERTICAL_LEFT => '┤',
         self::TEE_VERTICAL_RIGHT => '├',
         self::CROSS => '┼'
     ],
]
```

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$defaultStyle` | **array**  |  |



#### Hugga\Output\Drawing\Table::setHeaders

```php?start_inline=true
public function setHeaders(
    array $headers, boolean $adjustWidth = false
): $this
```

##### Set / Change the headers

Unless you pass $adjustWidth=true the width of the columns stays the same and longer column names are shortened

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$headers` | **array**  |  |
| `$adjustWidth` | **boolean**  |  |





---

### Hugga\Input\Question\AbstractQuestion


**Implements:** [Hugga\QuestionInterface](#huggaquestioninterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$question` | **string** |  |
| **protected** | `$default` | **mixed** |  |



#### Methods

* [__construct](#huggainputquestionabstractquestion__construct) 
* [getDefault](#huggainputquestionabstractquestiongetdefault) 
* [getQuestionText](#huggainputquestionabstractquestiongetquestiontext) 

#### Hugga\Input\Question\AbstractQuestion::__construct

```php?start_inline=true
public function __construct(
    string $question = '', string $default = null
): AbstractQuestion
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$question` | **string**  |  |
| `$default` | **string**  |  |



#### Hugga\Input\Question\AbstractQuestion::getDefault

```php?start_inline=true
public function getDefault(): mixed
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />



#### Hugga\Input\Question\AbstractQuestion::getQuestionText

```php?start_inline=true
protected function getQuestionText()
```




**Visibility:** this method is **protected**.
<br />






---

### Hugga\Output\AbstractOutput

**Extends:** [Hugga\AbstractInputOutput](#huggaabstractinputoutput)

**Implements:** [Hugga\OutputInterface](#huggaoutputinterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggaoutputabstractoutput__construct) 
* [getResource](#huggaoutputabstractoutputgetresource) 
* [isCompatible](#huggaoutputabstractoutputiscompatible) 

#### Hugga\Output\AbstractOutput::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Output\AbstractOutput::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Output\AbstractOutput::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |





---

### Hugga\InputInterface











#### Methods

* [getResource](#huggainputinterfacegetresource) 
* [read](#huggainputinterfaceread) 
* [readLine](#huggainputinterfacereadline) 
* [readUntil](#huggainputinterfacereaduntil) 

#### Hugga\InputInterface::getResource

```php?start_inline=true
public function getResource()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\InputInterface::read

```php?start_inline=true
public function read( \Hugga\int $count = 1, \Hugga\string $prompt = null )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |
| `$prompt` | **string**  |  |



#### Hugga\InputInterface::readLine

```php?start_inline=true
public function readLine( \Hugga\string $prompt = null )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$prompt` | **string**  |  |



#### Hugga\InputInterface::readUntil

```php?start_inline=true
public function readUntil(
    \Hugga\string $sequence, \Hugga\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sequence` | **string**  |  |
| `$prompt` | **string**  |  |





---

### Hugga\Input\ObserverFaker

**Extends:** [Hugga\Input\Observer](#huggainputobserver)


#### Class Observer






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$stdin` |  |  |
| **protected** | `$charHandler` |  |  |
| **protected** | `$handler` |  |  |
| **protected** | `$stop` |  |  |
| **protected** | `$keyPresses` |  |  |



#### Methods

* [__construct](#huggainputobserverfaker__construct) 
* [addHandler](#huggainputobserverfakeraddhandler) 
* [handle](#huggainputobserverfakerhandle) 
* [isCompatible](#huggainputobserverfakeriscompatible) 
* [off](#huggainputobserverfakeroff) 
* [on](#huggainputobserverfakeron) 
* [removeHandler](#huggainputobserverfakerremovehandler) 
* [sendKeys](#huggainputobserverfakersendkeys) Fake sending key presses after start()
* [start](#huggainputobserverfakerstart) Fake start listening
* [stop](#huggainputobserverfakerstop) 
* [ttySettings](#huggainputobserverfakerttysettings) 

#### Hugga\Input\ObserverFaker::__construct

```php?start_inline=true
public function __construct( \Hugga\InputInterface $input ): Observer
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$input` | **\Hugga\InputInterface**  |  |



#### Hugga\Input\ObserverFaker::addHandler

```php?start_inline=true
public function addHandler( callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable**  |  |



#### Hugga\Input\ObserverFaker::handle

```php?start_inline=true
protected function handle( \Hugga\Input\string $char )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |



#### Hugga\Input\ObserverFaker::isCompatible

```php?start_inline=true
public static function isCompatible( \Hugga\InputInterface $input )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$input` | **\Hugga\InputInterface**  |  |



#### Hugga\Input\ObserverFaker::off

```php?start_inline=true
public function off( \Hugga\Input\string $char, callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |
| `$callback` | **callable**  |  |



#### Hugga\Input\ObserverFaker::on

```php?start_inline=true
public function on( \Hugga\Input\string $char, callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |
| `$callback` | **callable**  |  |



#### Hugga\Input\ObserverFaker::removeHandler

```php?start_inline=true
public function removeHandler( callable $callback )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable**  |  |



#### Hugga\Input\ObserverFaker::sendKeys

```php?start_inline=true
public function sendKeys( array<string> $chars )
```

##### Fake sending key presses after start()



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$chars` | **array&lt;string>**  |  |



#### Hugga\Input\ObserverFaker::start

```php?start_inline=true
public function start()
```

##### Fake start listening

Sending keys will not have effect until started.

**Visibility:** this method is **public**.
<br />




#### Hugga\Input\ObserverFaker::stop

```php?start_inline=true
public function stop()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Input\ObserverFaker::ttySettings

```php?start_inline=true
public static function ttySettings( $options )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` |   |  |





---

### Hugga\Input\Question\Simple

**Extends:** [Hugga\Input\Question\AbstractQuestion](#huggainputquestionabstractquestion)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$question` | **string** |  |
| **protected** | `$default` | **mixed** |  |
| **protected** | `$required` | **boolean** |  |



#### Methods

* [__construct](#huggainputquestionsimple__construct) 
* [ask](#huggainputquestionsimpleask) 
* [getDefault](#huggainputquestionsimplegetdefault) 
* [getQuestionText](#huggainputquestionsimplegetquestiontext) 

#### Hugga\Input\Question\Simple::__construct

```php?start_inline=true
public function __construct(
    string $question = '', string $default = null
): AbstractQuestion
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$question` | **string**  |  |
| `$default` | **string**  |  |



#### Hugga\Input\Question\Simple::ask

```php?start_inline=true
public function ask( \Hugga\Console $console )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |



#### Hugga\Input\Question\Simple::getDefault

```php?start_inline=true
public function getDefault(): mixed
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />



#### Hugga\Input\Question\Simple::getQuestionText

```php?start_inline=true
protected function getQuestionText()
```




**Visibility:** this method is **protected**.
<br />






---

### Hugga\Input\Question\Confirmation

**Extends:** [Hugga\Input\Question\AbstractQuestion](#huggainputquestionabstractquestion)


#### Confirmation

Supports changing the characters (currently only ascii supported).

Example:
```php
$confirmation = new Confirmation('Wollen Sie fortfahren?');
$confirmation->setCharacters('j', 'n')->ask($console);
```

Ideas:
  - support utf8 characters
  - support




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$question` | **string** |  |
| **protected** | `$default` | **mixed** |  |
| **protected** | `$true` | **string** |  |
| **protected** | `$false` | **string** |  |



#### Methods

* [__construct](#huggainputquestionconfirmation__construct) 
* [ask](#huggainputquestionconfirmationask) 
* [getDefault](#huggainputquestionconfirmationgetdefault) 
* [getQuestionText](#huggainputquestionconfirmationgetquestiontext) 
* [setCharacters](#huggainputquestionconfirmationsetcharacters) 

#### Hugga\Input\Question\Confirmation::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Input\Question\string $question, string $default = false
): Confirmation
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$question` | **string**  |  |
| `$default` | **string**  |  |



#### Hugga\Input\Question\Confirmation::ask

```php?start_inline=true
public function ask( \Hugga\Console $console ): boolean|string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |



#### Hugga\Input\Question\Confirmation::getDefault

```php?start_inline=true
public function getDefault(): mixed
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />



#### Hugga\Input\Question\Confirmation::getQuestionText

```php?start_inline=true
protected function getQuestionText()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Input\Question\Confirmation::setCharacters

```php?start_inline=true
public function setCharacters( string $true = 'y', string $false = 'n' ): $this
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$true` | **string**  |  |
| `$false` | **string**  |  |





---

### Hugga\Input\Question\Choice

**Extends:** [Hugga\Input\Question\AbstractQuestion](#huggainputquestionabstractquestion)

**Implements:** [Hugga\DrawingInterface](#huggadrawinginterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$question` | **string** |  |
| **protected** | `$default` | **mixed** |  |
| **protected** | `$choices` | **array** |  |
| **protected** | `$indexedArray` | **boolean** |  |
| **protected** | `$interactive` | **boolean** |  |
| **protected** | `$returnKey` | **boolean** |  |
| **protected** | `$maxKeyLen` | **integer** |  |
| **protected** | `$selected` | **mixed** |  |
| **protected** | `$offset` | **integer** |  |
| **protected** | `$maxVisible` | **integer** |  |



#### Methods

* [__construct](#huggainputquestionchoice__construct) 
* [ask](#huggainputquestionchoiceask) 
* [askInteractive](#huggainputquestionchoiceaskinteractive) Starts the interactive question
* [askNonInteractive](#huggainputquestionchoiceasknoninteractive) Starts the non interactive question
* [changePos](#huggainputquestionchoicechangepos) Change selection by $change
* [charsToIndex](#huggainputquestionchoicecharstoindex) Converts a - zz to index
* [formatChoice](#huggainputquestionchoiceformatchoice) Format the choice
* [formatChoices](#huggainputquestionchoiceformatchoices) Format $choices as rows
* [getDefault](#huggainputquestionchoicegetdefault) 
* [getQuestionText](#huggainputquestionchoicegetquestiontext) 
* [getText](#huggainputquestionchoicegettext) Get the output for this drawing.
* [humanizeKeys](#huggainputquestionchoicehumanizekeys) Make an indexed array more readable for humans
* [indexToChars](#huggainputquestionchoiceindextochars) Converts an index to a - zz
* [isSelected](#huggainputquestionchoiceisselected) Check if $key =&gt; $value pair is selected
* [limit](#huggainputquestionchoicelimit) Show at max $count choices in interactive mode
* [nonInteractive](#huggainputquestionchoicenoninteractive) Don&#039;t use interactive mode
* [returnKey](#huggainputquestionchoicereturnkey) Return keys even if the choices have sequential keys
* [returnValue](#huggainputquestionchoicereturnvalue) Return value even if the choices have alphanumeric keys
* [updateSlice](#huggainputquestionchoiceupdateslice) Update the offset based on $maxVisible and
* [writeQuestionAndWaitAnswer](#huggainputquestionchoicewritequestionandwaitanswer) 

#### Hugga\Input\Question\Choice::__construct

```php?start_inline=true
public function __construct(
    array $choices, \Hugga\Input\Question\string $question = '', 
    string $default = null
): Choice
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$choices` | **array**  |  |
| `$question` | **string**  |  |
| `$default` | **string**  |  |



#### Hugga\Input\Question\Choice::ask

```php?start_inline=true
public function ask( \Hugga\Console $console )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |



#### Hugga\Input\Question\Choice::askInteractive

```php?start_inline=true
protected function askInteractive(
    \Hugga\Console $console, \Hugga\Input\Observer $observer
): integer|string
```

##### Starts the interactive question



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **integer|string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$observer` | **\Hugga\Input\Observer**  |  |



#### Hugga\Input\Question\Choice::askNonInteractive

```php?start_inline=true
protected function askNonInteractive(
    \Hugga\Console $console
): false|integer|mixed|null|string
```

##### Starts the non interactive question



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **false|integer|mixed|null|string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |



#### Hugga\Input\Question\Choice::changePos

```php?start_inline=true
protected function changePos(
    array $values, \Hugga\Console $console, integer $change, 
    boolean $loop = false
)
```

##### Change selection by $change



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$values` | **array**  |  |
| `$console` | **\Hugga\Console**  |  |
| `$change` | **integer**  |  |
| `$loop` | **boolean**  |  |



#### Hugga\Input\Question\Choice::charsToIndex

```php?start_inline=true
protected static function charsToIndex( string $c ): integer
```

##### Converts a - zz to index



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **integer**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$c` | **string**  |  |



#### Hugga\Input\Question\Choice::formatChoice

```php?start_inline=true
protected function formatChoice(
    string $key, string $value, boolean $selected = false
): string
```

##### Format the choice

Overload for different formatting.

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string &#124; integer**  |  |
| `$value` | **string**  |  |
| `$selected` | **boolean**  |  |



#### Hugga\Input\Question\Choice::formatChoices

```php?start_inline=true
protected function formatChoices( $choices ): string
```

##### Format $choices as rows



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$choices` |   |  |



#### Hugga\Input\Question\Choice::getDefault

```php?start_inline=true
public function getDefault(): mixed
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />



#### Hugga\Input\Question\Choice::getQuestionText

```php?start_inline=true
protected function getQuestionText()
```




**Visibility:** this method is **protected**.
<br />




#### Hugga\Input\Question\Choice::getText

```php?start_inline=true
public function getText(): string
```

##### Get the output for this drawing.

The drawing may include formatting and line breaks.
It should never change the amount of rows.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### Hugga\Input\Question\Choice::humanizeKeys

```php?start_inline=true
protected function humanizeKeys()
```

##### Make an indexed array more readable for humans

Replaces keys from indexed arrays from 1 to 9 or a to zz.

**Visibility:** this method is **protected**.
<br />




#### Hugga\Input\Question\Choice::indexToChars

```php?start_inline=true
protected static function indexToChars( integer $i ): string
```

##### Converts an index to a - zz



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$i` | **integer**  |  |



#### Hugga\Input\Question\Choice::isSelected

```php?start_inline=true
protected function isSelected( $key, string $value ): boolean
```

##### Check if $key => $value pair is selected



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` |   |  |
| `$value` | **string**  |  |



#### Hugga\Input\Question\Choice::limit

```php?start_inline=true
public function limit( integer $count ): $this
```

##### Show at max $count choices in interactive mode



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **integer**  |  |



#### Hugga\Input\Question\Choice::nonInteractive

```php?start_inline=true
public function nonInteractive(): $this
```

##### Don't use interactive mode



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### Hugga\Input\Question\Choice::returnKey

```php?start_inline=true
public function returnKey(): $this
```

##### Return keys even if the choices have sequential keys



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### Hugga\Input\Question\Choice::returnValue

```php?start_inline=true
public function returnValue(): $this
```

##### Return value even if the choices have alphanumeric keys



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### Hugga\Input\Question\Choice::updateSlice

```php?start_inline=true
protected function updateSlice()
```

##### Update the offset based on $maxVisible and



**Visibility:** this method is **protected**.
<br />




#### Hugga\Input\Question\Choice::writeQuestionAndWaitAnswer

```php?start_inline=true
protected function writeQuestionAndWaitAnswer( \Hugga\Console $console ): string
```




**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |





---

### Hugga\Input\Readline

**Extends:** [Hugga\Input\AbstractInput](#huggainputabstractinput)

**Implements:** [Hugga\InteractiveInputInterface](#huggainteractiveinputinterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggainputreadline__construct) 
* [getResource](#huggainputreadlinegetresource) 
* [isCompatible](#huggainputreadlineiscompatible) 
* [isEditline](#huggainputreadlineiseditline) Check if readline uses editline library
* [phpReadline](#huggainputreadlinephpreadline) Calls phps readline_* methods
* [read](#huggainputreadlineread) 
* [readConditional](#huggainputreadlinereadconditional) 
* [readLine](#huggainputreadlinereadline) 
* [readUntil](#huggainputreadlinereaduntil) 

#### Hugga\Input\Readline::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Input\Readline::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Input\Readline::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |



#### Hugga\Input\Readline::isEditline

```php?start_inline=true
protected static function isEditline(): boolean
```

##### Check if readline uses editline library



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### Hugga\Input\Readline::phpReadline

```php?start_inline=true
protected static function phpReadline( string $method, $args ): mixed
```

##### Calls phps readline_* methods



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **string**  |  |
| `$args` | **mixed**  |  |



#### Hugga\Input\Readline::read

```php?start_inline=true
public function read(
    \Hugga\Input\int $count = 1, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |
| `$prompt` | **string**  |  |



#### Hugga\Input\Readline::readConditional

```php?start_inline=true
protected function readConditional(
    callable $conditionMet, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$conditionMet` | **callable**  |  |
| `$prompt` | **string**  |  |



#### Hugga\Input\Readline::readLine

```php?start_inline=true
public function readLine( \Hugga\Input\string $prompt = null )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$prompt` | **string**  |  |



#### Hugga\Input\Readline::readUntil

```php?start_inline=true
public function readUntil(
    \Hugga\Input\string $sequence, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sequence` | **string**  |  |
| `$prompt` | **string**  |  |





---

### Hugga\Output\File

**Extends:** [Hugga\Output\AbstractOutput](#huggaoutputabstractoutput)







#### Constants

| Name | Value |
|------|-------|
| BUFFER_SIZE | `4096` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggaoutputfile__construct) 
* [delete](#huggaoutputfiledelete) 
* [deleteLine](#huggaoutputfiledeleteline) 
* [getResource](#huggaoutputfilegetresource) 
* [isCompatible](#huggaoutputfileiscompatible) 
* [truncate](#huggaoutputfiletruncate) 
* [write](#huggaoutputfilewrite) 

#### Hugga\Output\File::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Output\File::delete

```php?start_inline=true
public function delete( \Hugga\Output\int $count )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |



#### Hugga\Output\File::deleteLine

```php?start_inline=true
public function deleteLine( \Hugga\Output\int $bufferSize = self::BUFFER_SIZE )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$bufferSize` | **int**  |  |



#### Hugga\Output\File::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Output\File::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |



#### Hugga\Output\File::truncate

```php?start_inline=true
protected function truncate( \Hugga\Output\int $count )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |



#### Hugga\Output\File::write

```php?start_inline=true
public function write( \Hugga\Output\string $str )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$str` | **string**  |  |





---

### Hugga\Output\Tty

**Extends:** [Hugga\Output\AbstractOutput](#huggaoutputabstractoutput)

**Implements:** [Hugga\InteractiveOutputInterface](#huggainteractiveoutputinterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggaoutputtty__construct) 
* [delete](#huggaoutputttydelete) 
* [deleteLine](#huggaoutputttydeleteline) 
* [deleteLines](#huggaoutputttydeletelines) Deletes $count rows and replaces them with $replace
* [getResource](#huggaoutputttygetresource) 
* [getSize](#huggaoutputttygetsize) Get the size of the output window
* [isCompatible](#huggaoutputttyiscompatible) 
* [replace](#huggaoutputttyreplace) Replaces the amount of lines in $new with $new
* [write](#huggaoutputttywrite) 

#### Hugga\Output\Tty::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Output\Tty::delete

```php?start_inline=true
public function delete( \Hugga\Output\int $count )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |



#### Hugga\Output\Tty::deleteLine

```php?start_inline=true
public function deleteLine()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\Output\Tty::deleteLines

```php?start_inline=true
public function deleteLines(
    \Hugga\Output\int $count, \Hugga\Output\string $replace = ''
)
```

##### Deletes $count rows and replaces them with $replace

If $replace contains more rows than $count the rows will be appended.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |
| `$replace` | **string**  |  |



#### Hugga\Output\Tty::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Output\Tty::getSize

```php?start_inline=true
public function getSize(): array
```

##### Get the size of the output window

Returns an array with [int $rows, int $cols]

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### Hugga\Output\Tty::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |



#### Hugga\Output\Tty::replace

```php?start_inline=true
public function replace( \Hugga\Output\string $new )
```

##### Replaces the amount of lines in $new with $new



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$new` | **string**  |  |



#### Hugga\Output\Tty::write

```php?start_inline=true
public function write( \Hugga\Output\string $str )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$str` | **string**  |  |





---

### Hugga\Input\File

**Extends:** [Hugga\Input\AbstractInput](#huggainputabstractinput)







#### Constants

| Name | Value |
|------|-------|
| BUFFER_SIZE | `4096` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggainputfile__construct) 
* [getResource](#huggainputfilegetresource) 
* [isCompatible](#huggainputfileiscompatible) 
* [read](#huggainputfileread) 
* [readLine](#huggainputfilereadline) 
* [readUntil](#huggainputfilereaduntil) 

#### Hugga\Input\File::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Input\File::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Input\File::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |



#### Hugga\Input\File::read

```php?start_inline=true
public function read(
    \Hugga\Input\int $count = 1, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |
| `$prompt` | **string**  |  |



#### Hugga\Input\File::readLine

```php?start_inline=true
public function readLine( \Hugga\Input\string $prompt = null )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$prompt` | **string**  |  |



#### Hugga\Input\File::readUntil

```php?start_inline=true
public function readUntil(
    \Hugga\Input\string $sequence, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sequence` | **string**  |  |
| `$prompt` | **string**  |  |





---

### Hugga\Input\Editline

**Extends:** [Hugga\Input\Readline](#huggainputreadline)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |



#### Methods

* [__construct](#huggainputeditline__construct) 
* [getResource](#huggainputeditlinegetresource) 
* [isCompatible](#huggainputeditlineiscompatible) 
* [isEditline](#huggainputeditlineiseditline) Check if readline uses editline library
* [phpReadline](#huggainputeditlinephpreadline) Calls phps readline_* methods
* [read](#huggainputeditlineread) 
* [readConditional](#huggainputeditlinereadconditional) 
* [readLine](#huggainputeditlinereadline) 
* [readUntil](#huggainputeditlinereaduntil) 

#### Hugga\Input\Editline::__construct

```php?start_inline=true
public function __construct(
    \Hugga\Console $console, resource $resource
): AbstractInputOutput
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$console` | **\Hugga\Console**  |  |
| `$resource` | **resource**  |  |



#### Hugga\Input\Editline::getResource

```php?start_inline=true
public function getResource(): resource
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **resource**
<br />



#### Hugga\Input\Editline::isCompatible

```php?start_inline=true
public static function isCompatible( $resource )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resource` |   |  |



#### Hugga\Input\Editline::isEditline

```php?start_inline=true
protected static function isEditline(): boolean
```

##### Check if readline uses editline library



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### Hugga\Input\Editline::phpReadline

```php?start_inline=true
protected static function phpReadline( string $method, $args ): mixed
```

##### Calls phps readline_* methods



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **string**  |  |
| `$args` | **mixed**  |  |



#### Hugga\Input\Editline::read

```php?start_inline=true
public function read(
    \Hugga\Input\int $count = 1, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |
| `$prompt` | **string**  |  |



#### Hugga\Input\Editline::readConditional

```php?start_inline=true
protected function readConditional(
    callable $conditionMet, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$conditionMet` | **callable**  |  |
| `$prompt` | **string**  |  |



#### Hugga\Input\Editline::readLine

```php?start_inline=true
public function readLine( \Hugga\Input\string $prompt = null )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$prompt` | **string**  |  |



#### Hugga\Input\Editline::readUntil

```php?start_inline=true
public function readUntil(
    \Hugga\Input\string $sequence, \Hugga\Input\string $prompt = null
)
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sequence` | **string**  |  |
| `$prompt` | **string**  |  |





---

### Hugga\OutputInterface











#### Methods

* [delete](#huggaoutputinterfacedelete) 
* [deleteLine](#huggaoutputinterfacedeleteline) 
* [getResource](#huggaoutputinterfacegetresource) 
* [write](#huggaoutputinterfacewrite) 

#### Hugga\OutputInterface::delete

```php?start_inline=true
public function delete( \Hugga\int $count )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |



#### Hugga\OutputInterface::deleteLine

```php?start_inline=true
public function deleteLine()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\OutputInterface::getResource

```php?start_inline=true
public function getResource()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\OutputInterface::write

```php?start_inline=true
public function write( \Hugga\string $str )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$str` | **string**  |  |





---

### Hugga\InteractiveOutputInterface

**Extends:** [Hugga\OutputInterface](#huggaoutputinterface)










#### Methods

* [delete](#huggainteractiveoutputinterfacedelete) 
* [deleteLine](#huggainteractiveoutputinterfacedeleteline) 
* [deleteLines](#huggainteractiveoutputinterfacedeletelines) Deletes $count rows and replaces them with $replace
* [getResource](#huggainteractiveoutputinterfacegetresource) 
* [getSize](#huggainteractiveoutputinterfacegetsize) Get the size of the output window
* [replace](#huggainteractiveoutputinterfacereplace) Replaces the amount of lines in $new with $new
* [write](#huggainteractiveoutputinterfacewrite) 

#### Hugga\InteractiveOutputInterface::delete

```php?start_inline=true
public function delete( \Hugga\int $count )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **int**  |  |



#### Hugga\InteractiveOutputInterface::deleteLine

```php?start_inline=true
public function deleteLine()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\InteractiveOutputInterface::deleteLines

```php?start_inline=true
public function deleteLines( integer $count, string $replace = '' )
```

##### Deletes $count rows and replaces them with $replace

If $replace contains more rows than $count the rows will be appended.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$count` | **integer**  |  |
| `$replace` | **string**  |  |



#### Hugga\InteractiveOutputInterface::getResource

```php?start_inline=true
public function getResource()
```




**Visibility:** this method is **public**.
<br />




#### Hugga\InteractiveOutputInterface::getSize

```php?start_inline=true
public function getSize(): array
```

##### Get the size of the output window

Returns an array with [int $rows, int $cols]

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### Hugga\InteractiveOutputInterface::replace

```php?start_inline=true
public function replace( string $new )
```

##### Replaces the amount of lines in $new with $new



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$new` | **string**  |  |



#### Hugga\InteractiveOutputInterface::write

```php?start_inline=true
public function write( \Hugga\string $str )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$str` | **string**  |  |





---

### Hugga\Input\AbstractInput

**Extends:** [Hugga\AbstractInputOutput](#huggaabstractinputoutput)

**Implements:** [Hugga\InputInterface](#huggainputinterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$resource` | **resource** |  |
| **protected** | `$console` | ** \ Hugga \ Console** |  |




---

### Hugga\InteractiveInputInterface

**Extends:** [Hugga\InputInterface](#huggainputinterface)











---

