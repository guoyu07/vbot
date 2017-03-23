<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Support;

/**
 * Content 处理类.
 *
 * Class Content
 */
class Content
{
    /**
     * 格式化Content.
     *
     * @param $content
     *
     * @return string
     */
    public static function formatContent($content)
    {
        return self::htmlDecode(self::replaceBr($content));
    }

    public static function htmlDecode($content)
    {
        return html_entity_decode($content);
    }

    public static function replaceBr($content)
    {
        return str_replace('<br/>', "\n", $content);
    }
}
