<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\FileManager;

class Emoticon extends Message implements MediaInterface, MessageInterface
{
    use UploadAble, MediaTrait;

    public static $folder = 'gif';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if (!$response) {
            Console::log("表情 {$file} 上传失败", Console::WARNING);

            return false;
        }

        $mediaId = $response['MediaId'];

        $url  = sprintf(server()->baseUri . '/webwxsendemoticon?fun=sys&f=json&pass_ticket=%s', server()->passTicket);
        $data = [
            'BaseRequest' => server()->baseRequest,
            'Msg'         => [
                'Type'         => 47,
                'EmojiFlag'    => 2,
                'MediaId'      => $mediaId,
                'FromUserName' => myself()->username,
                'ToUserName'   => $username,
                'LocalID'      => time() * 1e4,
                'ClientMsgId'  => time() * 1e4,
            ],
        ];
        $result = http()->json($url, $data, true);

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('发送表情失败', Console::WARNING);

            return false;
        }

        return true;
    }

    /**
     * 根据MsgID发送文件.
     *
     * @param $username
     * @param $msgId
     *
     * @return mixed
     */
    public static function sendByMsgId($username, $msgId)
    {
        $path = static::getPath(static::$folder);

        static::send($username, $path . "/{$msgId}.gif");
    }

    /**
     * 从当前账号的本地表情库随机发送一个.
     *
     * @param $username
     */
    public static function sendRandom($username)
    {
        $path = static::getPath(static::$folder);

        $files = scandir($path);
        unset($files[0], $files[1]);
        $msgId = $files[array_rand($files)];

        static::send($username, $path . '/' . $msgId);
    }

    /**
     * 下载文件.
     *
     * @return mixed
     */
    public function download()
    {
        $url     = server()->baseUri . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->msg['MsgId'], server()->skey);
        $content = http()->get($url);
        FileManager::download($this->msg['MsgId'] . '.gif', $content, static::$folder);
    }

    public function make()
    {
        $this->download();
    }
}
