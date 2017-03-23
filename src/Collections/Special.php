<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Collections;

use Illuminate\Support\Collection;

class Special extends Collection
{
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Special
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }
}
