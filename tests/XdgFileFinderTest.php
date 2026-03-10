<?php

namespace alcamo\conf;

use PHPUnit\Framework\TestCase;
use alcamo\exception\{InvalidEnumerator, SecurityViolation};

class XdgFileFinderTest extends TestCase
{
    /**
     * @dataProvider basicsProvider
     */
    public function testBasics(
        $subdir,
        $type,
        $filename,
        $expectedDefaultDir,
        $expectedPathname
    ): void {
        $configHome = __DIR__;
        $dataHome = __DIR__ . DIRECTORY_SEPARATOR . 'foo';
        $dataHome1 = __DIR__;
        $dataHome2 = dirname($configHome);

        putenv("XDG_CONFIG_HOME=$configHome");
        putenv("XDG_CONFIG_DIRS=$dataHome2");
        putenv("XDG_DATA_HOME=$dataHome");
        putenv("XDG_DATA_DIRS=$dataHome1:$dataHome2");

        $finder = new XdgFileFinder($subdir, $type);

        $this->assertSame($subdir ?? 'alcamo', $finder->getSubdir());

        $this->assertSame($type ?? 'CONFIG', $finder->getType());

        $this->assertSame($expectedDefaultDir, $finder->getDefaultDir());

        $pathname = $finder->find($filename);

        $this->assertSame($expectedPathname, $pathname);
    }

    public function basicsProvider(): array
    {
        $configHome = __DIR__;
        $dataHome = __DIR__ . DIRECTORY_SEPARATOR . 'foo';
        $dataHome1 = __DIR__;
        $dataHome2 = dirname($configHome);

        putenv("XDG_CONFIG_HOME=$configHome");
        putenv("XDG_CONFIG_DIRS=$dataHome2");
        putenv("XDG_DATA_HOME=$dataHome");
        putenv("XDG_DATA_DIRS=$dataHome1:$dataHome2");

        return [
            'typical-use' => [
                null,
                null,
                'foo.json',
                $configHome . DIRECTORY_SEPARATOR . 'alcamo',
                $configHome . DIRECTORY_SEPARATOR
                . 'alcamo' . DIRECTORY_SEPARATOR . 'foo.json'
            ],

            'custom-subdir' => [
                'vendor',
                null,
                'autoload.php',
                $configHome . DIRECTORY_SEPARATOR . 'vendor',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'autoload.php'
            ],

            'data-file' => [
                'src',
                'DATA',
                'XdgFileFinder.php',
                $dataHome . DIRECTORY_SEPARATOR . 'src',
                $dataHome2 . DIRECTORY_SEPARATOR
                . 'src' . DIRECTORY_SEPARATOR . 'XdgFileFinder.php'
            ]
        ];
    }

    public function testState(): void
    {
        $finder = new XdgFileFinder(null, 'STATE');

        $this->assertSame(
            $finder->getHomeDir() . DIRECTORY_SEPARATOR . '.local'
                . DIRECTORY_SEPARATOR . 'state',
            $finder->getHomeStateDir()
        );

        $stateHome = __DIR__;
        $subdir = 'foo';
        $stateDir = $stateHome . DIRECTORY_SEPARATOR . $subdir;
        $filename = 'bar.json';

        if (is_dir($stateDir)) {
            if (file_exists($stateDir . DIRECTORY_SEPARATOR . $filename)) {
                unlink($stateDir . DIRECTORY_SEPARATOR . $filename);
            }

            rmdir($stateDir);
        }

        putenv("XDG_STATE_HOME=$stateHome");

        $finder = new XdgFileFinder($subdir, 'STATE');

        $this->assertSame($stateDir, $finder->getDefaultDir());

        $pathname = $finder->find($filename);

        $this->assertSame(
            $stateDir . DIRECTORY_SEPARATOR . $filename,
            $pathname
        );

        $this->assertTrue(is_dir($stateDir));
        $this->assertTrue(file_exists($pathname));

        unlink($pathname);
        rmdir($stateDir);
    }

    public function testCache(): void
    {
        $finder = new XdgFileFinder(null, 'CACHE');

        $this->assertSame(
            $finder->getHomeDir() . DIRECTORY_SEPARATOR . '.cache',
            $finder->getHomeCacheDir()
        );

        $cacheHome = __DIR__;
        $subdir = 'foo';
        $cacheDir = $cacheHome . DIRECTORY_SEPARATOR . $subdir;
        $filename = 'bar.json';

        if (is_dir($cacheDir)) {
            if (file_exists($cacheDir . DIRECTORY_SEPARATOR . $filename)) {
                unlink($cacheDir . DIRECTORY_SEPARATOR . $filename);
            }

            rmdir($cacheDir);
        }

        putenv("XDG_CACHE_HOME=$cacheHome");

        $finder = new XdgFileFinder($subdir, 'CACHE');

        $this->assertSame($cacheDir, $finder->getDefaultDir());

        $pathname = $finder->find($filename, LoaderInterface::CONFIDENTIAL);

        $this->assertSame(
            $cacheDir . DIRECTORY_SEPARATOR . $filename,
            $pathname
        );

        $this->assertTrue(is_dir($cacheDir));
        $this->assertTrue(file_exists($pathname));
        $this->assertSame(0, fileperms($cacheDir) & 0x3f);
        $this->assertSame(0, fileperms($pathname) & 0x3f);

        unlink($pathname);
        rmdir($cacheDir);
    }

    public function testRuntime(): void
    {
        $runtimeBase = __DIR__;
        $subdir = 'foo';
        $runtimeDir = $runtimeBase . DIRECTORY_SEPARATOR . $subdir;
        $filename = 'bar.pipe';

        if (is_dir($runtimeDir)) {
            if (file_exists($runtimeDir . DIRECTORY_SEPARATOR . $filename)) {
                unlink($runtimeDir . DIRECTORY_SEPARATOR . $filename);
            }

            rmdir($runtimeDir);
        }

        putenv("XDG_RUNTIME_DIR=$runtimeBase");

        $finder = new XdgFileFinder($subdir, 'RUNTIME');

        $this->assertSame($runtimeDir, $finder->getDefaultDir());

        $pathname = $finder->find($filename);

        $this->assertSame(
            $runtimeDir . DIRECTORY_SEPARATOR . $filename,
            $pathname
        );

        $this->assertTrue(is_dir($runtimeDir));
        $this->assertTrue(file_exists($pathname));

        $this->assertSame(0, fileperms($runtimeDir) & 0x3f);
        $this->assertSame(0, fileperms($pathname) & 0x3f);

        unlink($pathname);
        rmdir($runtimeDir);
    }

    public function testConstructException(): void
    {
        $this->expectException(InvalidEnumerator::class);
        $this->expectExceptionMessage(
            'Invalid value "FOO", expected one of '
                . '["CONFIG", "DATA", "STATE", "CACHE", '
        );

        new XdgFileFinder(null, 'FOO');
    }

    public function testSecurityViolationException1(): void
    {
        $configHome = __DIR__;

        putenv("XDG_CONFIG_HOME=$configHome");

        $alcamoPath = $configHome . DIRECTORY_SEPARATOR . 'alcamo';

        $quxPath = $alcamoPath . DIRECTORY_SEPARATOR . 'qux.ini';

        chmod($alcamoPath, 0700);
        chmod($quxPath, 0700);

        $finder = new XdgFileFinder();

        $this->assertSame(
            'qux.ini',
            basename($finder->find('qux.ini', LoaderInterface::CONFIDENTIAL))
        );

        chmod($quxPath, 0710);

        $this->expectException(SecurityViolation::class);
        $this->expectExceptionMessage('Security Violation; Permissions of');

        $finder->find('qux.ini', LoaderInterface::CONFIDENTIAL);
    }

    public function testSecurityViolationException2(): void
    {
        $configHome = __DIR__;

        putenv("XDG_CONFIG_HOME=$configHome");

        $alcamoPath = $configHome . DIRECTORY_SEPARATOR . 'alcamo';

        $quxPath = $alcamoPath . DIRECTORY_SEPARATOR . 'qux.ini';

        chmod($alcamoPath, 0710);
        chmod($quxPath, 0700);

        $this->expectException(SecurityViolation::class);
        $this->expectExceptionMessage('Security Violation; Permissions of');

        (new XdgFileFinder())->find('qux.ini', LoaderInterface::CONFIDENTIAL);
    }
}
