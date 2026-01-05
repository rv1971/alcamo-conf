<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a load() method to load configuration files
 *
 * @date Last reviewed 2025-12-22
 */
interface LoaderInterface
{
    /// Load configuration files and return the parsing result
    public function load($filenames);
}
