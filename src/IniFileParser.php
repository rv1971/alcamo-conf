<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Parser for INI files
 *
 * @date Last reviewed 2021-06-15
 */
class IniFileParser implements FileParserInterface
{
    /**
     * @copybrief FileParserInterface::parse()
     *
     * Use
     * [parse_ini_file()](https://www.php.net/manual/en/function.parse-ini-file)
     * to parse the file.
     */
    public function parse(string $filename): array
    {
        try {
            return parse_ini_file($filename, false, INI_SCANNER_TYPED);
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if parser fails. */
            throw new FileNotFound($filename);
        }
    }
}
