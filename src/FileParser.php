<?php

namespace alcamo\conf;

use alcamo\exception\InvalidEnumerator;

/**
 * @brief Parser for INI or JSON files
 *
 * @date Last reviewed 2021-06-15
 */
class FileParser implements FileParserInterface
{
    private $ext2parser_; // Map of filename extensions to parser objects

    public function __construct()
    {
        $this->ext2parser_ = [
            'ini' => new IniFileParser(),
            'json' => new JsonFileParser()
        ];
    }

    /**
     * @copybrief FileParserInterface::parse()
     *
     * Use a parser object depending on the file suffix.
     */
    public function parse(string $filename): array
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (isset($this->ext2parser_[$extension])) {
            return $this->ext2parser_[$extension]->parse($filename);
        }

        $extensions = array_keys($this->ext2parser_);

        /** @throw alcamo::exception::InvalidEnumerator if the file extension
         *  is not known. */
        throw new InvalidEnumerator(
            $extension,
            $extensions,
            "Invalid file extension in '$filename', expected one of: '"
            . implode("', '", $extensions) . "'"
        );
    }
}
