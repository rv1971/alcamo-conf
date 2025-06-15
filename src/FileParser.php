<?php

namespace alcamo\conf;

use alcamo\exception\InvalidEnumerator;

/**
 * @brief Parser for INI or JSON files
 */
class FileParser implements FileParserInterface
{
    public const EXT_TO_PARSER_CLASS = [
        'ini'  => IniFileParser::class,
        'json' => JsonFileParser::class
    ];

    public function createParser(string $filename): FileParserInterface
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (isset(static::EXT_TO_PARSER_CLASS[$extension])) {
            $class = static::EXT_TO_PARSER_CLASS[$extension];
            return new $class();
        }

        $extensions = array_keys(static::EXT_TO_PARSER_CLASS);

        /** @throw alcamo::exception::InvalidEnumerator if the file extension
         *  is not known. */
        throw new InvalidEnumerator(
            'Invalid file extension',
            0,
            null,
            [
                'value' => $extension,
                'expectedOneOf' => $extensions,
                'atUri' => $filename
            ]
        );
    }

    /**
     * @copybrief FileParserInterface::parse()
     *
     * Use a parser object depending on the file suffix.
     */
    public function parse(string $filename): array
    {
        return $this->createParser($filename)->parse($filename);
    }
}
