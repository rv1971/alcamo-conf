<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Configuration file loader based on a file finder and a file parser
 *
 * @date Last reviewed 2021-06-15
 */
class Loader implements LoaderInterface
{
    private $fileFinder_; ///< FileFinderInterface
    private $fileParser_; ///< FileParserInterface

    /**
     * @param $fileFinder @copybrief getFileFinder(), defaults to a new
     * XdgFileFinder instance.
     *
     * @param $fileParser @copybrief getFileParser(), defaults to a new
     * FileParser instance.
     */
    public function __construct(
        ?FileFinderInterface $fileFinder = null,
        ?FileParserInterface $fileParser = null
    ) {
        $this->fileFinder_ = $fileFinder ?? new XdgFileFinder();
        $this->fileParser_ = $fileParser ?? new FileParser();
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
     * @brief Load and parse files.
     *
     * @param $filename iterable|string file names to find and to load
     *
     * Each file is parsed into an array. The arrays are merged such that
     * files later in the list take precedence over files earlier in the list.
     */
    public function load($filenames): array
    {
        $result = [];

        if (!is_iterable($filenames)) {
            $filenames = (array)$filenames;
        }

        foreach ($filenames as $filename) {
            $pathname = $this->fileFinder_->find($filename);

            if (!isset($pathname)) {
                /** @throw alcamo::exception::FileNotFound inf the file finder
                 *  cannot find a file. */
                throw new FileNotFound($filename, (string)$this->fileFinder_);
            }

            $result = $this->fileParser_->parse($pathname) + $result;
        }

        return $result;
    }
}
