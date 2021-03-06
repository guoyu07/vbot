<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Support\System;

/**
 * Class MediaTrait.
 *
 * @property string $folder
 */
trait MediaTrait
{
    public static function getPath($folder)
    {
        $path = System::getPath() . $folder;

        return realpath($path);
    }
}
