<?php

namespace Hugga\Test\Output\Drawing;

use Hugga\Output\Drawing\ProgressBar;
use Hugga\Test\TestCase;

class ProgressBarTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->console->shouldReceive('addDrawing')->byDefault();
    }

    protected function tearDown()
    {
        parent::tearDown();
        ProgressBar::resetDefaultProgressCharacters();
        ProgressBar::resetDefaultThrobber();
        ProgressBar::setFormatDefinition('steps-with-type', '{done}/{max} {type}');
    }


    /** @test */
    public function rendersDefaultTemplate()
    {
        $progress = new ProgressBar($this->console, 100);

        $text = $progress->getText();

        self::assertSame(
            '  0/100 |' . str_repeat(' ', 50) . '|   0%',
            $text
        );
    }

    /** @test */
    public function shrinksBar()
    {
        $progress = new ProgressBar($this->console, 20);

        $text = $progress->getText();

        self::assertSame(
            ' 0/20 |' . str_repeat(' ', 20) . '|   0%',
            $text
        );
    }

    /** @test */
    public function doesNotShrinkForFloats()
    {
        $progress = new ProgressBar($this->console, 20.0);

        $text = $progress->getText();

        self::assertSame(
            ' 0.00/20 |' . str_repeat(' ', 50) . '|   0%',
            $text
        );
    }

    /** @test */
    public function rendersDefaultTemplateWithTitle()
    {
        $progress = new ProgressBar($this->console, 100, 'with title');

        $text = $progress->getText();

        self::assertSame(
            'with title   0/100 |' . str_repeat(' ', 50) . '|   0%',
            $text
        );
    }

    /** @test */
    public function rendersUndeterminedTemplate()
    {
        $progress = new ProgressBar($this->console, null);

        $text = $progress->getText();

        self::assertSame(
            '|█▓▒░' . str_repeat(' ', 16) . '|',
            $text
        );
    }

    /** @test */
    public function rendersUndeterminedTemplateWithTitle()
    {
        $progress = new ProgressBar($this->console, null, 'foo');

        $text = $progress->getText();

        self::assertSame(
            'foo |█▓▒░' . str_repeat(' ', 16) . '|',
            $text
        );
    }

    /** @test */
    public function rendersCustomTemplate()
    {
        $progress = new ProgressBar($this->console, 10, 'custom progress');

        $progress->template('|{progress}| {steps} {title}');

        self::assertSame(
            '|' . str_repeat(' ', 10) . '|  0/10 custom progress',
            $progress->getText()
        );
    }

    /** @test */
    public function rendersUndeterminedCustomTemplate()
    {
        $progress = new ProgressBar($this->console, null, 'custom progress');

        $progress->template('{progress} {steps} {title}');

        self::assertSame(
            '█▓▒░' . str_repeat(' ', 16) . ' {steps} custom progress', // no steps available
            $progress->getText()
        );
    }

    /** @test */
    public function forcesAnUnderterminedProgressBar()
    {
        $progress = new ProgressBar($this->console, 10);

        $progress->undetermined();

        self::assertSame(
            '|█▓▒░' . str_repeat(' ', 16) . '|',
            $progress->getText()
        );
    }

    /** @test */
    public function addsDrawingToConsole()
    {
        $progress = new ProgressBar($this->console, 10);
        $this->console->shouldReceive('addDrawing')->with($progress)
            ->once()->andReturn(true);

        $progress->start();
    }

    /** @test */
    public function setsTheProgress()
    {
        $progress = new ProgressBar($this->console, 10);

        $progress->progress(5);

        self::assertSame(
            ' 5/10 |█████     |  50%',
            $progress->getText()
        );
    }

    /** @test */
    public function resetsTheProgress()
    {
        $progress = new ProgressBar($this->console, 10);
        $progress->progress(5);

        $progress->start();

        self::assertSame(
            ' 0/10 |          |   0%',
            $progress->getText()
        );
    }

    /** @test */
    public function limitsToMax()
    {
        $progress = new ProgressBar($this->console, 10);

        $progress->progress(15);

        self::assertSame(
            '10/10 |██████████| 100%',
            $progress->getText()
        );
    }

    /** @test */
    public function doesNotRedraw()
    {
        $progress = new ProgressBar($this->console, 10);
        $progress->updateRate(1);
        $progress->start();
        $this->console->shouldNotReceive('redraw');

        $progress->progress(1);
    }

    /** @test */
    public function redrawsAfterUpdateRate()
    {
        $progress = new ProgressBar($this->console, 10);
        $progress->updateRate(0.001);
        $progress->start();
        $this->console->shouldReceive('redraw')->with()
            ->once();

        usleep(0.001 * 1000000); // sleep for update rate
        $progress->progress(1);
    }

    /** @test */
    public function forceRedraw()
    {
        $progress = new ProgressBar($this->console, 10);
        $progress->updateRate(1);
        $progress->start();
        $this->console->shouldReceive('redraw')->with()
            ->once();

        $progress->progress(1, true);
    }

    /** @test */
    public function increasesBySteps()
    {
        $progress = new ProgressBar($this->console, 10);

        $progress->advance();

        self::assertSame(
            ' 1/10 |█         |  10%',
            $progress->getText()
        );

        $progress->advance(5);

        self::assertSame(
            ' 6/10 |██████    |  60%',
            $progress->getText()
        );
    }

    /** @test */
    public function removesDrawingAndSetsProgressToMax()
    {
        $progress = new ProgressBar($this->console, 10);
        $this->console->shouldReceive('removeDrawing')->with($progress)
            ->once()->andReturn(false);

        $progress->finish();

        self::assertSame(
            '10/10 |██████████| 100%',
            $progress->getText()
        );
    }

    /** @test */
    public function drawsSteps()
    {
        $progress = new ProgressBar($this->console, 24);
        $progress->width(3)->template('|{progress}|')->start(8);

        self::assertSame('|█  |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▏ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▎ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▍ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▌ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▋ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▊ |', $progress->getText()) || $progress->advance();
        self::assertSame('|█▉ |', $progress->getText());
    }

    /** @test */
    public function usesDifferentProgressCharacters()
    {
        $progress = new ProgressBar($this->console, 1.0);
        $progress->width(3);
        $progress->progressCharacters(' ', '█', '▒');

        $progress->progress(0.5);
        self::assertSame('0.50/1 |█▒ |  50%', $progress->getText());
    }

    /** @test */
    public function ninetyNineRoundsDown()
    {
        $progress = new ProgressBar($this->console, 100);
        $progress->width(10);

        $progress->progress(99);
        self::assertSame(' 99/100 |█████████▉|  99%', $progress->getText());
    }

    /** @test */
    public function changeProgressCharacters()
    {
        $progress = new ProgressBar($this->console, 1.0);
        $progress->width(5);
        $progress->progressCharacters('░', '█', '▒', '▓');

        $progress->progress(0.5);
        self::assertSame('0.50/1 |██▒░░|  50%', $progress->getText());
    }

    /** @test */
    public function undeterminedIgnoresProgress()
    {
        $progress = new ProgressBar($this->console, null);
        $progress->width(7);
        $progress->start();

        self::assertSame('|█▓▒░   |', $progress->getText());
        $progress->progress(1);
        self::assertSame('|█▓▒░   |', $progress->getText());
    }

    /** @test */
    public function undeterminedMovesOneStepWithFlush()
    {
        $progress = new ProgressBar($this->console, null);
        $progress->width(7);
        $progress->start();

        self::assertSame('|█▓▒░   |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|▓█▓▒░  |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|▒▓█▓▒░ |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|░▒▓█▓▒░|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| ░▒▓█▓▒|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|  ░▒▓█▓|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|   ░▒▓█|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|  ░▒▓█▓|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| ░▒▓█▓▒|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|░▒▓█▓▒░|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|▒▓█▓▒░ |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|▓█▓▒░  |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|█▓▒░   |', $progress->getText());
    }

    /** @test */
    public function changeThrobber()
    {
        $progress = new ProgressBar($this->console, null);
        $progress->width(7)->throbber('<[]>');
        $progress->start();

        self::assertSame('|]>     |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|[]>    |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|<[]>   |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| <[]>  |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|  <[]> |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|   <[]>|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|    <[]|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|     <[|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|    <[]|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|   <[]>|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|  <[]> |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| <[]>  |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|<[]>   |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|[]>    |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|]>     |', $progress->getText());
    }

    /** @test */
    public function showsFilledAfterFinish()
    {
        $progress = new ProgressBar($this->console, null);
        $progress->width(7)->progressCharacters(' ', '█');

        $progress->finish();

        self::assertSame('|███████|', $progress->getText());
    }

    /** @test */
    public function changesDefaultThrobber()
    {
        ProgressBar::setDefaultThrobber('◊');
        $progress = new ProgressBar($this->console, null);
        $progress->width(3)->start();

        self::assertSame('|◊  |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| ◊ |', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('|  ◊|', $progress->getText()) || $progress->progress(0, true);
        self::assertSame('| ◊ |', $progress->getText());
    }

    /** @test */
    public function changeDefaultProgressCharacters()
    {
        ProgressBar::setDefaultProgressCharacters(' ', '█', '░', '▒', '▓');
        $progress = new ProgressBar($this->console, 12);
        $progress->width(3)->start(4);

        self::assertSame(' 4/12 |█  |  33%', $progress->getText()) || $progress->advance();
        self::assertSame(' 5/12 |█░ |  42%', $progress->getText()) || $progress->advance();
        self::assertSame(' 6/12 |█▒ |  50%', $progress->getText()) || $progress->advance();
        self::assertSame(' 7/12 |█▓ |  58%', $progress->getText());
    }

    /** @test */
    public function changeTypeTemplate()
    {
        ProgressBar::setFormatDefinition('steps-with-type', '{done%.2f} of {max} {type} done');
        $progress = new ProgressBar($this->console, 1, '', 'tests');

        self::assertSame('0.00 of 1 tests done | |   0%', $progress->getText());
    }
}
