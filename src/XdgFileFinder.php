<?php

namespace alcamo\conf;

use XdgBaseDir\Xdg;
use alcamo\exception\InvalidEnumerator;

/**
 * @brief Find a file as explained by the XDG Base Directory Specification.
 *
 * @sa [XDG Base Directory Specification](https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html)
 *
 * @date Last reviewed 2021-06-15
 */

class XdgFileFinder extends \XdgBaseDir\Xdg implements FileFinderInterface
{
    /// Default subdirectory within `$XDG_CONFIG_HOME` or `$XDG_DATA_HOME`
    public const SUBDIR = 'alcamo';

    private $subdir_; ///< string
    private $type_;   ///< string
    private $dirs_;   ///< array

    /**
     * @param $subdir subdirectory within `$XDG_CONFIG_HOME` or
     * `$XDG_DATA_HOME`, defaults to @ref SUBDIR.
     *
     * @param string $type `CONFIG` or `DATA`, defaults to `CONFIG`
     */
    public function __construct(?string $subdir = null, ?string $type = null)
    {
        $this->subdir_ = $subdir ?? static::SUBDIR;

        $this->type_ = $type ?? 'CONFIG';

        switch ($this->type_) {
            case 'CONFIG':
                $this->dirs_ = $this->getConfigDirs();
                break;

            case 'DATA':
                $this->dirs_ = $this->getDataDirs();
                break;

            default:
                /** @throw alcamo::exception::InvalidEnumerator if `$type` is
                 *  invalid. */
                throw new InvalidEnumerator($type, [ 'CONFIG', 'DATA' ]);
        }
    }

    /// Get subdirectory within `$XDG_CONFIG_HOME` or `$XDG_DATA_HOME`
    public function getSubdir(): string
    {
        return $this->subdir_;
    }

    /// Get type, either `CONFIG` or `DATA`
    public function getType(): string
    {
        return $this->type_;
    }

    /// Get `$XDG_CONFIG_DIRS`/subdir or `$XDG_DATA_DIRS`/subdir
    public function getDirs(): array
    {
        return $this->dirs_;
    }

    /**
     * @brief Return colon-separated list of result of getDirs()
     */
    public function __toString()
    {
        return implode(':', $this->dirs_);
    }

    /**
     * @copybrief FileFinderInterface::find()
     *
     * Find a file by searching through the directories returned by getDirs().
     */
    public function find(string $filename): ?string
    {
        foreach ($this->dirs_ as $dir) {
            $pathname = $dir . DIRECTORY_SEPARATOR
                . $this->subdir_ . DIRECTORY_SEPARATOR
                . $filename;

            if (is_readable($pathname)) {
                return $pathname;
            }
        }

        return null;
    }
}
