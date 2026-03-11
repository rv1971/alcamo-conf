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
    public function parse(string $path, ?int $flags = null)
    {
        try {
            $contents = file_get_contents($path);
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if file cannot be
             *  loaded from storage. */
            throw (new FileNotFound())
                ->setMessageContext([ 'filename' => $path ]);
        }

        $json = json_decode($contents, !($flags & LoaderInterface::AS_OBJECT));

        if (!isset($json)) {
            throw (new DataValidationFailed())
                ->setMessageContext(
                    [
                        'filename' => $path,
                        'extraMessage' => 'no valid JSON data'
                    ]
                );
        }

        return $json;
    }
}
