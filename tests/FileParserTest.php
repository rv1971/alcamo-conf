<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\InvalidEnumerator;

class FileParserTest extends TestCase
{
    public function testParseIni()
    {
        $parser = new FileParser();

        $iniFilename = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'bar.ini';

        $iniData = $parser->parse($iniFilename);

        $this->assertSame(
            [
            'quux' => 45,
            'corge' => 'foo bar baz',
            'bar' => 46
            ],
            $iniData
        );
    }

    public function testParseJson()
    {
        $parser = new FileParser();

        $jsonFileName = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'foo.json';

        $jsonData = $parser->parse($jsonFileName);

        $this->assertSame(
            [
            'bar' => 44,
            'baz' => 'Stet clita kasd gubergren',
            'qux' => true
            ],
            $jsonData
        );
    }

    public function testInvalidExtension()
    {
        $txtFileName = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'baz.txt';

        $this->expectException(InvalidEnumerator::class);
        $this->expectExceptionMessage(
            "Invalid file extension in '$txtFileName', expected one of: 'ini', 'json'"
        );

        $parser = new FileParser();

        $parser->parse($txtFileName);
    }
}
