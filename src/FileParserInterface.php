<?php

namespace alcamo\conf;

/**
 * @brief Object implementing a parse() method to parse a configuration file
 *
 * @date Last reviewed 2025-12-22
 */
interface FileParserInterface
{
    /**
     * @brief Parse a configuration file and return the result
     *
     * @param $path Complete path to the file.
     *
     * @param $flags If $flags & alcamo::conf::LoaderInterface::AS_OBJECT,
     * return the file content as an object rather than an array, if the
     * parser supports both.
     */
    public function parse(string $path, ?int $flags = null);
}
