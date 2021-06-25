<?php

namespace alcamo\conf;

/**
 * @brief Object containing a configuration object
 *
 * @date Last reviewed 2021-06-15
 */
trait HasConfTrait
{
    private $conf_;

    public function getConf()
    {
        return $this->conf_;
    }
}
