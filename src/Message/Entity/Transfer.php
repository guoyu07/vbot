<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Transfer extends Message implements MessageInterface
{
    /**
     * 转账金额 单位 元.
     *
     * @var string
     */
    public $fee;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->msg['Content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $des = (array) $array['appmsg']->des;
        $fee = (array) $array['appmsg']->wcpayinfo;

        $this->content = current($des);

        $this->fee = substr($fee['feedesc'], 3);
    }
}
