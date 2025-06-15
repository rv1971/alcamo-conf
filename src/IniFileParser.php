<?php

namespace alcamo\conf;

use alcamo\exception\FileNotFound;

/**
 * @brief Parser for INI files
 */
class IniFileParser implements FileParserInterface
{
    public const PROCESS_SECTIONS = false;

    private $processSections_; ///< bool

    public function __construct(?bool $processSections = null)
    {
        $this->processSections_ = $processSections ?? static::PROCESS_SECTIONS;
    }

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
            return parse_ini_file(
                $filename,
                $this->processSections_,
                INI_SCANNER_TYPED
            );
        } catch (\Throwable $e) {
            /** @throw alcamo::exception::FileNotFound if parser fails. */
            throw (new FileNotFound())
                ->setMessageContext([ 'filename' => $filename ]);
        }
    }
}
