<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Location extends Message implements MessageInterface
{
    /**
     * @var string 位置链接
     */
    public $url;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    /**
     * 判断是否位置消息.
     *
     * @param $content
     *
     * @return bool
     */
    public static function isLocation($content)
    {
        return str_contains($content['Content'], 'webwxgetpubliclinkimg') && $content['Url'];
    }

    public function make()
    {
        $this->setLocationText();
    }

    /**
     * 设置位置文字信息.
     */
    private function setLocationText()
    {
        $this->content = current(explode(":\n", $this->msg['Content']));

        $this->url = $this->msg['Url'];
    }
}
