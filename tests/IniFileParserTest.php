<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\FileNotFound;

/** Positive test cases are done in FileParserTest.php: */

class IniFileParserTest extends TestCase
{
    public function testNotFound()
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'none.ini';

        $this->expectException(FileNotFound::class);

        $parser = new IniFileParser();

        $parser->parse($fileName);
    }
}
