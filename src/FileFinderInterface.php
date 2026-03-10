<?php

namespace alcamo\conf;

/**
 * @namespace alcamo::conf
 *
 * @brief Simple reading of conf files from XDG base directories
 */

/**
 * @brief Object implementing a find() method to find a file by its name
 *
 * @date Last reviewed 2025-12-22
 */
interface FileFinderInterface
{
    /**
     * @brief Find a file by its name
     *
     * @param $filename Filename without directory.
     *
     * @param $flags If $flags & alcamo::conf::LoaderInterface::CONFIDENTIAL,
     * ensure that only the user can access the file that is found.
     */
    public function find(string $filename, ?int $flags = null): ?string;

    /// Serialize this object, mainly for debugging
    public function __toString(): string;
}
