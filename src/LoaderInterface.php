<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a load() method to load configuration files
 *
 * @date Last reviewed 2025-12-22
 */
interface LoaderInterface
{
    /// Load and merge configuration files and return an associative array
    public function load($filenames): array;
}
