<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a load() to load configuration files
 *
 * @date Last reviewed 2021-06-15
 */
interface LoaderInterface
{
    /// Load and merge configuration files and return an associative array
    public function load($filenames): array;
}
