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
     * @copydoc alcamo::conf::FileParserInterface::parse()
     *
     * Use
     * [parse_ini_file()](https://www.php.net/manual/en/function.parse-ini-file)
     * to parse the file. The flag LoaderInterface::AS_OBJECT is supported.
     */
    public function parse(string $path, ?int $flags = null)
    {
        try {
            $data = parse_ini_file(
                $path,
                $this->processSections_,
                $this->iniScannerMode_
            );

            if ($flags & LoaderInterface::AS_OBJECT) {
                $data = $this->processSections_
                    ? json_decode(json_encode($data))
                    : (object)$data;
            }

            return $data;
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if parser fails. */
            throw
                FileNotFound::newFromPrevious($e, [ 'filename' => $path ]);
        }
    }
}
