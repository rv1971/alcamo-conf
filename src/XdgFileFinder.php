<?php

namespace alcamo\conf;

use XdgBaseDir\Xdg;
use alcamo\exception\InvalidEnumerator;

/**
 * @brief Find a file as explained by the XDG Base Directory Specification
 *
 * @sa [XDG Base Directory Specification](https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html)
 */
class XdgFileFinder extends \XdgBaseDir\Xdg implements FileFinderInterface
{
    /**
     * @brief Default subdirectory
     *
     * Subdirectory within `$XDG_CONFIG_HOME`, `$XDG_DATA_HOME`,
     * `$XDG_STATE_HOME` or `$XDG_CACHE_HOME`
     */
    public const SUBDIR = 'alcamo';

    private $subdir_; ///< string
    private $type_;   ///< string
    private $dirs_;   ///< array

    /**
     * @param $subdir subdirectory within `$XDG_CONFIG_HOME`,
     * `$XDG_DATA_HOME`, `$XDG_STATE_HOME` or `$XDG_CACHE_HOME`, defaults
     * to @ref alcamo::conf::XdgFileFinder::SUBDIR.
     *
     * @param string $type `CONFIG`, `DATA`, `STATE` or `CACHE`, defaults to
     * `CONFIG`
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

            case 'STATE':
                $this->dirs_ = [ $this->getHomeStateDir() ];
                break;

            case 'CACHE':
                $this->dirs_ = [ $this->getHomeCacheDir() ];
                break;

            default:
                /** @throw alcamo::exception::InvalidEnumerator if `$type` is
                 *  invalid. */
                throw (new InvalidEnumerator())->setMessageContext(
                    [
                        'value' => $type,
                        'expectedOneOf' =>
                            [ 'CONFIG', 'DATA', 'STATE', 'CACHE' ]
                    ]
                );
        }
    }

    /// Get $XDG_STATE_HOME
    public function getHomeStateDir(): string
    {
        return getenv('XDG_STATE_HOME') ?:
            $this->getHomeDir() . DIRECTORY_SEPARATOR . '.local'
            . DIRECTORY_SEPARATOR . 'state';
    }

    /// Get $XDG_CACHE_HOME
    public function getHomeCacheDir(): string
    {
        return getenv('XDG_CACHE_HOME') ?:
            $this->getHomeDir() . DIRECTORY_SEPARATOR . '.cache';
    }

    /**
     * @brief Get subdirectory within $XDG_CONFIG_HOME, $XDG_DATA_HOME,
     * $XDG_STATE_HOME or $XDG_CACHE_HOME
     */
    public function getSubdir(): string
    {
        return $this->subdir_;
    }

    /// Get type, either CONFIG, DATA, STATE or CACHE
    public function getType(): string
    {
        return $this->type_;
    }

    /// Get $XDG_CONFIG_DIRS, $XDG_DATA_DIRS, $XDG_STATE_HOME or $XDG_CACHE_HOME
    public function getDirs(): array
    {
        return $this->dirs_;
    }

    /**
     * @brief Return colon-separated list of result of getDirs()
     */
    public function __toString(): string
    {
        return implode(':', $this->dirs_);
    }

    /**
     * @copybrief FileFinderInterface::find()
     *
     * Find a file by searching through the subdirectories returned by
     * getSubdir() in the directories returned by getDirs().
     */
    public function find(string $filename): ?string
    {
        foreach ($this->dirs_ as $dir) {
            $directory = $dir . DIRECTORY_SEPARATOR . $this->subdir_;
            $pathname = $directory . DIRECTORY_SEPARATOR . $filename;

            switch ($this->type_) {
                /** If a *state* or *cache* directory does not exist, create
                 *  it and return the file name. */
                case 'STATE':
                case 'CACHE':
                    if (!is_dir($directory)) {
                        /* If this fails, it will trigger an error which must
                         * be handled appropriately by the caller. */
                        mkdir($directory, 0777, true);
                    }

                    return $pathname;

                default:
                    if (is_readable($pathname)) {
                        return $pathname;
                    }
            }
        }

        return null;
    }
}
