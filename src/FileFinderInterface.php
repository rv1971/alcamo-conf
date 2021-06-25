<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a find() method to find a file by its name
 *
 * @date Last reviewed 2021-06-15
 */
interface FileFinderInterface
{
    /// Find a file by its name
    public function find(string $filename): ?string;

    /// Serialize this object, mainly for debugging
    public function __toString();
}
