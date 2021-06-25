<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\InvalidEnumerator;

class XdgFileFinderTest extends TestCase
{
  /**
   * @dataProvider basicsProvider
   */
    public function testBasics(
        $subdir,
        $type,
        $filename,
        $expectedPathname
    ) {
        $finder = new XdgFileFinder($subdir, $type);

        $this->assertSame($subdir ?? 'alcamo', $finder->getSubdir());

        $this->assertSame($type ?? 'CONFIG', $finder->getType());

        $pathname = $finder->find($filename);

        $this->assertSame($expectedPathname, $pathname);
    }

    public function basicsProvider(): array
    {
        $configHome = __DIR__;
        $dataHome1 = __DIR__;
        $dataHome2 = dirname($configHome);

        putenv("XDG_CONFIG_HOME=$configHome");
        putenv("XDG_DATA_DIRS=$dataHome1:$dataHome2");

        return [
            'typical-use' => [
                null,
                null,
                'foo.json',
                $configHome . DIRECTORY_SEPARATOR
                . 'alcamo' . DIRECTORY_SEPARATOR . 'foo.json'
            ],

            'custom-subdir' => [
                'vendor',
                null,
                'autoload.php',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php'
            ],

            'data-file' => [
                'src',
                'DATA',
                'XdgFileFinder.php',
                $dataHome2 . DIRECTORY_SEPARATOR
                . 'src' . DIRECTORY_SEPARATOR . 'XdgFileFinder.php'
            ]
        ];
    }

    public function testException()
    {
        $this->expectException(InvalidEnumerator::class);
        $this->expectExceptionMessage(
            'Invalid value "FOO", expected one of: "CONFIG", "DATA"'
        );

        new XdgFileFinder(null, 'FOO');
    }
}
