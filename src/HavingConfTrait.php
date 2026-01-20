<?php

namespace alcamo\conf;

/**
 * @brief Simple implementation of HavingConfInterface
 *
 * @date Last reviewed 2025-12-22
 */
trait HavingConfTrait
{
    private $conf_; ///< array

    public function getConf()
    {
        return $this->conf_;
    }
}
