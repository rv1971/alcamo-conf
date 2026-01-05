<?php

namespace alcamo\conf;

use alcamo\exception\{DataValidationFailed, FileNotFound};

/**
 * @brief Parser for JSON files
 *
 * @date Last reviewed 2025-12-22
 */
class JsonFileParser implements FileParserInterface
{
    /// @copybrief alcamo::conf::FileParserInterface::parse()
    public function parse(string $filename)
    {
        try {
            $contents = file_get_contents($filename);
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if file cannot be
             *  loaded from storage. */
            throw (new FileNotFound())
                ->setMessageContext([ 'filename' => $filename ]);
        }

        $json = json_decode($contents, true);

        if (!isset($json)) {
            throw (new DataValidationFailed())
                ->setMessageContext(
                    [
                        'filename' => $filename,
                        'extraMessage' => 'no valid JSON data'
                    ]
                );
        }

        return $json;
    }
}
