<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\GroupChange;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Location;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Mina;
use Hanson\Vbot\Message\Entity\Official;
use Hanson\Vbot\Message\Entity\Recall;
use Hanson\Vbot\Message\Entity\Recommend;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Message\Entity\RequestFriend;
use Hanson\Vbot\Message\Entity\Share;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Touch;
use Hanson\Vbot\Message\Entity\Transfer;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Message\Entity\Voice;

$path  = __DIR__ . '/./../tmp/';
$robot = new Vbot([
    'tmp'   => $path,
    'debug' => true,
]);

// 图灵自动回复
function reply($str)
{
    return http()->post('http://www.tuling123.com/openapi/api', [
        'key'  => '1dce02aef026258eff69635a06b0ab7d',
        'info' => $str,
    ], true)['text'];
}

$robot->server->setMessageHandler(function ($message) use ($path) {
    /** @var $message Message */

    // 位置信息 返回位置文字
    if ($message instanceof Location) {
        /* @var $message Location */
        Text::send('地图链接：' . $message->from['UserName'], $message->url);

        return '位置：' . $message;
    }

    // 文字信息
    if ($message instanceof Text) {
        /** @var $message Text */
        // 联系人自动回复
        if ($message->fromType === 'Contact') {
            return reply($message->content);
            // 群组@我回复
        } elseif ($message->fromType === 'Group') {
            if (str_contains($message->content, '设置群名称') && $message->from['Alias'] === 'hanson1994') {
                group()->setGroupName($message->from['UserName'], str_replace('设置群名称', '', $message->content));
            }

            if ($message->isAt) {
                return reply($message->content);
            }
        }
    }

    // 图片信息 返回接收到的图片
    if ($message instanceof Image) {
        //        return $message;
    }

    // 视频信息 返回接收到的视频
    if ($message instanceof Video) {
        //        return $message;
    }

    // 表情信息 返回接收到的表情
    if ($message instanceof Emoticon) {
        Emoticon::sendRandom($message->from['UserName']);
    }

    // 语音消息
    if ($message instanceof Voice) {
        /* @var $message Voice */
//        return '收到一条语音并下载在' . $message::getPath($message::$folder) . "/{$message->msg['MsgId']}.mp3";
    }

    // 撤回信息
    if ($message instanceof Recall && $message->msg['FromUserName'] !== myself()->username) {
        /** @var $message Recall */
        if ($message->origin instanceof Image) {
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一张照片");
            Image::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        } elseif ($message->origin instanceof Emoticon) {
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个表情");
            Emoticon::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        } elseif ($message->origin instanceof Video) {
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个视频");
            Video::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        } elseif ($message->origin instanceof Voice) {
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条语音");
        } else {
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条信息 \"{$message->origin->msg['Content']}\"");
        }
    }

    // 红包信息
    if ($message instanceof RedPacket) {
        // do something to notify if you want ...
        return $message->content . ' 来自 ' . $message->from['NickName'];
    }

    // 转账信息
    if ($message instanceof Transfer) {
        /* @var $message Transfer */
        return $message->content . ' 收到金额 ' . $message->fee;
    }

    // 推荐名片信息
    if ($message instanceof Recommend) {
        /** @var $message Recommend */
        if ($message->isOfficial) {
            return $message->from['NickName'] . ' 向你推荐了公众号 ' . $message->province . $message->city .
            " {$message->info['NickName']} 公众号信息： {$message->description}";
        }

        return $message->from['NickName'] . ' 向你推荐了 ' . $message->province . $message->city .
            " {$message->info['NickName']} 头像链接： {$message->bigAvatar}";
    }

    // 请求添加信息
    if ($message instanceof RequestFriend) {
        /** @var $message RequestFriend */
        $groupUsername = group()->getGroupsByNickname('芬芬', true)->first()['UserName'];

        Text::send($groupUsername, "{$message->info['NickName']} 请求添加好友 \"{$message->info['Content']}\"");

        if ($message->info['Content'] === '上山打老虎') {
            Text::send($groupUsername, '暗号正确');
            $message->verifyUser($message::VIA);
        } else {
            Text::send($groupUsername, '暗号错误');
        }
    }

    // 分享信息
    if ($message instanceof Share) {
        /** @var $message Share */
        $reply = "收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
        if ($message->app) {
            $reply .= "\n来源APP：{$message->app}";
        }

        return $reply;
    }

    // 分享小程序信息
    if ($message instanceof Mina) {
        /** @var $message Mina */
        $reply = "收到小程序\n小程序名词：{$message->title}\n链接：{$message->url}";

        return $reply;
    }

    // 公众号推送信息
    if ($message instanceof Official) {
        /** @var $message Official */
        $reply = "收到公众号推送\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}\n来源公众号名称：{$message->app}";

        return $reply;
    }

    // 手机点击聊天事件
    if ($message instanceof Touch) {
        //        Text::send($message->msg['ToUserName'], "我点击了此聊天");
    }

    // 新增好友
    if ($message instanceof \Hanson\Vbot\Message\Entity\NewFriend) {
        \Hanson\Vbot\Support\Console::log('新加好友：' . $message->from['NickName']);
    }

    // 群组变动
    if ($message instanceof GroupChange) {
        /** @var $message GroupChange */
        if ($message->action === 'ADD') {
            \Hanson\Vbot\Support\Console::log('新人进群');

            return '欢迎新人 ' . $message->nickname;
        } elseif ($message->action === 'REMOVE') {
            \Hanson\Vbot\Support\Console::log('群主踢人了');

            return $message->content;
        } elseif ($message->action === 'RENAME') {
            //            \Hanson\Vbot\Support\Console::log($message->from['NickName'] . ' 改名为 ' . $message->rename);
            if ($message->rename !== 'vbot 测试群') {
                group()->setGroupName($message->from['UserName'], 'vbot 测试群');

                return '行不改名,坐不改姓！';
            }
        }
    }

    return false;
});

$robot->server->setExitHandler(function () {
    \Hanson\Vbot\Support\Console::log('其他设备登录');
});

$robot->server->setExceptionHandler(function () {
    \Hanson\Vbot\Support\Console::log('异常退出');
});

$robot->server->run();
