<?php

namespace alcamo\conf;

use XdgBaseDir\Xdg;
use alcamo\exception\InvalidEnumerator;

/**
 * @brief Find a file as explained by the XDG Base Directory Specification
 *
 * @sa [XDG Base Directory Specification](https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html)
 *
 * @date Last reviewed 2025-12-22
 */
class XdgFileFinder extends \XdgBaseDir\Xdg implements FileFinderInterface
{
    /**
     * @brief Default subdirectory
     *
     * Subdirectory within `$XDG_CONFIG_HOME`, `$XDG_DATA_HOME`,
     * `$XDG_STATE_HOME`, `$XDG_CACHE_HOME` or `$XDG_RUNTIME_DIR`
     */
    public const SUBDIR = 'alcamo';

    /// Default mode for directories to create
    public const DEFAULT_DIR_MODE = 0777;

    private $subdir_; ///< string
    private $type_;   ///< string
    private $dirs_;   ///< array

    /**
     * @param $subdir subdirectory within `$XDG_CONFIG_HOME`,
     * `$XDG_DATA_HOME`, `$XDG_STATE_HOME`, `$XDG_CACHE_HOME` or
     * `$XDG_RUNTIME_DIR`, defaults to @ref
     * alcamo::conf::XdgFileFinder::SUBDIR.
     *
     * @param string $type `CONFIG`, `DATA`, `STATE`, `CACHE` or `RUNTIME`,
     * defaults to `CONFIG`
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

            case 'RUNTIME':
                $this->dirs_ = [ $this->getRuntimeDir() ];
                break;

            default:
                /** @throw alcamo::exception::InvalidEnumerator if `$type` is
                 *  invalid. */
                throw (new InvalidEnumerator())->setMessageContext(
                    [
                        'value' => $type,
                        'expectedOneOf' =>
                            [ 'CONFIG', 'DATA', 'STATE', 'CACHE', 'RUNTIME' ]
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

    /**
     * @brief Get subdirectory within $XDG_CONFIG_HOME, $XDG_DATA_HOME,
     * $XDG_STATE_HOME, $XDG_CACHE_HOME or $XDG_RUNTIME_DIR
     */
    public function getSubdir(): string
    {
        return $this->subdir_;
    }

    /// Get type, either CONFIG, DATA, STATE, CACHE or RUNTIME
    public function getType(): string
    {
        return $this->type_;
    }

    /**
     * @brief Get $XDG_CONFIG_DIRS, $XDG_DATA_DIRS, $XDG_STATE_HOME,
     * $XDG_CACHE_HOME or $XDG_RUNTIME_DIR
     */
    public function getDirs(): array
    {
        return $this->dirs_;
    }

    /// Get default directory including subdirectory
    public function getDefaultDir(): string
    {
        switch ($this->type_) {
            case 'CONFIG':
                $dir = $this->getHomeConfigDir();
                break;

            case 'DATA':
                $dir = $this->getHomeDataDir();
                break;

            case 'STATE':
                $dir = $this->getHomeStateDir();
                break;

            case 'CACHE':
                $dir = $this->getHomeCacheDir();
                break;

            case 'RUNTIME':
                $dir = $this->getRuntimeDir();
                break;
        }

        return $dir . DIRECTORY_SEPARATOR . $this->subdir_;
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
                /** If a *state*, *cache* or *runtime* directory does not
                 *  exist, create it and return the file name. */
                case 'STATE':
                case 'CACHE':
                    if (!is_dir($directory)) {
                        /* If this fails, it will trigger an error which must
                         * be handled appropriately by the caller. */
                        mkdir($directory, static::DEFAULT_DIR_MODE, true);
                    }

                    return $pathname;

                case 'RUNTIME':
                    if (!is_dir($directory)) {
                        /* If this fails, it will trigger an error which must
                         * be handled appropriately by the caller. */
                        mkdir($directory, 0700, true);
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
