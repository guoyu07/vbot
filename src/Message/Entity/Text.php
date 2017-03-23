<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Support\Console;

class Text extends Message implements MessageInterface
{
    public $isAt;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    /**
     * 发送消息.
     *
     * @param $word string|Text 消息内容
     * @param $username string 目标username
     *
     * @return bool
     */
    public static function send($username, $word)
    {
        if (!$word) {
            return false;
        }

        $word = is_string($word) ? $word : $word->content;

        $random = strval(time() * 1000) . '0' . strval(rand(100, 999));

        $data = [
            'BaseRequest' => server()->baseRequest,
            'Msg'         => [
                'Type'         => 1,
                'Content'      => $word,
                'FromUserName' => myself()->username,
                'ToUserName'   => $username,
                'LocalID'      => $random,
                'ClientMsgId'  => $random,
            ],
            'Scene' => 0,
        ];
        $result = http()->post(server()->baseUri . '/webwxsendmsg?pass_ticket=' . server()->passTicket,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true
        );

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('发送消息失败', Console::WARNING);

            return false;
        }

        return true;
    }

    public function make()
    {
        $this->content = $this->msg['Content'];

        $this->isAt = str_contains($this->content, '@' . myself()->nickname);
    }
}
