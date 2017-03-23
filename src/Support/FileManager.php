<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Support;

class FileManager
{
    public static function download($name, $data, $path = '')
    {
        $path = System::getPath() . $path;
        if (!is_dir(realpath($path))) {
            mkdir($path, 0700, true);
        }

        file_put_contents("$path/$name", $data);
    }
}
