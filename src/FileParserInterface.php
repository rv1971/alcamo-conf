<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a parse() method to parse a configuration file
 *
 * @date Last reviewed 2025-12-22
 */
interface FileParserInterface
{
    /// Parse a configuration file and return an associative array
    public function parse(string $filename): array;
}
