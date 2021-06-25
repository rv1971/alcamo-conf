<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Parser for JSON files
 *
 * @date Last reviewed 2021-06-15
 */
class JsonFileParser implements FileParserInterface
{
    /// @copybrief FileParserInterface::parse()
    public function parse(string $filename): array
    {
        try {
            $contents = file_get_contents($filename);
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if file cannot be
             *  loaded from storage. */
            throw new FileNotFound($filename);
        }

        return json_decode($contents, true);
    }
}
