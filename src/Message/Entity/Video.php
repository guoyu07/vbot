<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\FileManager;

class Video extends Message implements MessageInterface, MediaInterface
{
    use UploadAble, MediaTrait;

    public static $folder = 'mp4';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if (!$response) {
            Console::log("视频 {$file} 上传失败", Console::WARNING);

            return false;
        }

        $mediaId = $response['MediaId'];

        $url  = sprintf(server()->baseUri . '/webwxsendvideomsg?fun=async&f=json&pass_ticket=%s', server()->passTicket);
        $data = [
            'BaseRequest' => server()->baseRequest,
            'Msg'         => [
                'Type'         => 43,
                'MediaId'      => $mediaId,
                'FromUserName' => myself()->username,
                'ToUserName'   => $username,
                'LocalID'      => time() * 1e4,
                'ClientMsgId'  => time() * 1e4,
            ],
        ];
        $result = http()->json($url, $data, true);

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('发送视频失败', Console::WARNING);

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

        static::send($username, $path . "/{$msgId}.mp4");
    }

    /**
     * 下载文件.
     *
     * @return mixed
     */
    public function download()
    {
        $url     = server()->baseUri . sprintf('/webwxgetvideo?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
        $content = http()->request($url, 'get', [
            'headers' => [
                'Range' => 'bytes=0-',
            ],
        ]);
        if (strlen($content) === 0) {
            Console::log('下载视频失败', Console::WARNING);
            Console::log('url:' . $url);
        } else {
            FileManager::download($this->msg['MsgId'] . '.mp4', $content, static::$folder);
        }
    }

    public function make()
    {
        $this->download();
    }
}
