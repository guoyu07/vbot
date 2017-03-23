<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Support;

class System
{
    /**
     * 判断运行服务器是否windows.
     *
     * @return bool
     */
    public static function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function getPath()
    {
        $path = server()->config['tmp'] . '/' . myself()->alias . '/';

        if (!is_dir(realpath($path))) {
            mkdir($path, 0700, true);
        }

        return $path;
    }
}
