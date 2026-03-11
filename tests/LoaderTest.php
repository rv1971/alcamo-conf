<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\{FileNotFound, SecurityViolation};

class MyLoader extends Loader
{
    public const CONF_FILES = [ 'bar' => 'bar.ini' ];
}

class LoaderTest extends TestCase
{
    public function testLoad()
    {
        $configHome = __DIR__;

        putenv("XDG_CONFIG_HOME=$configHome");

        $loader = new Loader();

        $data = [
            'quux' => 45,
            'corge' => 'foo bar baz',
            'bar' => 46,
            'baz' => 'Stet clita kasd gubergren',
            'qux' => true
        ];

        $this->assertSame($data, $loader->load([ 'foo.json', 'bar.ini' ]));

        $this->assertEquals(
            (object)$data,
            $loader->load([ 'foo.json', 'bar.ini' ], LoaderInterface::AS_OBJECT)
        );
    }

    public function testCustom()
    {
        $dataHome = __DIR__;

        putenv("XDG_DATA_HOME=$dataHome");

        $loader = new MyLoader(
            new XdgFileFinder('alcamo/subdir', 'DATA'),
            new IniFileParser(null, INI_SCANNER_NORMAL)
        );

        $this->assertSame(
            [
                'baz' => '43',
                'qux' => 'QUX',
                'corge' => '1'
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

    public function testSecurityViolationException(): void
    {
        $configHome = __DIR__;

        putenv("XDG_CONFIG_HOME=$configHome");

        $alcamoPath = $configHome . DIRECTORY_SEPARATOR . 'alcamo';

        $quxPath = $alcamoPath . DIRECTORY_SEPARATOR . 'qux.ini';

        chmod($alcamoPath, 0710);
        chmod($quxPath, 0700);

        $this->expectException(SecurityViolation::class);
        $this->expectExceptionMessage('Security Violation; Permissions of');

        (new Loader())->load('qux.ini', LoaderInterface::CONFIDENTIAL);
    }
}
