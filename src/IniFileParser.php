<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Parser for INI files
 *
 * @date Last reviewed 2025-12-22
 */
class IniFileParser implements FileParserInterface
{
    /// Default whether to process sections
    public const DEFAULT_PROCESS_SECTIONS = false;

    /// Default mode
    public const DEFAULT_INI_SCANNER_MODE = INI_SCANNER_TYPED;

    private $processSections_; ///< bool
    private $iniScannerMode_; ///< int

    /**
     * @param $processSections Whether to process sections in
     * parse_ini_file(). Defaults to
     * alcamo::conf::IniFileParser::DEFAULT_PROCESS_SECTIONS.
     *
     * @param $iniScannerMode Scanner mode used in parse_ini_file(). Defaults
     * to alcamo::conf::IniFileParser::DEFAULT_INI_SCANNER_MODE.
     */
    public function __construct(
        ?bool $processSections = null,
        ?int $iniScannerMode = null
    ) {
        $this->processSections_ =
            $processSections ?? static::DEFAULT_PROCESS_SECTIONS;

        $this->iniScannerMode_ =
            $iniScannerMode ?? static::DEFAULT_INI_SCANNER_MODE;
    }

    /**
     * @copybrief alcamo::conf::FileParserInterface::parse()
     *
     * Use
     * [parse_ini_file()](https://www.php.net/manual/en/function.parse-ini-file)
     * to parse the file.
     */
    public function parse(string $filename)
    {
        try {
            return parse_ini_file(
                $filename,
                $this->processSections_,
                $this->iniScannerMode_
            );
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if parser fails. */
            throw
                FileNotFound::newFromPrevious($e, [ 'filename' => $filename ]);
        }
    }
}
