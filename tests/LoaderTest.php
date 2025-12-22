<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\FileNotFound;

class LoaderTest extends TestCase
{
    public function testLoad()
    {
        $configHome = __DIR__;

        putenv("XDG_CONFIG_HOME=$configHome");

        $loader = new Loader();

        $data1 = $loader->load([ 'foo.json', 'bar.ini' ]);

        $this->assertSame(
            [
                'quux' => 45,
                'corge' => 'foo bar baz',
                'bar' => 46,
                'baz' => 'Stet clita kasd gubergren',
                'qux' => true
            ],
            $data1
        );
    }

    public function testCustom()
    {
        $dataHome = __DIR__;

        putenv("XDG_DATA_HOME=$dataHome");

        $loader = new Loader(
            new XdgFileFinder('alcamo/subdir', 'DATA'),
            new IniFileParser(null, INI_SCANNER_NORMAL)
        );

        $this->assertSame(
            [
                'baz' => '43',
                'qux' => 'QUX'
            ],
            $loader->load('baz.ini')
        );
    }

    public function testFileNotFound()
    {
        $configHome = dirname(__DIR__);

        $this->expectException(FileNotFound::class);
        $this->expectExceptionMessage(
            "File \"foo.ini\" not found in \"$configHome:"
        );

        putenv("XDG_CONFIG_HOME=$configHome");
        putenv("XDG_CONFIG_DIRS=" . __DIR__);

        $loader = new Loader();

        $loader->load('foo.ini');
    }
}
