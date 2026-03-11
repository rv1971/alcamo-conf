<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Configuration file loader based on a file finder and a file parser
 *
 * @date Last reviewed 2025-12-22
 */
class Loader implements LoaderInterface
{
    /// Default file finder class
    public const DEFAULT_FILE_FINDER_CLASS = XdgFileFinder::class;

    /// Default file parser class
    public const DEFAULT_FILE_PARSER_CLASS = FileParser::class;

    /// Files to load first
    public const CONF_FILES = [];

    private $fileFinder_; ///< FileFinderInterface
    private $fileParser_; ///< FileParserInterface

    /**
     * @param $fileParser @copybrief getFileParser(), defaults to a new
     * FileParser instance.
     *
     * @param $fileFinder @copybrief getFileFinder(), defaults to a new
     * XdgFileFinder instance.
     */
    public function __construct(
        ?FileFinderInterface $fileFinder = null,
        ?FileParserInterface $fileParser = null
    ) {
        if (isset($fileFinder)) {
            $this->fileFinder_ = $fileFinder;
        } else {
            $class = static::DEFAULT_FILE_FINDER_CLASS;
            $this->fileFinder_ = new $class();
        }

        if (isset($fileParser)) {
            $this->fileParser_ = $fileParser;
        } else {
            $class = static::DEFAULT_FILE_PARSER_CLASS;
            $this->fileParser_ = new $class();
        }
    }

    /// Object used to find a configuration file
    public function getFileFinder(): FileFinderInterface
    {
        return $this->fileFinder_;
    }

    /// Object used to parse a configuration file
    public function getFileParser(): FileParserInterface
    {
        return $this->fileParser_;
    }

    /**
     * @copybrief alcamo::conf::LoaderInterface::load()
     *
     * @param $filename array|string|null file names to find and to load.
     *
     * @param $flags If $flags & alcamo::conf::LoaderInterface::CONFIDENTIAL,
     * ensure that only the user can access the file that is found.
     *
     * The list of files to load is constructed from
     * alcamo::conf::Loader::CONF_FILES (which may be empty) and $filenames
     * (which may be the name of a file, a possibly empty array of filenames,
     * or `null`) using the `+` operator.
     *
     * @attention This implies that numerically-indexed items in
     * alcamo::conf::Loader::CONF_FILES may be overriden by
     * numerically-indexed items in $filenames, which is not desired on most
     * cases. A good pratice is to use string keys in the former and numerical
     * indexes in the latter.
     *
     * Each file is found once and parsed. If there is more than one file, the
     * files are parsed without the AS_OBJECT flag, and the parsing results
     * are merged using the `+` operator, such that files later in the list
     * take precedence over files earlier in the list. If $flags contain
     * AS_OBJECT, the result is then converted to an object.
     *
     * This implies that more than one file is supported only if the parser
     * supports providing the result as an array.
     */
    public function load($filenames = null, ?int $flags = null)
    {
        $filenames = static::CONF_FILES + (array)$filenames;

        $result = [];

        foreach ($filenames as $filename) {
            $path = $this->fileFinder_->find($filename, $flags);

            if (!isset($path)) {
                /** @throw alcamo::exception::FileNotFound if the file finder
                 *  cannot find a file. */
                throw (new FileNotFound())->setMessageContext(
                    [
                        'filename' => $filename,
                        'inPlaces' => (string)$this->fileFinder_
                        ]
                );
            }

            if (count($filenames) == 1) {
                return $this->fileParser_->parse($path, $flags);
            }

            $result = $this->fileParser_
                ->parse($path, $flags & ~LoaderInterface::AS_OBJECT) + $result;
        }

        return $flags & LoaderInterface::AS_OBJECT
            ? json_decode(json_encode($result))
            : $result;
    }
}
