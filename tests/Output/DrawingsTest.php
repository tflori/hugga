<?php

namespace Hugga\Test\Output;

use Hugga\Output\File;
use Hugga\Output\Tty;
use Hugga\OutputInterface;
use Hugga\Test\Examples\Drawing;
use Hugga\Test\TestCase;
use Mockery as m;

class DrawingsTest extends TestCase
{
    /** @var OutputInterface|m\Mock */
    protected $stdout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stdout = m::mock(Tty::class);
        $this->console->setStdout($this->stdout);
    }

    /**
     * @return OutputInterface|m\Mock
     */
    protected function provideNonInteractiveOutput()
    {
        /** @var OutputInterface|m\Mock $stdout */
        $stdout = m::mock(File::class);
        $this->console->setStdout($stdout);
        return $stdout;
    }

    /*******************************
     * With non-interactive output *
     *******************************/

    /** @test */
    public function writesDrawingToStdoutWhenRemoving()
    {
        $stdout = $this->provideNonInteractiveOutput();
        $drawing = new Drawing();
        $this->console->addDrawing($drawing);

        $stdout->shouldReceive('write')->with($this->formatter->format($drawing->getText()) . PHP_EOL)
            ->once();

        $this->console->removeDrawing($drawing);
    }

    /** @test */
    public function addsDrawingsOnlyOnce()
    {
        $this->provideNonInteractiveOutput();
        $drawing = new Drawing();
        $this->console->addDrawing($drawing);

        self::assertFalse($this->console->addDrawing($drawing));
    }

    /** @test */
    public function doesNotDrawNonAdded()
    {
        $this->provideNonInteractiveOutput();
        $drawing = new Drawing();

        self::assertFalse($this->console->removeDrawing($drawing));
    }
    
    /***************************
     * With interactive output *
     ***************************/

    /** @test */
    public function drawsAddedDrawing()
    {
        $drawing = new Drawing();

        $this->stdout->shouldReceive('deleteLines')->with(0, $this->formatter->format($drawing->getText()))
            ->once();

        $this->console->addDrawing($drawing);
    }

    /** @test */
    public function cleansPreviousDrawingsBefore()
    {
        $drawing1 = new Drawing();
        $drawing2 = new Drawing();

        $this->stdout->shouldReceive('deleteLines')->with(0, $this->formatter->format($drawing1->getText()))
            ->once()->ordered();
        $this->stdout->shouldReceive('deleteLines')->with(
            4,
            $this->formatter->format($drawing1->getText()) . PHP_EOL .
            $this->formatter->format($drawing2->getText())
        )->once()->ordered();

        $this->console->addDrawing($drawing1);
        $this->console->addDrawing($drawing2);
    }

    /** @test */
    public function redrawsDrawings()
    {
        $drawing1 = new Drawing();
        $drawing2 = new Drawing();

        // what was before
        $this->stdout->shouldReceive('deleteLines')->with(0, $this->formatter->format($drawing1->getText()))
            ->once()->ordered();
        $this->stdout->shouldReceive('deleteLines')->with(
            4,
            $this->formatter->format($drawing1->getText()) . PHP_EOL .
            $this->formatter->format($drawing2->getText())
        )->once()->ordered();
        $this->console->addDrawing($drawing1);
        $this->console->addDrawing($drawing2);

        // redrawing expectation
        $this->stdout->shouldReceive('deleteLines')->with(
            8,
            $this->formatter->format($drawing1->getText()) . PHP_EOL .
            $this->formatter->format($drawing2->getText())
        )->once()->ordered();

        $this->console->redraw();
    }

    /** @test */
    public function cleansDrawingsOutputsTheRemovedThenRedrawsLeft()
    {
        $drawing1 = new Drawing();
        $drawing2 = new Drawing();

        // what was before
        $this->stdout->shouldReceive('deleteLines')->with(0, $this->formatter->format($drawing1->getText()))
            ->once()->ordered();
        $this->stdout->shouldReceive('deleteLines')->with(
            4,
            $this->formatter->format($drawing1->getText()) . PHP_EOL .
            $this->formatter->format($drawing2->getText())
        )->once()->ordered();
        $this->console->addDrawing($drawing1);
        $this->console->addDrawing($drawing2);

        // cleanup expectation
        $this->stdout->shouldReceive('deleteLines')->with(8)
            ->once()->ordered();

        // output of removed drawing
        $this->stdout->shouldReceive('write')->with(
            $this->formatter->format($drawing2->getText()) . PHP_EOL
        )->once()->ordered();

        // drawing of left drawings
        $this->stdout->shouldReceive('write')->with(
            $this->formatter->format($drawing1->getText())
        )->once()->ordered();

        $this->console->removeDrawing($drawing2);
    }
}
