<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Parser for JSON files
 *
 * @date Last reviewed 2025-12-22
 */
class JsonFileParser implements FileParserInterface
{
    /// @copybrief alcamo::conf::FileParserInterface::parse()
    public function parse(string $filename): array
    {
        try {
            $contents = file_get_contents($filename);
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if file cannot be
             *  loaded from storage. */
            throw (new FileNotFound())
                ->setMessageContext([ 'filename' => $filename ]);
        }

        return json_decode($contents, true);
    }
}
