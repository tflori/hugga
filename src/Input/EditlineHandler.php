<?php

namespace Hugga\Input;

use Hugga\Console;

class EditlineHandler extends ReadlineHandler
{
    public static function isCompatible($resource): bool
    {
        if (AbstractInputHandler::isCompatible($resource) && Console::isTty($resource) && STDIN === $resource) {
            return self::isEditline();
        }
        return false;
    }

    protected function readConditional(callable $conditionMet, string $prompt = null): string
    {
        $this->console->write($prompt ?? ' ');
        $buffer = '';
        // read using input observer
        $observer = $this->console->getInputObserver();
        $observer->addHandler(function ($event) use (&$buffer, $observer, $conditionMet) {
            if (ord($event->char[0]) >= 32 || $event->char === "\n") {
                $this->console->write($event->char);
                $buffer .= $event->char;
                if ($conditionMet($buffer)) {
                    $observer->stop();
                }
            }
        });
        $observer->on("\x7f", function ($event) use (&$buffer) {
            $event->stopPropagation = true;
            if (strlen($buffer) && mb_substr($buffer, -1) != "\n") {
                $this->console->delete(1);
                $buffer = mb_substr($buffer, 0, -1);
            }
        });
        $observer->start();

        if (substr($buffer, -strlen(PHP_EOL)) !== PHP_EOL) {
            $this->console->write(PHP_EOL);
        }
        return $buffer;
    }
}
