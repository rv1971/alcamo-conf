<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a load() method to load configuration files
 *
 * @date Last reviewed 2025-12-22
 */
interface LoaderInterface
{
    /// Flag for use in FileFinderInterface::find
    public const CONFIDENTIAL = 1;

    /**
     * @brief Load configuration files and return the parsing result
     *
     * @param $filename Filename without directory.
     *
     * @param $flags If $flags & alcamo::conf::LoaderInterface::CONFIDENTIAL,
     * ensure that only the user can access the file that is found.
     */
    public function load($filenames, ?int $flags = null);
}
