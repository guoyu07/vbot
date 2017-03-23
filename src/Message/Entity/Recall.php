<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;

class Recall extends Message implements MessageInterface
{
    use MediaTrait;

    /**
     * @var Message 上一条撤回的消息
     */
    public $origin;

    /**
     * @var string 撤回者昵称
     */
    public $nickname;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $msgId = $this->parseMsgId($this->msg['Content']);

        /* @var Message $message */
        $this->origin = message()->get($msgId, null);

        if ($this->origin) {
            $this->nickname = $this->origin->sender ? $this->origin->sender['NickName'] : account()->getAccount($this->origin->msg['FromUserName'])['NickName'];
            $this->setContent();
        }
    }

    /**
     * 解析message获取msgId.
     *
     * @param $xml
     *
     * @return string msgId
     */
    private function parseMsgId($xml)
    {
        preg_match('/<msgid>(\d+)<\/msgid>/', $xml, $matches);

        return $matches[1];
    }

    private function setContent()
    {
        $this->content = "{$this->nickname} 刚撤回了消息";
    }
}
