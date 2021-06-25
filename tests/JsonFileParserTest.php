<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\FileNotFound;

/** Positive test cases are done in FileParserTest.php: */

class JsonFileParserTest extends TestCase
{
    public function testNotFound()
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'none.json';

        $this->expectException(FileNotFound::class);

        $parser = new JsonFileParser();

        $parser->parse($fileName);
    }

    public function testJson()
    {
        $txtFileName = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'baz.txt';

        $this->expectException(\TypeError::class);

        $parser = new JsonFileParser();

        $parser->parse($txtFileName);
    }
}
