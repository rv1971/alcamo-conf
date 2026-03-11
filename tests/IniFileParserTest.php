<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\FileNotFound;

/** More positive test cases are done in FileParserTest.php: */
class IniFileParserTest extends TestCase
{
    public function testSections(): void
    {
        $parser = new IniFileParser(true, INI_SCANNER_NORMAL);

        $iniFilename = __DIR__ . DIRECTORY_SEPARATOR .
        'alcamo' . DIRECTORY_SEPARATOR . 'bar.ini';

        $iniData = $parser->parse($iniFilename);

        $this->assertSame(
            [
                'Foo' => [
                    'quux' => '45',
                    'corge' => 'foo bar baz'
                ],
                'Bar' => [
                    'bar' => '46'
                ]
            ],
            $iniData
        );

        $iniData2 = $parser->parse($iniFilename, LoaderInterface::AS_OBJECT);

        $this->assertEquals(json_decode(json_encode($iniData)), $iniData2);
    }

    public function testNotFound(): void
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'none.ini';

        $this->expectException(FileNotFound::class);

        $parser = new IniFileParser();

        $parser->parse($fileName);
    }
}
