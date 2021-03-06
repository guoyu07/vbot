<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class RequestFriend extends Message implements MessageInterface
{
    const ADD = 2;
    const VIA = 3;

    /**
     * @var array 信息
     */
    public $info;

    public $avatar;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $this->info = $this->msg['RecommendInfo'];
        $this->parseContent();
    }

    /**
     * 验证通过好友.
     *
     * @param $code
     * @param null $ticket
     *
     * @return bool
     */
    public function verifyUser($code, $ticket = null)
    {
        $url  = sprintf(server()->baseUri . '/webwxverifyuser?lang=zh_CN&r=%s&pass_ticket=%s', time() * 1000, server()->passTicket);
        $data = [
            'BaseRequest'        => server()->baseRequest,
            'Opcode'             => $code,
            'VerifyUserListSize' => 1,
            'VerifyUserList'     => [$ticket ?: $this->verifyTicket()],
            'VerifyContent'      => '',
            'SceneListCount'     => 1,
            'SceneList'          => [33],
            'skey'               => server()->skey,
        ];

        $result = http()->json($url, $data, true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 返回通过好友申请所需的数组.
     *
     * @return array
     */
    public function verifyTicket()
    {
        return [
            'Value'            => $this->info['UserName'],
            'VerifyUserTicket' => $this->info['Ticket'],
        ];
    }

    private function parseContent()
    {
        $isMatch = preg_match('/bigheadimgurl="(.+?)"/', $this->msg['Content'], $matches);

        if ($isMatch) {
            $this->avatar = $matches[1];
        }
    }
}
