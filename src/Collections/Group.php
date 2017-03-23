<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Collections;

use Hanson\Vbot\Support\Console;
use Illuminate\Support\Collection;

class Group extends Collection
{
    public static $instance = null;

    /**
     * username => id.
     *
     * @var array
     */
    public $map = [];

    /**
     * create a single instance.
     *
     * @return Group
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 判断是否群组.
     *
     * @param $userName
     *
     * @return bool
     */
    public static function isGroup($userName)
    {
        return strstr($userName, '@@') !== false;
    }

    /**
     * 根据群名筛选群组.
     *
     * @param $name
     * @param bool $blur
     *
     * @return static
     */
    public function getGroupsByNickname($name, $blur = false)
    {
        $groups = $this->filter(function ($value, $key) use ($name, $blur) {
            if (!$blur) {
                return $value['NickName'] === $name;
            }

            return str_contains($value['NickName'], $name);
        });

        return $groups;
    }

    /**
     * 根据通讯录中的昵称获取通讯对象
     *
     * @param $nickname
     *
     * @return mixed
     */
    public function getUsernameByNickname($nickname)
    {
        return $this->search(function ($item, $key) use ($nickname) {
            if ($item['NickName'] === $nickname) {
                return true;
            }
        });
    }

    /**
     * 根据昵称搜索群成员.
     *
     * @param $groupUsername
     * @param $memberNickname
     * @param bool $blur
     *
     * @return array
     */
    public function getMembersByNickname($groupUsername, $memberNickname, $blur = false)
    {
        $members = $this->get($groupUsername);

        $result = [];

        foreach ($members['MemberList'] as $member) {
            if ($blur && str_contains($member['NickName'], $memberNickname)) {
                $result[] = $member;
            } elseif (!$blur && $member['NickName'] === $memberNickname) {
                $result[] = $member;
            }
        }

        return $result;
    }

    /**
     * 根据ID获取群username.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getUsernameById($id)
    {
        return array_search($id, $this->map, true);
    }

    /**
     * 设置map.
     *
     * @param $username
     * @param $id
     */
    public function setMap($username, $id)
    {
        $this->map[$username] = $id;
    }

    /**
     * 创建群聊天.
     *
     * @param array $contacts
     *
     * @return bool
     */
    public function create(array $contacts)
    {
        $url = sprintf('%s/webwxcreatechatroom?lang=zh_CN&r=%s', server()->baseUri, time());

        $result = http()->json($url, [
            'MemberCount' => count($contacts),
            'MemberList'  => $this->makeMemberList($contacts),
            'Topic'       => '',
            'BaseRequest' => server()->baseRequest,
        ], true);

        if ($result['BaseResponse']['Ret'] != 0) {
            return false;
        }

        return $this->add($result['ChatRoomName']);
    }

    /**
     * 删除群成员.
     *
     * @param $group
     * @param $members
     *
     * @return bool
     */
    public function deleteMember($group, $members)
    {
        $members = is_string($members) ? [$members] : $members;
        $result  = http()->json(sprintf('%s/webwxupdatechatroom?fun=delmember&pass_ticket=%s', server()->baseUri, server()->passTicket), [
            'BaseRequest'   => server()->baseRequest,
            'ChatRoomName'  => $group,
            'DelMemberList' => implode(',', $members),
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 添加群成员.
     *
     * @param $groupUsername
     * @param $members
     *
     * @return bool
     */
    public function addMember($groupUsername, $members)
    {
        if (!$groupUsername) {
            return false;
        }
        $group = group()->get($groupUsername);

        if (!$group) {
            return false;
        }

        $groupCount      = count($group['MemberList']);
        list($fun, $key) = $groupCount > 40 ? ['invitemember', 'InviteMemberList'] : ['addmember', 'AddMemberList'];
        $members         = is_string($members) ? [$members] : $members;

        $result = http()->json(sprintf('%s/webwxupdatechatroom?fun=%s&pass_ticket=%s', server()->baseUri, $fun, server()->passTicket), [
            'BaseRequest'  => server()->baseRequest,
            'ChatRoomName' => $groupUsername,
            $key           => implode(',', $members),
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 设置群名称.
     *
     * @param $group
     * @param $name
     *
     * @return bool
     */
    public function setGroupName($group, $name)
    {
        $result = http()->post(sprintf('%s/webwxupdatechatroom?fun=modtopic&pass_ticket=%s', server()->baseUri, server()->passTicket),
            json_encode([
            'BaseRequest'  => server()->baseRequest,
            'ChatRoomName' => $group,
            'NewTopic'     => $name,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 增加群聊天到group.
     *
     * @param $username
     *
     * @return bool
     */
    private function add($username)
    {
        $result = http()->json(sprintf('%s/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', server()->baseUri, time(), server()->passTicket), [
            'Count'       => 1,
            'BaseRequest' => server()->baseRequest,
            'List'        => [
                [
                    'ChatRoomId' => '',
                    'UserName'   => $username,
                ],
            ],
        ], true);

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('增加聊天群组失败 ' . $username, Console::WARNING);

            return false;
        }

        group()->put($username, $result['ContactList'][0]);

        return $result['ContactList'][0];
    }

    /**
     * 生成member list 格式.
     *
     * @param $contacts
     *
     * @return array
     */
    private function makeMemberList($contacts)
    {
        $memberList = [];

        foreach ($contacts as $contact) {
            $memberList[] = ['UserName' => $contact];
        }

        return $memberList;
    }
}
