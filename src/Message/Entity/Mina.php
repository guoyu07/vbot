<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Mina extends Message implements MessageInterface
{
    public $title;

    public $url;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->msg['Content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
        $this->url   = $info['url'];
    }
}
