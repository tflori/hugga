<?php

namespace Hugga\Test;

class FormatterTest extends TestCase
{
    /** @test */
    public function appendsResetToTheEnd()
    {
        $result = $this->formatter->format('foo bar');

        self::assertSame("foo bar\e[0m", $result);
    }

    /** @test */
    public function replacesSimpleFormats()
    {
        $result = $this->formatter->format('${bold}foo bar');

        self::assertSame("\e[1mfoo bar\e[0m", $result);
    }

    /** @test */
    public function multipleFormattingInOneTag()
    {
        $result = $this->formatter->format('${ bold ; underline }foo bar');

        self::assertSame("\e[1m\e[4mfoo bar\e[0m", $result);
    }

    /** @test */
    public function emptyFormatGetsIgnored()
    {
        $result = $this->formatter->format('${ bold ; ; underline }foo bar');

        self::assertSame("\e[1m\e[4mfoo bar\e[0m", $result);
    }

    /** @test */
    public function unknownFormattingGetsIgnored()
    {
        $result = $this->formatter->format('${unknown}foo bar');

        self::assertSame("foo bar\e[0m", $result);
    }

    /** @test */
    public function foregroundColorsByName()
    {
        $result = $this->formatter->format('${fg:red}foo bar');

        self::assertSame("\e[31mfoo bar\e[0m", $result);
    }

    /** @test */
    public function backgroundColorsByName()
    {
        $result = $this->formatter->format('${bg:red}foo bar');

        self::assertSame("\e[41mfoo bar\e[0m", $result);
    }

    /** @test */
    public function foregroundIsFallbackWhenFormatUnknown()
    {
        $result = $this->formatter->format('${red}foo bar');

        self::assertSame("\e[31mfoo bar\e[0m", $result);
    }

    /** @test */
    public function foregroundWith256Colors()
    {
        $result = $this->formatter->format('${fg:34}foo bar');

        self::assertSame("\e[38;5;34mfoo bar\e[0m", $result);
    }

    /** @test */
    public function backgroundWith256Colors()
    {
        $result = $this->formatter->format('${bg:34}foo bar');

        self::assertSame("\e[48;5;34mfoo bar\e[0m", $result);
    }

    /** @dataProvider provideEscapedMessages
     * @param string $message
     * @param string $expected
     * @test */
    public function escapingFormatDefinition(string $message, string $expected)
    {
        $result = $this->formatter->format($message);

        self::assertSame($expected, $result);
    }

    public function provideEscapedMessages()
    {
        return [
            ['Somewhere \\${bold}between words.', "Somewhere \${bold}between words.\e[0m"],
            ['Somewhere \\\\${bold}between words.', "Somewhere \\\e[1mbetween words.\e[0m"],
            ['Somewhere \\\\\\${bold}between words.', "Somewhere \\\${bold}between words.\e[0m"],
            ['Somewhere \\\\\\\\${bold}between words.', "Somewhere \\\\\e[1mbetween words.\e[0m"],
        ];
    }

    /** @test */
    public function stripsFormatting()
    {
        $result = $this->formatter->stripFormatting('${bold}foo bar');

        self::assertSame('foo bar', $result);
    }

    /** @test */
    public function keepsEscapedFormatting()
    {
        $result = $this->formatter->stripFormatting('\\${bold}foo bar');
        
        self::assertSame('${bold}foo bar', $result);
    }
}
