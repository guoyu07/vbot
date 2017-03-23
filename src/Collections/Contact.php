<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Collections;

use Illuminate\Support\Collection;

class Contact extends Collection
{
    /**
     * @var Contact
     */
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Contact
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 根据微信号获取联系人.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getContactById($id)
    {
        return $this->filter(function ($item, $key) use ($id) {
            if ($item['Alias'] === $id) {
                return true;
            }
        })->first();
    }

    /**
     * 根据微信号获取联系username.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getUsernameById($id)
    {
        return $this->search(function ($item, $key) use ($id) {
            if ($item['Alias'] === $id) {
                return true;
            }
        });
    }

    /**
     * 根据通讯录中的备注获取通讯对象
     *
     * @param $id
     *
     * @return mixed
     */
    public function getUsernameByRemarkName($id)
    {
        return $this->search(function ($item, $key) use ($id) {
            if ($item['RemarkName'] === $id) {
                return true;
            }
        });
    }

    /**
     * 根据通讯录中的昵称获取通讯对象
     *
     * @param $nickname
     * @param bool $blur
     *
     * @return mixed
     */
    public function getUsernameByNickname($nickname, $blur = false)
    {
        return $this->search(function ($item, $key) use ($nickname, $blur) {
            if ($blur && str_contains($item['NickName'], $nickname)) {
                return true;
            } elseif (!$blur && $item['NickName'] === $nickname) {
                return true;
            }
        });
    }

    public function setRemarkName($username, $remarkName)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', server()->baseUri, server()->passTicket);

        $result = http()->post($url, json_encode([
            'UserName'    => $username,
            'CmdId'       => 2,
            'RemarkName'  => $remarkName,
            'BaseRequest' => server()->baseRequest,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    public function setStick($username, $isStick = true)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', server()->baseUri, server()->passTicket);

        $result = http()->json($url, [
            'UserName'    => $username,
            'CmdId'       => 3,
            'OP'          => (int) $isStick,
            'BaseRequest' => server()->baseRequest,
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }
}
