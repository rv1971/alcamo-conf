# Usage examples

~~~
use alcamo\conf\{Loader, XdgFileFinder}

$conf = (new Loader(new XdgFileFinder('foo')))->Load(
    [ 'default.ini', 'conf.ini' ]
);
~~~

Now `$conf` contains an array representing the contents of
`$XDG_CONFIG_HOME/foo/default.ini` and
`$XDG_CONFIG_HOME/foo/conf.ini`, where items in the latter replace
items in the former with the same key, if `$XDG_CONFIG_HOME` is
set. Otherwise, `foo/conf.ini` is searched as specified in the
[XDG Base Directory Specification](https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html)

~~~
use alcamo\conf\{Loader as LoaderBase, XdgFileFinder as XdgFileFinderBase}

class XdgFileFinder extends XdgFileFinderBase
{
    public const SUBDIR = 'foo';
}

class Loader extends LoaderBase
{
    public const DEFAULT_FILE_PARSER_CLASS = XdgFileFinder::class;

    public const CONF_FILES = [ 'default.ini' ];
}

$conf = (new Loader())->Load('conf.ini');
~~~

This has exactly the same effect and may be more useful if
configurations are loaded from the same subdirectory in many places in
the code, in particular when there is a common basic configuration
files plus specific configuration files.

~~~
use alcamo\conf\{Loader as LoaderBase, XdgFileFinder as XdgFileFinderBase}

class XdgFileFinder extends XdgFileFinderBase
{
    public const SUBDIR = 'bar';
}

$cacheFilename = (new XdgFileFinder(null, 'CACHE'))->find('data.json');

~~~

Now, `$cacheFilename` contains `$XDG_CACHE_HOME/bar/data.json` if
`$XDG_CACHE_HOME` is set. Otherwise, the path is constructed as
specified in the XDG Base Directory Specification. If the directory
`$XDG_CACHE_HOME/bar` did not exist, it has been created
automatically.

# Supplied interfaces, classes and traits

## Interface `FileFinderInterface`

Interface for configuration file finders to put into a Loader object.

## Class `XdgFileFinder`

Finds config files and other files following the XDG Base Directory
Specification.

## Interface `FileParserInterface`

Interface for file parsers to put into a Loader object.

## Class `IniFileParser`

Parser for INI files.

## Class `JsonFileParser`

Parser for JSON files.

## Class `FileParser`

Parser for INI or JSON files, telling the file type from the suffix.

## Interface `LoaderInterface`

Interface implemented by the Loader class.

## Class `Loader`

Contains a file finder and a file parser object and uses them to
locate and load file or a nuimber of files.

## Interface `HavingConfInterface`

Class that provides an array with configuration data.

## Trait `HavingConfTrait`

Simple implementation of HavingConfInterface.
