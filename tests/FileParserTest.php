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

        $data = [
            'quux' => 45,
            'corge' => 'foo bar baz',
            'bar' => 46
        ];

        $this->assertSame($data, $parser->parse($iniFilename));

        $this->assertEquals(
            (object)$data,
            $parser->parse($iniFilename, LoaderInterface::AS_OBJECT)
        );
    }

    public function testParseJson()
    {
        $parser = new FileParser();

        $jsonFileName = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'foo.json';

        $data = [
            'bar' => 44,
            'baz' => 'Stet clita kasd gubergren',
            'qux' => true
        ];

        $this->assertSame($data, $parser->parse($jsonFileName));

        $this->assertEquals(
            (object)$data,
            $parser->parse($jsonFileName, LoaderInterface::AS_OBJECT)
        );
    }

    public function testInvalidExtension()
    {
        $txtFileName = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'baz.txt';

        $this->expectException(InvalidEnumerator::class);
        $this->expectExceptionMessage(
            'Invalid file extension, expected one of ["ini", "json"] at URI '
        );

        $parser = new FileParser();

        $parser->parse($txtFileName);
    }
}
